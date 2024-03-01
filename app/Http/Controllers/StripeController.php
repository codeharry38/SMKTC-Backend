<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Stripe;
use App\Helpers\StripeManager;

class StripeController extends Controller
{
    
    public function createAccount(Request $request)
    {
        $request;
       return StripeManager::createProduct();
    }
    

}