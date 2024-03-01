<?php

namespace App\Http\Controllers;

use App\Http\Resources\Prize as Rprize;
use App\Prize;
use Illuminate\Http\Request;

class PrizeController extends Controller
{
    // Fetch All Records
    public function index()
    {
        try {
            return Rprize::collection(Prize::all());
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // Fetch Active Records
    public function activeRecord()
     {
         try {
             return Rprize::collection(Prize::where('status', 1)->get());
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
    
    // Create and Edit Records
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);
        $data = $request->isMethod('put') ? Prize::findOrFail($request->id) : new Prize();
        $data->id = $request->input('id');
        $data->name = $request->input('name');
        $data->status = $request->input('status');
        $data->save();
        $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "Prize has been updated", 'record' => $data]) :
        response()->json(['action' => true, 'message' => "Prize has been created", 'record' => $data]);
        return $message;
    }

   // Find by id
    public function show($id)
    {
        try {
            return new Rprize(Prize::findOrFail($id));
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // update Status for Prize
    public function statusUpdate(Request $request)
    {
        try {
            $data = Prize::findOrFail($request->id);
            $data->id = $request->input('id');
            $data->status = $request->input('status');
            $data->save();
            $message = $request->status == 0 ? 'Prize has been Deactivated' : 'Prize has been Activated';
            return response()->json(['message'=> $message, 'action' => true]);
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }


    // Delete Prizes
    public function destroy($id)
    {
        try {
           $del = new Prize();
           $del->whereIn('id', explode(",", $id))->delete();
           return response()->json(['action' => true, 'message' => "Selected Prize has been deleted"]);
        } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
}
