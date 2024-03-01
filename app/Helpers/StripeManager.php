<?php


namespace App\Helpers;

use Stripe;

class StripeManager
{

    
    public static function createAccount($data)
    {
        $stripe = new \Stripe\StripeClient('ENV');
        $customer = $stripe->customers->create([
            'email' => $data['email'],
            'name' => $data['name']
        ]);
        return $customer->id;
    }

    // Create Product along with price in stripe during create product at the backend

    public static function createProduct($data)
    {

        $stripe = new \Stripe\StripeClient('ENV');
        $product = $stripe->products->create([
            'name' => $data['name'],
            'default_price_data' => [
                'currency' => 'USD',
                'unit_amount' => (int)$data['cost'] * 100,
            ]
        ]);
        return ['stripe_product' =>  $product->id, 'stripe_product_price' => $product->default_price];
    }
    
    public static function createSession($data,$qty,$user)
    {
       // return $user['stripe_account'];
        $stripe = new \Stripe\StripeClient('ENV');
        $sessionId = $stripe->checkout->sessions->create([
            'success_url' => 'https://URL/checkout/success',
            'cancel_url' => 'https://URL/chekout/cancel',
            'payment_method_types' => ["card","cashapp"],
            'customer' => $user['stripe_account'],
            'line_items' => [
              [
                'price' => $data['stripe_product_price'],
                'quantity' => $qty,
              ],
            ],
            'mode' => 'payment',
          ]);
        return $sessionId->id;
    }
	// Fetch Payment Method
	public static function paymentMethod($user)
    {
       // return $user['stripe_account'];
        $stripe = new \Stripe\StripeClient('ENV');
        $data = $stripe->paymentMethods->all(['customer' => $user]);
        return $data;
    }

	// Fetch Invoce
	public static function invoce($user)
    {
       // return $user['stripe_account'];
        $stripe = new \Stripe\StripeClient('ENV');
        $data = $stripe->paymentIntents->all(['customer' => $user]);
        return $data;
    }


}