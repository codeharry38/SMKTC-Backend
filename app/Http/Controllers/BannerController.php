<?php

namespace App\Http\Controllers;

use App\Http\Resources\Banner as Rbanner;
use App\banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    
     // Fetch All Records
     public function index()
     {
         try {
             return Rbanner::collection(banner::all());
         } catch (Exception $error){
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
     
     // Create and Edit Records
     public function store(Request $request)
     {
        try {
         $validatedData = $request->validate([
             'name' => 'required',
             'media' => 'required',
            
         ]);
         $data = $request->isMethod('put') ? banner::findOrFail($request->id) : new banner();
         $data->id = $request->input('id');
         $data->name = $request->input('name');
         $data->media = $request->input('media');
         $data->status = $request->input('status');
          $data->mini = $request->input('mini');
         $data->save();
         $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "Banner has been updated", 'record' => new Rbanner($data)]) :
         response()->json(['action' => true, 'message' => "Banner has been created", 'record' => new Rbanner($data)]);
         return $message;
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
     }
    

    // Find by id
     public function show($id)
     {
         try {
             return new Rbanner(banner::findOrFail($id));
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
 
     // update Status for Banner
     public function statusUpdate(Request $request)
     {
         try {
             $data = banner::findOrFail($request->id);
             $data->id = $request->input('id');
             $data->status = $request->input('status');
             $data->save();
             $message = $request->status == 0 ? 'Banner has been Deactivated' : 'Banner has been Activated';
             return response()->json(['message'=> $message, 'action' => true]);
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
 
 
     // Delete Banner
     public function destroy($id)
     {
         try {
            $del = new banner();
            $del->whereIn('id', explode(",", $id))->delete();
            return response()->json(['action' => true, 'message' => "Selected Banner has been deleted"]);
         } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
    
    
    // Front Side
    public function activBanner()
    {
        try {
            return Rbanner::collection(banner::where('status', 1)->where('mini', 0)->get());
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        } 
     }
     
     // Front Side
    public function activMiniBanner()
    {
        try {
            return Rbanner::collection(banner::where('status', 1)->where('mini',1)->get());
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        } 
     }
}
