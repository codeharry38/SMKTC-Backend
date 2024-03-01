<?php

namespace App\Http\Controllers;

use App\User as Player;
use Illuminate\Http\Request;

// Resources For Backend Manager
use App\Http\Resources\Player as Rplayer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Helpers\StripeManager;
use App\Helpers\EmailTemplateManager;


class PlayerController extends Controller
{

    public function playerInfo() {
        return response()->json(auth('api')->user());
    }

    public function login(Request $request) {
        try {
            $validatedData = $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);
            
           /* if(Auth::attempt(['username' => $request->username , 'password' => $request->password])){
                $user = Auth::user();
                $token = $user->createToken('myapp')->accessToken;
                $userdetail = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ])->get(route('playerinfo'));
                return response()->json(['message'=> 'Login Successfully', 'action' => true, 'data' => $token, 'loginManage' => json_decode((string) $userdetail->getBody(), true)], 200);
            }else{
                return response()->json(['message'=> 'Invalid credentials', 'action' => false], 401);
            }*/
            
            $checkRole = Player::where('username', $request->input('username'))->first();
        	if($checkRole != ''){
        	//$check = $checkRole 
            	if($checkRole->role == 'player'){            
            	    $response = Http::asForm()->post(route('passport.token'), [
            	        'grant_type' => 'password',
            	        'client_id' => '2',
            	        'client_secret' => 'yOp0nC0x0WmgTnpAgDcXiWXzqEjuVmHgqfLuMq9Q',
            	        'username' => $request->input('username'),
            	        'password' => $request->input('password'),
            	        'scope' => '',
            	    //'provider' => 'users'
            	    ]);
            	    $check = json_decode((string) $response->getBody());
            	    if(isset($check->error)){
            	        return response()->json(['message'=> 'Invalid credentials', 'action' => false, 'data' => $check->error  ], 401);
            	    }else{
            	        $makeToken = json_decode((string) $response->getBody(), true);
            	        $token = $makeToken['access_token'];
            	        $userdetail = Http::withHeaders([
            	            'Accept' => 'application/json',
            	            'Authorization' => 'Bearer '.$token,
            	        ])->get(route('userinfo'));
            	        return response()->json(['message'=> 'Login Successfully', 'action' => true, 'data' => $token, 'loginManage' => json_decode((string) $userdetail->getBody(), true)], 200);
            	    }
            	}else{
            	    return response()->json(['message'=> $checkRole->role .' not eligible to login player side.', 'action' => false]);
            	}
            }else{
            	return response()->json(['message' => 'Your credentials are incorrect. Please try again', 'action' => false]);
            }
           
            
        } catch (Exception $e) {
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
           $data = Player::findOrFail($request->id);
           $data->id = $request->input('id');
           $data->status = $request->input('status');
           $data->save();
           
           $message = $request->status == 0 ? 'Player has been Deactivated' : 'Player has been Activated';
          
           return response()->json(['message'=> $message, 'action' => true]);
       } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
       }
   }


   
   // Logout
   public function Logout(Request $request)
   {
       try {
          //$request->Player()->token()->revoke();
          auth('api')->user()->token()->delete();
           return response()->json(['message'=> 'Logged out successfully', 'action' => true]);
       } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
       }
   }



   // Fetch All Players
   public function index()
   {
       try {
           return Rplayer::collection(Player::where('role', 'player')->get());
       } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
       }
   }



   // Create And Update Players
   public function store(Request $request)
   {
       try {
           if ($request->isMethod('put')) {
               $validatedData = $request->validate([
                   'name' => 'required',
                   'email' => 'required|email',
                   'phone' => 'required|regex:/[0-9]{9}/|max:10',
                   'username' => 'required'
               ]);
           } else {
                $validatedData = $request->validate([
                   'name' => 'required',
                   'email' => 'unique:users|required|email',
                   'username' => 'unique:users|required',
                   'phone' => 'required|unique:users|regex:/[0-9]{9}/|max:10',
                   'password' => 'required|min:4'
               ]);
           }
           //return  response()->json([$request->all()]);

           $request->isMethod('put') ? '' : $sAccount = StripeManager::createAccount($request);
           $data = $request->isMethod('put') ? Player::findOrFail($request->id) : new Player();
           $data->id = $request->input('id');
           $data->name = $request->input('name');
           $data->email = $request->input('email');
           $data->username = $request->input('username');
           $data->phone = $request->input('phone');
           $data->address = $request->input('address');
           $data->role = 'player';
           $data->status = '1';
           $request->isMethod('put') ? '' : $data->stripe_account = $sAccount;
           if ($request->isMethod('put')) {
                  if (empty($request->input('password'))) {
                   //
                } else {
                   $data->password = bcrypt($request->input('password'));
                }
          } else {
               $data->password = bcrypt($request->input('password'));
           }
           $request->isMethod('put') ? '' : EmailTemplateManager::welcomeMessage($request->input('name'), $request->input('email'));
           $data->save();
           $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "Account has been created successfully.", 'record' => $data]) :
           response()->json(['action' => true, 'message' => "Player has been created", 'record' => $data]);
           return $message;
        } catch (Exception $error) {
              return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
   }
   

   // Settings for Players
   public function setting(Request $request)
   {
       try {
           $data = Player::findOrFail(auth('api')->user()->id);
           $data->id = auth('api')->user()->id;
           $data->name = $request->input('name');
           $data->email = $request->input('email');
           //$data->Playername = $request->input('Playername');
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



   // Fetch Player By Id
   public function show($id)
   {
       try {
           return new Rplayer(Player::findOrFail($id));
       } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
       }
   }



   // Delete Players
   public function destroy($id)
   {
       try {
          $del = new Player();
          $del->whereIn('id', explode(",", $id))->delete();
          return response()->json(['action' => true, 'message' => "Selected Players has been deleted"]);
       } catch (Exception $error) {
          return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
       }
   }
   
   
   
   // Fetching Active Player
   public function activePlayer()
   {
       try {
           return Rplayer::collection(Player::where(['role' => 'player', 'status' => 1])->get());
       } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
       }
   }
   
   // Player Side Data 
   
   public function myProfile(){
       return response()->json(auth('api')->user());
   }
   
   public function myProfileUpdate(Request $request)
   {
       try {
               $validatedData = $request->validate([
                   'name' => 'required',
                   'email' => 'required|email|unique:users,email,'.auth('api')->user()->id,
                   'phone' => 'required|regex:/[0-9]{9}/|max:10|unique:users,phone,'.auth('api')->user()->id,
               ]);
           

           //$request->isMethod('put') ? '' : $sAccount = StripeManager::createAccount($request);
           $data = Player::findOrFail(auth('api')->user()->id);
           $data->id = auth('api')->user()->id;
           $data->name = $request->input('name');
           $data->email = $request->input('email');
           $data->phone = $request->input('phone');
           $data->address = $request->input('address');
           
            if (empty($request->input('password'))) {
               //
            } else {
               $data->password = bcrypt($request->input('password'));
            }
           $data->save();
           $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "Your data has been updated.", 'record' => $data]) :
           response()->json(['action' => true, 'message' => "Player has been created", 'record' => $data]);
           return $message;
        } catch (Exception $error) {
              return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
   }
   
// Fetch Data payment method
	public function paymentMethods(){
    	try{
        	$data = StripeManager::paymentMethod(auth('api')->user()->stripe_account);
        	return response()->json(['action' => true,'record' => $data]);
        } catch(Exception $error){
        	 return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }


// Fetch Data payment method
	public function Invoces(){
    	try{
        	$data = StripeManager::invoce(auth('api')->user()->stripe_account);
        	return response()->json(['action' => true,'record' => $data]);
        } catch(Exception $error){
        	 return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
   
   
   

}
