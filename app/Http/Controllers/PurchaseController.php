<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function purchase($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            return redirect()->back()->with('error', 'Product is out of stock or does not exist.');
        }

        if (auth()->user()->credit < $product->price) {
            return redirect()->back()->with('error', 'Insufficient credit.');
        }

        // Decrease the user's credit and the product's stock
        $user = auth()->user();
        $user->credit -= $product->price;
        $user->save();

        $product->stock -= 1;
        $product->save();

        // Create the purchase record
        Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return redirect()->route('purchases.index')->with('success', 'Product purchased successfully.');
    }

    public function index()
    {
        // عرض جميع المنتجات المشتراة للمستخدم الحالي
        $purchases = auth()->user()->purchases()->with('product')->get();
        return view('purchases.index', compact('purchases'));
    }
}
