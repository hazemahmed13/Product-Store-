<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        if (!auth()->user()->can('add_review')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'review' => 'required|string|max:1000',
        ]);

        Review::create([
            'product_id' => $productId,
            'user_id' => auth()->id(),
            'review' => $request->review,
        ]);

        return redirect()->back()->with('success', 'Review added!');
    }
}
