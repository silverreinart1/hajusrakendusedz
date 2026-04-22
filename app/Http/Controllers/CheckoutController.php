<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Ostukorv on tühi.');
        }

        return Inertia::render('Shop/Checkout', [
            'cart'      => array_values($cart),
            'stripeKey' => config('services.stripe.key'),
            'total'     => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']),
        ]);
    }

    public function createSession(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email',
            'phone'      => 'nullable|string|max:20',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Ostukorv on tühi.'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = collect($cart)->map(fn($item) => [
            'price_data' => [
                'currency'     => 'eur',
                'unit_amount'  => (int) round($item['price'] * 100),
                'product_data' => ['name' => $item['name']],
            ],
            'quantity' => $item['quantity'],
        ])->values()->toArray();

        $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        $order = Order::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'total'      => $total,
            'status'     => 'pending',
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item['product_id'],
                'product_name' => $item['name'],
                'quantity'     => $item['quantity'],
                'price'        => $item['price'],
            ]);
        }

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('checkout.cancel'),
            'metadata'             => ['order_id' => $order->id],
            'customer_email'       => $request->email,
        ]);

        $order->update(['stripe_session_id' => $session->id]);

        return response()->json(['url' => $session->url]);
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId     = $request->query('session_id');
        $stripeSession = StripeSession::retrieve($sessionId);
        $order         = null;

        if ($stripeSession->payment_status === 'paid') {
            $order = Order::where('stripe_session_id', $sessionId)->first();
            if ($order) {
                $order->update(['status' => 'paid']);
            }
            session()->forget('cart');
        }

        return Inertia::render('Shop/Success', [
            'order' => $order,
        ]);
    }

    public function cancel()
    {
        return Inertia::render('Shop/Cancel');
    }

    public function webhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response('Webhook error: ' . $e->getMessage(), 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            Order::where('stripe_session_id', $session->id)
                ->update(['status' => 'paid']);
        }

        return response('OK', 200);
    }
}
