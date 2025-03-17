<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MiniController extends Controller
{
    public function secoundaction () 
    {
 
        $bill = [
            ['item' => 'Milk', 'price' => 1.50, 'quantity' => 2],
            ['item' => 'Bread', 'price' => 0.80, 'quantity' => 3],
            ['item' => 'Eggs', 'price' => 2.00, 'quantity' => 1],
            ['item' => 'Butter', 'price' => 1.25, 'quantity' => 2],
        ];
    
       
        return view('minitest', compact('bill'));
    }
}
