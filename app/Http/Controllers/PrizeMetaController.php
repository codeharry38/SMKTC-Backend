<?php

namespace App\Http\Controllers;

use App\Http\Resources\PrizeMeta as RPrizeMeta;
use App\PrizeMeta;
use Illuminate\Http\Request;

class PrizeMetaController extends Controller
{
     // Fetch All Records
     public function index($id)
     {
         try {
             return RPrizeMeta::collection(PrizeMeta::where('prize', $id)->where('is_daily', 0)->orderBy('position_type', 'asc')->orderBy('position', 'asc')->get());
         } catch (Exception $error){
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
      
     // Create and Edit Records
     public function store(Request $request)
     {
         $validatedData = $request->validate([
             'position' => 'required',
             'position_type' => 'required',
             'prize_type' => 'required',
             'prize_value' => 'required',
         ]);
         $data = $request->isMethod('put') ? PrizeMeta::findOrFail($request->id) : new PrizeMeta();
         $data->id = $request->input('id');
         $data->position = $request->input('position');
         $data->position_type = $request->input('position_type');
         $data->prize_type = $request->input('prize_type');
         $data->prize = $request->input('prize');
         $data->prize_value = $request->input('prize_value');
         $data->save();
         $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "PrizeMeta has been updated", 'record' => $data]) :
         response()->json(['action' => true, 'message' => "PrizeMeta has been created", 'record' => $data]);
         return $message;
     }



    // Find by id
     public function show($id)
     {
         try {
             return new RPrizeMeta(PrizeMeta::findOrFail($id));
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
 
     // Delete PrizeMetas
     public function destroy($id)
     {
         try {
            $del = new PrizeMeta();
            $del->whereIn('id', explode(",", $id))->delete();
            return response()->json(['action' => true, 'message' => "Selected PrizeMeta has been deleted"]);
         } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
     
     // Daily Data
     
     // Fetch All Records
     public function indexDaily($id)
     {
         try {
             return RPrizeMeta::collection(PrizeMeta::where('prize', $id)->where('is_daily', 1)->orderBy('position_type', 'asc')->orderBy('position', 'asc')->get());
         } catch (Exception $error){
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
     
     // Create and Edit Records
     public function storeDaily(Request $request)
     {
         $validatedData = $request->validate([
             'position' => 'required',
             'position_type' => 'required',
             'prize_type' => 'required',
             'prize_value' => 'required',
         ]);
         $data = $request->isMethod('put') ? PrizeMeta::findOrFail($request->id) : new PrizeMeta();
         $data->id = $request->input('id');
         $data->position = $request->input('position');
         $data->position_type = $request->input('position_type');
         $data->is_daily = 1;
         $data->prize_type = $request->input('prize_type');
         $data->prize = $request->input('prize');
         $data->prize_value = $request->input('prize_value');
         $data->save();
         $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "PrizeMeta has been updated", 'record' => $data]) :
         response()->json(['action' => true, 'message' => "PrizeMeta has been created", 'record' => $data]);
         return $message;
     }
     
     
     
     
     
}
