<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Add this line
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Artisan;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;


class UsersController extends Controller {

	use ValidatesRequests;


    public function list(Request $request)
    {
        $query = User::query();

        if (auth()->user()->hasRole('Employee')) {
            $query->role('Customer'); // Only users with 'Customer' role
        }

        $users = $query->get();

        return view('users.list', compact('users'));
    }


	public function register(Request $request) {
        return view('users.register');
    }


    public function doRegister(Request $request) {

        try{
            $this->validate($request, [
            'name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['required', 'regex:/^01[0-9]{9}$/', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            ]);
        } catch(\Exception $e) {
            return redirect()->back()->withInput($request->input())->withErrors('Invalid registration information.');
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);
        $user->save();

        // Assign default role "Customer"
        $customerRole = Role::firstOrCreate(['name' => 'Customer']);
        $user->assignRole($customerRole);

        $title = "Verification Link";
        $token = Crypt::encryptString(json_encode(['id' => $user->id, 'email' => $user->email]));
        $link = route("verify", ['token' => $token]);
        Mail::to($user->email)->send(new VerificationEmail($link, $user->name));

        return redirect('/');
    }

    public function login(Request $request) {
        return view('users.login');
    }

    public function doLogin(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return back()->withInput()->withErrors(['email' => 'Invalid login information.']);
    }

    // ❗ Block login if not verified
    if (is_null($user->email_verified_at)) {
        return back()->withInput()->withErrors(['email' => 'Your email is not verified.']);
    }

    // ✅ Log in the user first
    Auth::login($user);

    // 👇 Redirect to change password page if using temporary password
    if ($user->temp_password) {
        return redirect()->route('edit_password', $user->id)
                         ->with('info', 'You are using a temporary password. Please change it now.');
    }

    return redirect('/')->with('success', 'Login successful.');
}


    public function doLogout(Request $request) {
    	
    	Auth::logout();

        return redirect('/');
    }

    public function profile(Request $request, User $user = null) {

        $user = $user??auth()->user();
        if(auth()->id()!=$user->id) {
            if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
        }

        $permissions = [];
        foreach($user->permissions as $permission) {
            $permissions[] = $permission;
        }
        foreach($user->roles as $role) {
            foreach($role->permissions as $permission) {
                $permissions[] = $permission;
            }
        }

        return view('users.profile', compact('user', 'permissions'));
    }

    public function edit(Request $request, User $user = null) {
        if (!auth()->user()->hasRole(['Employee','Admin'])) {
            abort(403);
        }
   
        $user = $user??auth()->user();
        if(auth()->id()!=$user?->id) {
            if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
        }
    
        $roles = [];
        foreach(Role::all() as $role) {
            $role->taken = ($user->hasRole($role->name));
            $roles[] = $role;
        }

        $permissions = [];
        $directPermissionsIds = $user->permissions()->pluck('id')->toArray();
        foreach(Permission::all() as $permission) {
            $permission->taken = in_array($permission->id, $directPermissionsIds);
            $permissions[] = $permission;
        }      

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    public function save(Request $request, User $user) {

        if(auth()->id()!=$user->id) {
            if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
        }

        $user->name = $request->name;
        $user->save();

        if(auth()->user()->hasPermissionTo('admin_users')) {

            $user->syncRoles($request->roles);
            $user->syncPermissions($request->permissions);

            Artisan::call('cache:clear');
        }

        //$user->syncRoles([1]);
        //Artisan::call('cache:clear');

        return redirect(route('profile', ['user'=>$user->id]));
    }

    public function delete(Request $request, User $user) {
       

        
        if(!auth()->user()->hasPermissionTo('delete_users')) abort(401);
        $user->delete();
        return redirect()->route('users');
    }

    public function editPassword(Request $request, User $user = null) {

        $user = $user??auth()->user();
        if(auth()->id()!=$user?->id) {
            if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
        }

        return view('users.edit_password', compact('user'));
    }

    public function savePassword(Request $request, User $user)
{
    $currentUser = auth()->user();

    // Case 1: User is changing their own password
    if ($currentUser && $currentUser->id === $user->id) {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
            'old_password' => ['required'],
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->withErrors(['old_password' => 'The old password is incorrect.']);
        }

    // Case 2: Admin/Employee is changing someone else's password
    } elseif ($currentUser && $currentUser->hasPermissionTo('edit_users')) {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->symbols()],
        ]);

    } else {
        abort(403, 'Unauthorized action.');
    }

    // ✅ Only update password and temp_password flag — do NOT touch roles
    $user->password = bcrypt($request->password);
    $user->temp_password = false;
    $user->save();

    return redirect()->route('profile', ['user' => $user->id])
                    ->with('success', 'Password changed successfully!');
}

    public function chargeCreditForm(User $user) {
        // Only employees can access this
        if (!auth()->user()->hasRole('Employee')) {
            abort(403);
        }
    
        // Can only charge Customers
        if (!$user->hasRole('Customer')) {
            abort(403);
        }
    
        return view('users.charge_credit', compact('user'));
    }
    
    public function chargeCredit(Request $request, User $user) {
        if (!auth()->user()->hasRole('Employee')) {
            abort(403);
        }
    
        if (!$user->hasRole('Customer')) {
            abort(403);
        }
    
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);
    
        $user->credit += $validated['amount'];
        $user->save();
    
        return redirect()->route('charge_credit_form', $user->id)
                         ->with('success', 'Credit charged successfully!');
    }

    public function verify(Request $request) {

        $decryptedData = json_decode(Crypt::decryptString($request->token), true);
        $user = User::find($decryptedData['id']);
        if(!$user) abort(401);
        $user->email_verified_at = Carbon::now();
        $user->save();
        return view('users.verified', compact('user'));
       }

       public function forgotPassword()
    {
        return view('users.forgot_password');
    }

    public function sendTemporaryPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        $tempPassword = Str::random(10);
        $user->password = bcrypt($tempPassword);
        $user->temp_password = true;
        $user->save();

        Mail::raw("Your temporary password is: {$tempPassword}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Temporary Password')
                    ->from('mohamed102khaled@gmail.com', 'websec');
        });

        return redirect()->route('login')->with('success', 'Temporary password sent to your email.');
    }

       public function redirectToGoogle()
       {
           return Socialite::driver('google')->redirect();
       }
       
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
    
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(16)),
                ]
            );
    
            Auth::login($user);
            return redirect('/');
    
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
       

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $facebookUser->getEmail()],
                [
                    'name' => $facebookUser->getName(),
                    'facebook_id' => $facebookUser->getId(),
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            Auth::login($user);
            return redirect('/');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Facebook login failed.');
        }
    }

    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->stateless()->user();

            $user = User::where('email', $githubUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                    'email' => $githubUser->getEmail(),
                    'email_verified_at' => now(), // Automatically verify email
                    'password' => bcrypt(uniqid()), // Random password
                ]);
                $user->assignRole('customer'); // Assign the 'customer' role
            }

            Auth::login($user);

            return redirect('/')->with('success', 'Logged in successfully using GitHub!');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors('Unable to login using GitHub.');
        }
    }

    public function redirectToLinkedin()
    {
        return Socialite::driver('linkedin')->redirect();
    }

    public function handleLinkedinCallback()
    {
        try {
            $linkedinUser = Socialite::driver('linkedin')->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $linkedinUser->getEmail()],
                [
                    'name' => $linkedinUser->getName(),
                    'linkedin_id' => $linkedinUser->getId(),
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            Auth::login($user);
            return redirect('/');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'LinkedIn login failed.');
        }
    }

    public function loginWithCertificate(Request $request)
    {
        $email = emailFromLoginCertificate(); // Securely extract the email from the certificate

        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                Auth::login($user);
                return redirect()->intended('/');
            } else {
                return back()->withErrors(['email' => 'Certificate email not recognized.']);
            }
        }

        return back()->withErrors(['certificate' => 'No valid certificate found.']);
    }



}