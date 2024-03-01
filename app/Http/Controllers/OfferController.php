<?php

namespace App\Http\Controllers;

use App\Offer;
use App\Http\Resources\Offer as Roffer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    
    // Fetch All Records
    public function index()
    {
        try {
            return Roffer::collection(Offer::all());
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // Fetch Active Records
    public function activeRecord()
     {
         try {
             return Roffer::collection(Offer::where('status', 1)->get());
         } catch (Exception $error){
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
    
    // Create and Edit Records
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'offercode' => 'required',
            'paidticket' => 'required',
            'freeticket' => 'required',
            'start_at' => 'required',
            'end_at' => 'required',
        ]);
        $data = $request->isMethod('put') ? Offer::findOrFail($request->id) : new Offer();
        $data->id = $request->input('id');
        $data->name = $request->input('name');
        $data->offercode = $request->input('offercode');
        $data->paidticket = $request->input('paidticket');
        $data->freeticket = $request->input('freeticket');
        $data->start_at = $request->input('start_at');
        $data->end_at = $request->input('end_at');
        $data->status = $request->input('status');
        $data->save();
        $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "Offer has been updated", 'record' => $data]) :
        response()->json(['action' => true, 'message' => "Offer has been created", 'record' => $data]);
        return $message;
    }

   // Find by id
    public function show($id)
    {
        try {
            return new Roffer(Offer::findOrFail($id));
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // update Status for Offer
    public function statusUpdate(Request $request)
    {
        try {
            $data = Offer::findOrFail($request->id);
            $data->id = $request->input('id');
            $data->status = $request->input('status');
            $data->save();
            $message = $request->status == 0 ? 'Offer has been Deactivated' : 'Offer has been Activated';
            return response()->json(['message'=> $message, 'action' => true]);
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }



    // Delete Offers
    public function destroy($id)
    {
        try {
           $del = new Offer();
           $del->whereIn('id', explode(",", $id))->delete();
           return response()->json(['action' => true, 'message' => "Selected offer has been deleted"]);
        } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
}
