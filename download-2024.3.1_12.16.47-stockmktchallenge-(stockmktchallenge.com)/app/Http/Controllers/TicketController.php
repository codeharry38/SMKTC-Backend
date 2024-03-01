<?php

namespace App\Http\Controllers;

use App\Http\Resources\Ticket as Rticket;
use App\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // Fetch All Records
    public function index()
    {
        try {
            return Rticket::collection(Ticket::all());
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // Fetch Active Records
    public function activeRecord()
     {
         try {
             return Rticket::collection(Ticket::where('status', 1)->get());
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
    
    // Create and Edit Records
    public function store(Request $request)
    {
        try {
            $data = Ticket::findOrFail($request->id);
            $data->id = $request->input('id');
            $data->assign_to = $request->input('assign_to');
            $data->is_paid = $request->input('is_paid');
            $data->save();
            return response()->json(['action' => true, 'message' => "Player has been assigned", 'record' => $data]);
        } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
    }

   // Find by id
    public function show($id)
    {
        try {
            return new Rticket(Ticket::findOrFail($id));
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // update Status for Ticket
    public function statusUpdate(Request $request)
    {
        try {
            $data = Ticket::findOrFail($request->id);
            $data->id = $request->input('id');
            $data->status = $request->input('status');
            $data->save();
            $message = $request->status == 0 ? 'Ticket has been Deactivated' : 'Ticket has been Activated';
            return response()->json(['message'=> $message, 'action' => true]);
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }


    // Delete Ticket
    public function destroy($id)
    {
        try {
           $del = new Ticket();
           $del->whereIn('id', explode(",", $id))->delete();
           return response()->json(['action' => true, 'message' => "Selected Ticket has been deleted"]);
        } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
    // Filter Data
    public function filterTicket(Request $request){
        try{
            if($request->query('type') == 'all'){
                return Rticket::collection(Ticket::where(['game' => $request->query('game')])->get());
            }else{
                return Rticket::collection(Ticket::where(['game' => $request->query('game'), 'ticket_type' => $request->query('type')])->get());
            }
           
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
    
    
    
    
    
    
    /* Player Side Data */
    
    public function myTickets(){
        return Rticket::collection(Ticket::where('assign_to', auth('api')->user()->id)->whereOr('used_by', auth('api')->user()->id)->get());
    }
    
    
    
    
    
    
    
    
    
    
    
}
