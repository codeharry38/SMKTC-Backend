<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use App\Exception;

class EmailTemplateManager
{
    
    
    
    /* Welcome Message */
    
    public static function welcomeMessage($name,$email){
        try{
            
            $subject = 'Welcome to the StockMktChallenge.com game';
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $htmlTemplate = '<h2 style="text-transform:capitalize;">Hi '.$name.'</h2>
            <br>
            <p style="font-size:25px;">"Thank you for playing the <a href="URL">Company Name</a> game. You can view and trade your stocks at any time. <br> <span style="font-weight:bold;color:red;">Good luck!</span></p>';
        if(mail($email,$subject,$htmlTemplate,$headers)){
            return 'true';
        }
            return 'Somthing went wrong! Contact to the administrator';
        } catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
    
    
    /*___________ RESET PASSWORD _________________ */
    
    public static function resetPassword($data, $userType = 0){
        if($userType == 'player'){
             self::resetPlayerPassword($data);
        } else if ($userType == 'manager'){
             self::resetUserPassword($data);
        } else {
            throw new Exception("Error = Class::EmailTemplateManager => Please provide valid usertype for reset password method.");
        }
    }
    
    /*__________ SEND TICKET TO FRIENDS _________________ */
    
    public static function sendTicket($data, $email, $friend_message){
        try{
            //return self::ticketTemplate($data , $friend_message);
            $subject = 'Play and win ticket.';
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        if(mail($email,$subject,self::ticketTemplate($data,$friend_message),$headers)){
            return 'true';
        }
            return 'Somthing went wrong! Contact to the administrator';
        } catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
        
    }
    
   /*__________ SEND INVOICE  _________________ */
    
    public static function sendInvoce($data, $email, $subject, $message){
        try{
            //return $data;// self::invoiceTemplate($data,$message);
            $subject = $subject;
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        if(mail($email,$subject,self::invoiceTemplate($data,$message),$headers)){
            return 'true';
        }
            return 'Somthing went wrong! Contact to the administrator';
        } catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
        
    
    
    // Used In the same Class
    
    private static function resetPlayerPassword($data){
        $subject = 'Password Reset Request';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message = '<h4>Someone Request for reset pasword. If this is not you then ignore this email</h4><br><br><a href="URL?email='.$data['email'].'&ty=email&tc='.$data['token'].'" style="text-decoration:none;padding:10px 15px;color:#fff;background:#141344;border-radius:15px; margin:20px 0px !important;">Set new password</a>';
        if(mail($data->email,'subject',$message,$headers)){
            return true;
        } else {
            throw new Exception("Error = Function::mail => Mail Server is not working, setup smtp or mail server to send emails.");
        }
    }
    
    private static function resetUserPassword($data){
        $subject = 'Password Reset Request';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message = '<h4>Someone Request for reset pasword. If this is not you then ignore this email</h4><br><br><a href="URL?email='.$data['email'].'&ty=email&tc='.$data['token'].'" style="text-decoration:none;padding:10px 15px;color:#fff;background:#141344;border-radius:15px;">Set new password</a>';
        if(mail($data->email,'subject',$message,$headers)){
            return true;
        } else {
            throw new Exception("Error = Function::mail => Mail Server is not working, setup smtp or mail server to send emails.");
        }
    }
    
    private static function ticketTemplate($data, $friend_message){
        if($friend_message == ''){
            $message_from_friend = '';
        }else{
            $message_from_friend = '<p class="SenderMessage"><i>Message from sender: "'.$friend_message.'"</i></p>';
        }
        
        $template = '<!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <title>Ticket From Friend.</title>';
        $template .= '<style>
            *{
                box-sizing:border-box;
                font-family:arial !important;
                text-transformation:capitalize !important;
                margin:0px !important;
            }
            .ticket {
              display:block;
              border-radius: 15px;
              display: inline-block;
              text-align: left;
              text-transform: uppercase;
              width:350px;
              max-width:350px;
              float:left;
              content:"";
              box-sizing:border-box;
            }
            
            .ticket.light {
              	background: url("https://apibackend.stockmktchallenge.com/public/images/bg-dark.jpg") repeat;
            	 color: rgb(255, 255, 255);
            	 box-sizing:border-box;
            }

            .ticket-body {
              border-bottom: 1px dashed rgb(255, 255, 255);
              padding: 15px 20px;
              position: relative;
              box-sizing:border-box;
            }
            
            .ticket-body p {
              color: #ffffff;
              font-size: 12px;
              margin:0px;
              box-sizing:border-box;
            }
            
            .ticket-body:before, .ticket-body:after {
              background-color: rgb(255, 255, 255);
              border-radius: 100%;
              content: "";
              height: 15px;
              position: absolute;
              top: 100%;
              width: 20px;
              box-sizing:border-box;
            }
            
            .ticket-body:before {
              left: 0;
              transform: translate(-70%,-45%);
            }
            
            .ticket-body:after {
              right: 0;
              transform: translate(70%,-45%);
              box-sizing:border-box;
            }
            
            .disclaimer {
                color: #ffffff;
                font-size: 14px;
                font-style: italic;
                line-height: 1.25;
                padding: 15px 20px;
                text-transform: none;
                box-sizing:border-box;
            }
            
            .customTicketButton{
                padding:10px 15px;
                width:100% !important;
                background-color: #c6119d !important;
                color:#fff !important;
                border-radius: 15px !important;
                font-size: 12px !important;
                text-transform: capitalize !important;
                font-weight: 300 !important;
                margin:0px auto !important;
                text-decoration: none !important;
                text-align:center !important;
                box-sizing:border-box;
                display:block;
            }
            .gameTitleTicket{
                text-align:center;
                font-size:12px;
                padding:5px;
                box-sizing:border-box;
                background:#222347; 
                border-radius:20px;
                margin-bottom:20px;
            }
            
            .access_code{
              color:rgb(255, 255, 255);
              font-size:15px !important;
              font-weight: bold;
              padding:5px 0px;
              text-transform:uppercase !important;
              box-sizing:border-box;
            }
            .offerChip{
                font-size:12px !important;
                color:#fff;
                padding:2px 10px;
                border:1px solid #ddd;
                font-weight:300;
                border-radius:12px;
                display:inline; 
                margin-right:10px;
                line-height:30px;
                text-transform: capitalize !important;
                box-sizing:border-box;
            }
            .gameTicketNo{
                font-size:12px !important;
                font-weight:500 !important;
                font-color:#fff !important;
            }
            .SenderMessage{
                color:red !important;
                font-weight:bold !important;
                font-size:15px !important;
                font-style:italic !important;
            }
        </style>
    </head>';
    $template .='
    <body>
        '.$message_from_friend.'
    <br><br><br>
        <div class="ticket light">
            <div class="ticket-body">
                <p class="gameTitleTicket">'.$data['gameMeta']['name'].'</p>
                <br>
                <p class="gameTicketNo">Ticket No. '.$data['ticket_number'].'</p>
                <h4 class="access_code">'.$data['access_code'].'</h4>
                <p class="offerChip"> Start Date: '.date("Y-m-d", strtotime($data['start_at'])).'</p>
                <p class="offerChip"> End Date: '.date("Y-m-d", strtotime($data['end_at'])).' </p>
                <p class="offerChip"> Last Entrance: '.date("Y-m-d", strtotime($data['entrance_deadline'])).' </p>
            </div>
            <div class="footer">
                <div class="disclaimer">
                    <a href="URL/loading/?s_t=claim&a_c='.$data['access_code'].'" class="customTicketButton">Claim Now</a>
                </div>
            </div>
        </div>
    </body></html>';
    
        return $template;
        
    }
    
    private static function invoiceTemplate($data, $friend_message){
        
        if($friend_message == ''){
            $message_from_friend = '';
        }else{
            $message_from_friend = '<p class="SenderMessage"><i>Message from sender: "'.$friend_message.'"</i></p>';
        }
        
        $template = '<!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <title>Invoce</title>';
        $template .= '<style>
            *{
                box-sizing:border-box;
                font-family:arial !important;
                text-transformation:capitalize !important;
                margin:0px !important;
                font-size:14px;
            }
            
            .SenderMessage{
                color:red !important;
                font-weight:bold !important;
                font-size:15px !important;
                font-style:italic !important;
            }
            .invoiceContainer{
                width:800px;
                padding:10px;
                display:block;
                margin:0px auto;
                conent:"";
            }
            .inTable{
                border:1px solid #dfdfdf;
                width:100% !important;
                display:table;
                padding:10px;
            }
            .inTable tr{
                
                padding:10px !important;
                border-bottom:1px solid #dfdfdf;   
            }
            .inTable tr td:last-child{
                text-align:right !important;
            }
            .inTable tr td:first-child{
                text-align:left !important;
            }
            .inTable tr th:last-child{
                text-align:right !important;
            }
            .inTable tr th:first-child{
                text-align:left !important;
            }
            .inTable tr td:nth-child(2){
                text-align:center;
            }
            .mb-10{
                margin-bottom:10px
            }
            .text-right{
                text-align:right !important;
            }
        </style>
    </head>';
    $template .='
    <body>
        '.$message_from_friend.'
    <br><br><br>
        <div class="invoiceContainer">
        <table class="inTable">
            <tr>
                <td><b>Game: '.$data["name"].'</b>
                <br><br></td>
                <td colspan="2" class="text-right"><b>Organization: '.$data["organization"]["name"].'</b><br><br></td>
            </tr>
            <br>
            <tr>
                <td colspan="3">Game Start At: '.$data["start_at"].'</td>
            </tr>
            <tr>
                <td colspan="3">Last Entrance: '.$data["entrance_deadline"].'</td>
            </tr>
            <tr>
                <td colspan="3">Game End At: '.$data["end_at"].'</td>
            </tr>
            <tr>
                <td colspan="3">Entry Cost: $ '.(number_format($data["cost"], 2, ".","")).' <br><br></td>
            </tr>
            
            <tr>
                <td><b>Generated Tickets</b></td>
                <td><b>Paid Tickets</b></td>
                <td class="text-right"><b>Claimed Tickets</b></td>
            </tr>
            <tr>
                <td>Online Tickets: '.$data["online_tickets"].'</td>
                <td>Online Paid: '.$data["online_paid"].'</td>
                <td class="text-right">Online Claimed: '.$data["online_claim"].'</td>
            </tr>
            <tr>
                <td>Reserve Tickets: '.$data["reserve_tickets"].'</td>
                <td>Reserve Paid: '.$data["reserve_paid"].'</td>
                <td class="text-right">Reserve Claimed: '.$data["reserve_claim"].'</td>
            </tr>
            <tr>
                <td>Offline Tickets: '.$data["offline_tickets"].'</td>
                <td></td>
                <td class="text-right">Offline Claimed: '.$data["offline_claim"].'</td>
            </tr>
            <tr>
                <td>Free Tickets: '.$data["free_tickets"].'</td>
                <td> </td>
                <td class="text-right">Free Claimed: '.$data["free_claim"].'</td>
            </tr>
            <tr>
                <td><b>Total Generated: '.$data["numberoftickets"].'</b> <br><br></td>
                <td><b>Total Paid: $ '.number_format(($data["online_paid"] + $data["reserve_paid"]) * $data["cost"], 2, ".","").'</b> <br><br></td>
                <td class="text-right"><b>Total Claimed: '.($data["online_claim"] + $data["free_claim"] + $data["offline_claim"] + $data["reserve_claim"]).' </b><br><br></td>
            </tr>
            
            <tr>
                <td><b>Stock Registered</b></td>
                <td colspan="2" class="text-right"><b>Claimed Stocks</b></td>
            </tr>
            <tr>
                <td>Total Registered:'.(($data["online_tickets"] + $data["free_tickets"] + $data["offline_tickets"] + $data["reserve_tickets"]) * 5).'</td>
                <td colspan="2" class="text-right">Total Claimed: '.(($data["online_claim"] + $data["free_claim"] + $data["offline_claim"]  + $data["reserve_claim"]) * 5).'</td>
            </tr>
        </tabel>
        </div>
    </body></html>';
    
        return $template;
        
    }
    
    
   
    

}