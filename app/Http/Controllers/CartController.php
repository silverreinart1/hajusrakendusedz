<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        return Inertia::render('Shop/Cart', [
            'cart'     => session('cart', []),
            'products' => ShopController::products(),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|min:1',
        ]);

        $products = collect(ShopController::products());
        $product  = $products->firstWhere('id', $request->product_id);

        if (!$product) {
            return back()->withErrors(['product_id' => 'Toodet ei leitud.']);
        }

        $cart = session('cart', []);
        $id   = $request->product_id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $request->quantity;
        } else {
            $cart[$id] = [
                'product_id' => $id,
                'name'       => $product['name'],
                'price'      => $product['price'],
                'image'      => $product['image'],
                'quantity'   => $request->quantity,
            ];
        }

        session(['cart' => $cart]);
        return back()->with('success', 'Toode lisatud ostukorvi!');
    }

    public function update(Request $request, int $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity;
            session(['cart' => $cart]);
        }

        return back();
    }

    public function remove(int $productId)
    {
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);
        return back()->with('success', 'Toode eemaldatud.');
    }
}
