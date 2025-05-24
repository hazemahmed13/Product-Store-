<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use DB;
use App\Models\User;
use App\Models\BoughtProduct;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller {

	use ValidatesRequests;

	public function __construct()
    {
        $this->middleware('auth:web')->except('list');
    }

	public function list(Request $request) {

		

		$query = Product::select("products.*");

		$query->when($request->keywords, 
		fn($q)=> $q->where("name", "like", "%$request->keywords%"));

		$query->when($request->min_price, 
		fn($q)=> $q->where("price", ">=", $request->min_price));
		
		$query->when($request->max_price, fn($q)=> 
		$q->where("price", "<=", $request->max_price));
		
		$query->when($request->order_by, 
		fn($q)=> $q->orderBy($request->order_by, $request->order_direction??"ASC"));

		$products = $query->get();

		return view('products.list', compact('products'));
	}

	public function edit(Request $request, Product $product = null) {
		

	 if(!auth()->user()) return redirect('/');

		$product = $product??new Product();

		return view('products.edit', compact('product'));
	}

	public function save(Request $request, Product $product = null) {

		$this->validate($request, [
	        'code' => ['required', 'string', 'max:32'],
	        'name' => ['required', 'string', 'max:128'],
	        'model' => ['required', 'string', 'max:256'],
			'stock' => ['required', 'integer', 'min:0'],
	        'description' => ['required', 'string', 'max:1024'],
	        'price' => ['required', 'numeric'],
	    ]);

		$product = $product??new Product();
		$product->fill($request->all());
		$product->save();

		return redirect()->route('products_list');
	}

	public function delete(Request $request, Product $product) {

		if(!auth()->user()->hasPermissionTo('delete_products')) abort(401);

		$product->delete();

		return redirect()->route('products_list');
	}

	public function buy(Request $request, Product $product) {
		$user = auth()->user();
	
		// Check if the stock is available
		if ($product->stock == 0) {
			return redirect()->route('products_list')->with('error', 'This product is not available right now.');
		}
	
		// Check if the user has enough credit
		if ($user->credit < $product->price) {
			return view('products.insufficient_credit', [
				'product' => $product,
				'user' => $user,
				
			]);
		}
	
		// Decrease the user's credit
		$user->credit -= $product->price;
		$user->save();
	
		// Decrease the product stock
		$product->stock -= 1;
		$product->save();
	
		// Attach the product to the user's bought products
		$user->boughtProducts()->attach($product->id);
	
		return redirect()->route('products_list')->with('success', 'Product bought successfully!');
	}
	
	public function returnProduct(Product $product)
{
    $user = auth()->user();

    // Find the pivot record
    $pivot = $user->boughtProducts()
        ->where('product_id', $product->id)
        ->first();

    if (!$pivot) {
        return back()->with('error', 'You have not bought this product.');
    }

    // Refund only if product was actually bought
    $totalPrice = $pivot->pivot->total_price;

    // Refund user credits
    $user->credit += $totalPrice;
    $user->save();

    // Increase product quantity (if tracked)
    $product->stock += $pivot->pivot->quantity;
    $product->save();

    // Remove the bought product record
    $user->boughtProducts()->detach($product->id);

    return back()->with('success', 'Product returned and credits refunded.');
}

public function trackDelivery()
{
    if (!auth()->user()->hasPermissionTo('track_delivery')) {
        abort(403);
    }

    $purchases = BoughtProduct::with('user', 'product')->get();

    return view('products.track_delivery', compact('purchases'));
}

public function updateStatusMessage(Request $request, $purchase_id)
{
    if (!auth()->user()->hasPermissionTo('track_delivery')) {
        abort(403);
    }

    $request->validate([
        'status_message' => 'nullable|string|max:255'
    ]);

    $purchase = BoughtProduct::findOrFail($purchase_id);
    $purchase->status_message = $request->input('status_message');
    $purchase->save();

    return redirect()->route('track_delivery')->with('success', 'Status updated!');
}

public function toggleFavourite(Product $product)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('select_favourite')) {
            abort(403);
        }

        $product->favourite = !$product->favourite;
        $product->save();

        return back()->with('success', 'Favourite status updated.');
    }





	
}