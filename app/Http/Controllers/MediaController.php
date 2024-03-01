<?php

namespace App\Http\Controllers;
use App\Game;
use App\banner;
use App\Medias;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    
     public function index()
     {
         try {
             return Medias::all();
         } catch (Exception $error){
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
    
    
    
    public function store(Request $request)
    {
        try{
        $validator =$this->validate($request,[
            'file' => 'required|mimes:jpg,jpeg,png|max:10000',
        ],$messages = [
            'mimes' => 'Please insert image only',
            'max'   => 'Image should be less than 10 MB'
        ]);
        
        // Extention
        $extention = $request->file->getClientOriginalExtension();
        
        // Mime Type
        $mimeType = $request->file->getClientMimeType();
        
        // File Name
        $imageName = time().'.'.$request->file->getClientOriginalExtension();
        
        //File Path
        $path = '/public/images/'.$imageName;
       
        // File Size
        $size = $request->file->getSize();
        
        // File Upload
        $request->file->move(public_path('images'), $imageName);
        
        $media = new Medias();
        $media->fileName = $imageName;
        $media->filePath = $path;
        $media->type = $mimeType;
        $media->size = $size;
        $media->extention = $extention;
        $media->save();
        
        
        return response()->json(['success'=>'uploaded successfully.','recoard' => $media]);
        
        }
        catch(Exception $error){
            return response()->json(['error'=> $error]);
        }
    }
    
    public function show($id){
        try {
            return Medias::findOrFail($id);
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
    }
    
    public function destroy($id)
     {
         try {
            $del = new Medias();
            $delMedia = Medias::findOrFail($id);
            unlink(public_path('images').'/'.$delMedia->fileName);
            $del->where('id',$id)->forcedelete();
            Game::where('media', '=', $id)->update(['media' => NULL]);
            banner::where('media', '=', $id)->forcedelete();
            return response()->json(['action' => true, 'message' => "Selected file has been deleted"]);
         } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }

}