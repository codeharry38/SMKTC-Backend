<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Http\Resources\Organization as Org;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
     // Fetch All Records
     public function index()
     {
         try {
             return Org::collection(Organization::all());
         } catch (Exception $error){
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }

    // Fetch Active Records
    public function activeRecord()
     {
         try {
             return Org::collection(Organization::where('status', 1)->get());
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
 
     
     // Create and Edit Records
     public function store(Request $request)
     {
         $validatedData = $request->validate([
             'name' => 'required',
             'code' => 'required',
         ]);
         $data = $request->isMethod('put') ? Organization::findOrFail($request->id) : new Organization();
         $data->id = $request->input('id');
         $data->name = $request->input('name');
         $data->code = $request->input('code');
         $data->status = $request->input('status');
         $data->save();
         $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "Organization has been updated", 'record' => $data]) :
         response()->json(['action' => true, 'message' => "Organization has been created", 'record' => $data]);
         return $message;
     }
 
    // Find by id
     public function show($id)
     {
         try {
             return new Org(Organization::findOrFail($id));
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
 
     // update Status for Organization
     public function statusUpdate(Request $request)
     {
         try {
             $data = Organization::findOrFail($request->id);
             $data->id = $request->input('id');
             $data->status = $request->input('status');
             $data->save();
             $message = $request->status == 0 ? 'Organization has been Deactivated' : 'Organization has been Activated';
             return response()->json(['message'=> $message, 'action' => true]);
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
 
 
 
     // Delete Organizations
     public function destroy($id)
     {
         try {
            $del = new Organization();
            $del->whereIn('id', explode(",", $id))->delete();
            return response()->json(['action' => true, 'message' => "Selected Organization has been deleted"]);
         } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
}
