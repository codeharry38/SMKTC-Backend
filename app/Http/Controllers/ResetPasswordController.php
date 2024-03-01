<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\User;
use App\Ticket;
use App\ForgotPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Helpers\EmailTemplateManager;

class ResetPasswordController extends Controller
{
    public function sendTicket(Request $request){
        //return $request->input('message');
        try{
            $data = Ticket::findOrFail($request->id);
            EmailTemplateManager::sendTicket($data, $request->email, $request->input('message'));
            return response()->json(['action' => true, 'message' => "Ticket has been sent successfully. Check spam folder if you do not receive your ticket."]);
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    public function ForgotTokenGenrate(Request $request){
        try{
            $this->validate($request,[
                'email' => 'required|email',
             ]);
            $checkUser = User::where('email',$request->email)->first();
           
            if(empty($checkUser) || $checkUser ==''){
                return response()->json(['action' => false, 'message' => "Entered email not registed with us."]);
            }
            else{
                $tokenGenrate = new ForgotPassword();
                $tokenGenrate->email = $checkUser->email;
                $tokenGenrate->phone = $checkUser->phone;
                $tokenGenrate->type = 'email';
                $tokenGenrate->token = Str::random(32);
                $tokenGenrate->userid = $checkUser->id;
                $tokenGenrate->save();
                // return $tokenGenrate;
                // Templates and Email Sent
                if($checkUser->role == 'player'){
                    $check = EmailTemplateManager::resetPassword($tokenGenrate,'player');
                } else {
                    $check = EmailTemplateManager::resetPassword($tokenGenrate,'manager');
                }
                return response()->json(['action' => true, 'message' => "Reset password link has been sent to your registed email. Check spam too."]);
            }
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

    public function ForgotTokenCheck(Request $request){
        try{
            $checkToken = ForgotPassword::where(['email' => $request->query('email'), 'type' => $request->query('ty'), 'token' => $request->query('tc')])->first();
            if(empty($checkToken) || $checkToken ==''){
                return response()->json(['action' => false, 'message' => "Wrong or Expired Link!"]);
            }
            else{
                $UpdateUser = User::findOrFail($checkToken->userid);
                $UpdateUser->id = $checkToken->userid;
                $UpdateUser->password = bcrypt($request->input('password'));
                $UpdateUser->save();
                $this->removeTocken($checkToken);
                return response()->json(['action' => true, 'message' => "Password has been updated."]);
            }
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    private function removeTocken($data){
        try {
            $del = new ForgotPassword();
            $del->where('id', $data->id)->delete();
            return true;
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
}