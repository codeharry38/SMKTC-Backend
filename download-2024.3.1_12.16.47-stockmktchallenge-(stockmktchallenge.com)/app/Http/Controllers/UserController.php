<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

// Resources For Backend Manager
use App\Http\Resources\UserInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Fetch Loged in User Info
    public function UserInfo()
    {
        return response()->json(new UserInfo(auth('api')->user()));
    }
    // Login
    public function login(Request $request) {
        try {
             $validatedData = $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);
        	$checkRole = User::where('username', $request->input('username'))->first();
        	if($checkRole != ''){
            	if($checkRole->role == 'admin'){
            		$response = Http::asForm()->post(route('passport.token'), [
            		    'grant_type' => 'password',
            		    'client_id' => '2',
            		    'client_secret' => 'yOp0nC0x0WmgTnpAgDcXiWXzqEjuVmHgqfLuMq9Q',
            		    'username' => $request->input('username'),
            		    'password' => $request->input('password'),
            		    'scope' => '',
            		    'provider' => 'user'
            		]);
            		$makeToken = json_decode((string) $response->getBody(), true);
            		$token = $makeToken['access_token'];
            		$userdetail = Http::withHeaders([
                		'Accept' => 'application/json',
                		'Authorization' => 'Bearer '.$token,
            		])->get(route('userinfo'));
            	return response()->json(['message'=> 'Login Successfully', 'action' => true, 'data' => json_decode((string) $response->getBody(), true), 'loginManage' => json_decode((string) $userdetail->getBody(), true)], 200);
                }else{
            	    return response()->json(['message'=> $checkRole->role .' not eligible to login manager side.', 'action' => false]);
            	}
            }else{
            	return response()->json(['message' => 'Your credentials are incorrect. Please try again', 'action' => false]);
            }
        
        
        } catch (\Exception $e) {
            if ($e->getCode() === 400) {
                return response()->json(['message' => 'Invalid Request. Please enter a username or a password.', 'action' => false, 'error' => $e->getCode()], 400);
            } elseif ($e->getCode() === 401) {
                return response()->json(['message' => 'Your credentials are incorrect. Please try again', 'action' => false, 'error' => $e->getCode()], 401);
            } elseif ($e->getCode() === 404) {
                return response()->json(['message' => 'Your credentials are incorrect', 'action' => false, 'error' => $e->getCode()], 404);
            }
            return response()->json(['message' => 'Something went wrong on the server.', 'action' => false, 'error' => $e->getCode()], 500);
        }
    }
    
    
    // update Status
    public function statusUpdate(Request $request)
    {
        try {
            $data = User::findOrFail($request->id);
            $data->id = $request->input('id');
            $data->status = $request->input('status');
            $data->save();
            
            $message = $request->status == 0 ? 'User has been Deactivated' : 'User has been Activated';
           
            return response()->json(['message'=> $message, 'action' => true]);
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // Logout
    public function Logout(Request $request)
    {
        try {
           //$request->user()->token()->revoke();
           $request->user()->token()->delete();
            return response()->json(['message'=> 'Logged out successfully', 'action' => true]);
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // Fetch All Users
    public function index()
    {
        try {
            return UserInfo::collection(User::whereIn('role', ['admin','analyser','editor'])->get());
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // Create And Update Users
    public function store(Request $request)
    {
        try {
            if ($request->isMethod('put')) {
                $validatedData = $request->validate([
                    'name' => 'required',
                    'email' => 'required|email',
                    'phone' => 'required|regex:/[0-9]{9}/|max:10',
                    'username' => 'required',
                    'role' => 'required',
                ]);
            } else {
                 $validatedData = $request->validate([
                    'name' => 'required',
                    'email' => 'unique:users|required|email',
                    'username' => 'unique:users|required',
                    'phone' => 'required|unique:users|regex:/[0-9]{9}/|max:10',
                    'password' => 'required|min:4',
                    'role' => 'required',
                ]);
            }
            $data = $request->isMethod('put') ? User::findOrFail($request->id) : new User();
            $data->id = $request->input('id');
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            $data->username = $request->input('username');
            $data->phone = $request->input('phone');
            $data->role = $request->input('role');
            $data->status = $request->input('status');
            if ($request->isMethod('put')) {
                if (empty($request->input('password'))) {
                    //
                } else {
                    $data->password = bcrypt($request->input('password'));
                }
            } else {
                $data->password = bcrypt($request->input('password'));
            }
            $data->save();
            $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "User has been updated", 'record' => $data]) :
            response()->json(['action' => true, 'message' => "User has been created", 'record' => $data]);
            return $message;
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    public function setting(Request $request)
    {
        try {
            $data = User::findOrFail(auth('api')->user()->id);
            $data->id = auth('api')->user()->id;
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            //$data->username = $request->input('username');
            $data->phone = $request->input('phone');
            if (empty($request->input('password'))) {
                //
            } else {
                $data->password = bcrypt($request->input('password'));
            }
            $data->save();
            return response()->json(['action' => true, 'message' => "Setting has been updated"]);
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // Fetch User By Id
    public function show($id)
    {
        try {
            return new UserInfo(User::findOrFail($id));
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    // Delete Users
    public function destroy($id)
    {
        try {
           $del = new User();
           $del->whereIn('id', explode(",", $id))->delete();
           return response()->json(['action' => true, 'message' => "Selected users has been deleted"]);
        } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
}
