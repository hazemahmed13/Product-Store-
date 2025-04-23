<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerificationEmail;
use App\Services\UserService;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersController extends Controller
{
    use ValidatesRequests;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function list(Request $request)
    {
        if (!auth()->user()->can('show_users')) abort(403);

        $users = User::query()
            ->when($request->keywords, fn($q) => $q->where('name', 'like', "%{$request->keywords}%"))
            ->paginate(15);

        return view('users.list', compact('users'));
    }

    public function register()
    {
        return view('users.register');
    }

    public function doRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:5'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
        ]);

        $user = $this->userService->createUser($validated);

        $this->userService->sendVerificationEmail($user);

        return redirect('/')->with('message', 'Please check your email to verify your account.');
    }

    public function login()
    {
        return view('users.login');
    }

    public function doLogin(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withInput()->withErrors('Invalid login credentials.');
        }

        $user = Auth::user();

        if (!$user->email_verified_at) {
            Auth::logout();
            return back()->withInput()->withErrors('Your email is not verified.');
        }

        return redirect('/');
    }

    public function doLogout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function profile(User $user = null)
    {
        $user = $user ?? auth()->user();

        if (auth()->id() !== $user->id && !auth()->user()->can('show_users')) {
            abort(403);
        }

        $permissions = $user->getAllPermissions();

        return view('users.profile', compact('user', 'permissions'));
    }

    public function edit(User $user = null)
    {
        $user = $user ?? auth()->user();

        if (auth()->id() !== $user->id && !auth()->user()->can('edit_users')) {
            abort(403);
        }

        $roles = Role::all()->map(function ($role) use ($user) {
            $role->taken = $user->hasRole($role->name);
            return $role;
        });

        $permissions = Permission::all()->map(function ($permission) use ($user) {
            $permission->taken = $user->hasPermissionTo($permission->name);
            return $permission;
        });

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    public function save(Request $request, User $user)
    {
        if (auth()->id() !== $user->id && !auth()->user()->can('admin_users')) {
            abort(403);
        }

        $user->update(['name' => $request->name]);

        if (auth()->user()->can('admin_users')) {
            $user->syncRoles($request->roles ?? []);
            $user->syncPermissions($request->permissions ?? []);
            Cache::forget("permissions_user_{$user->id}");
        }

        return redirect()->route('profile', ['user' => $user->id]);
    }

    public function delete(User $user)
    {
        if (!auth()->user()->can('delete_users')) abort(403);

        $user->delete();

        return redirect()->route('users')->with('message', 'User deleted successfully.');
    }

    public function editPassword(User $user = null)
    {
        $user = $user ?? auth()->user();

        if (auth()->id() !== $user->id && !auth()->user()->can('edit_users')) {
            abort(403);
        }

        return view('users.edit_password', compact('user'));
    }

    public function savePassword(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            $request->validate([
                'old_password' => ['required'],
                'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
            ]);

            if (!Auth::validate(['email' => $user->email, 'password' => $request->old_password])) {
                Auth::logout();
                return redirect('/');
            }
        } elseif (!auth()->user()->can('edit_users')) {
            abort(403);
        }

        $user->update(['password' => bcrypt($request->password)]);

        return redirect()->route('profile', ['user' => $user->id]);
    }

    public function verify(Request $request)
    {
        $decrypted = json_decode(Crypt::decryptString($request->token), true);
        $user = User::findOrFail($decrypted['id']);

        $user->update(['email_verified_at' => Carbon::now()]);

        return view('users.verified', compact('user'));
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            if (!$googleUser->user['verified_email']) {
                return redirect('/login')->with('error', 'Google account email not verified.');
            }

            $user = User::updateOrCreate([
                'google_id' => $googleUser->id,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
            ]);

            Auth::login($user);

            return redirect('/');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google login failed.');
        }
    }
}
