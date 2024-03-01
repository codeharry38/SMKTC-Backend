<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Game;
use App\Ticket;
use App\Helpers\StripeManager;

class CheckoutController extends Controller
{
    
    public function CreateCheckoutSession(Request $request)
    {
        try {        
            	$paid = Ticket::where('game', '=', $request->game)->where('ticket_type', '=', 'online')->where('assign_to', '=', 0)->where('used_by', '=', 0)->where('is_used', '=', 0)->where('is_paid', '=', 0)->count();
            	$free = Ticket::where('game', '=', $request->game)->where('ticket_type', '=', 'free')->where('assign_to', '=', 0)->where('used_by', '=', 0)->where('is_used', '=', 0)->where('is_paid', '=', 0)->count();
            	if($paid >= $request->input('paid') && $free >= $request->input('free')){
                	$data = Game::findOrFail($request->game);
                	//$request;
                	return StripeManager::createSession($data, $request->input('quantity'),auth('api')->user());
            	}else{
                	return response()->json(['action' => false, 'message' => "Maybe sold out or offer is closed, please try again."]);
            	}
        }catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
    }
    
    public function assignTickets(Request $request){
        try {
        	if($request->input('ticket')){
            	if($request->input('ticket_type') == 'reserve'){
               		Ticket::where('access_code', '=', $request->input('ticket'))->where('ticket_type', '=', 'reserve')->where('assign_to', '=', 0)->where('used_by', '=', 0)->where('is_used', '=', 0)->update(['is_paid' => '1','assign_to' => auth('api')->user()->id]);
            	}
            	if($request->input('ticket_type') == 'online'){
               		Ticket::where('access_code', '=', $request->input('ticket'))->where('ticket_type', '=', 'online')->where('assign_to', '=', 0)->where('used_by', '=', 0)->where('is_used', '=', 0)->update(['is_paid' => '1','assign_to' => auth('api')->user()->id]);
            	}
            	if($request->input('paid') > 1){
                	$finalqty = $request->input('paid') - 1;
                	Ticket::where('game', '=', $request->input('game'))->where('ticket_type', '=', 'online')->where('assign_to', '=', 0)->where('used_by', '=', 0)->where('is_used', '=', 0)->where('is_paid', '=', 0)->take($finalqty)->update(['is_paid' => '1','assign_to' => auth('api')->user()->id]);
                }
            	if($request->input('paid') > 0){
                	Ticket::where('game', '=', $request->input('game'))->where('ticket_type', '=', 'free')->where('assign_to', '=', 0)->where('used_by', '=', 0)->where('is_used', '=', 0)->where('is_paid', '=', 0)->take($request->input('free'))->update(['is_paid' => '1','assign_to' => auth('api')->user()->id]);
                }
            }
            else{
            	Ticket::where('game', '=', $request->input('game'))->where('ticket_type', '=', 'online')->where('assign_to', '=', 0)->where('used_by', '=', 0)->where('is_used', '=', 0)->where('is_paid', '=', 0)->take($request->input('paid'))->update(['is_paid' => '1','assign_to' => auth('api')->user()->id]);
            	if($request->input('paid') > 0){
                	Ticket::where('game', '=', $request->input('game'))->where('ticket_type', '=', 'free')->where('assign_to', '=', 0)->where('used_by', '=', 0)->where('is_used', '=', 0)->where('is_paid', '=', 0)->take($request->input('free'))->update(['is_paid' => '1','assign_to' => auth('api')->user()->id]);
            	}
            }
            return response()->json(['action' => true, 'message' => "Ticket has assigned to the player."]);
        }catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }

    }
    

}