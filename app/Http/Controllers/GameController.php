<?php

namespace App\Http\Controllers;

use App\Http\Resources\Game as Rgame;
use App\Http\Resources\ExportTicket;
use App\Http\Resources\Billing;
use App\Game;
use App\Gameplay;
use App\Ticket;
use App\GameplayStock;
use App\GameWinner;
use App\Organization;
use App\StockList;
use Illuminate\Http\Request;
use App\Helpers\StripeManager;
use App\Helpers\EmailTemplateManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Helpers\StockManager;

class GameController extends Controller
{

	public function checkstock($stock){
    $fetched= StockManager::fetch($stock);
    return $fetched;
    }

	public function getTicketData(){
    	$ticketData = [11779, 11784, 11780, 11785, 11781, 11786, 11782, 11787, 11778, 11783];
    	$data = Ticket::whereIn('id', $ticketData)->pluck('assign_symbol');
    	//$final = explode(',', $data);
    	foreach($data as $f):
    	$data = explode(",", $f);
    	$string = str_replace(array('[',']'),'',$data);
    	$list = StockList::whereIn('id', $string)->pluck('symbol');
    	print_r($list);
    	echo "<br>____<br>";
    
    	endforeach;
    
    }
     // Fetch All Records
     public function index()
     {
         try {
             return Rgame::collection(Game::all());
         } catch (Exception $error){
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
 
     // Fetch Active Records
     public function activeRecord()
      {
          try {
              return Rgame::collection(Game::where('status', 1)->get());
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
             'start_at' => 'required',
             'end_at' => 'required',
             'entrance_deadline' => 'required',
             'cost' => 'required',
             'online_tickets' => 'required',
             'offline_tickets' => 'required',
             'free_tickets' => 'required',
             'organization' => 'required',
             'prize' => 'required',
            
         ]);
        if ($request->isMethod('put')) {
            //
         } else {
            $sProduct = StripeManager::createProduct($request);
         }
         $data = $request->isMethod('put') ? Game::findOrFail($request->id) : new Game();
         $data->id = $request->input('id');
         $data->name = $request->input('name');
         $data->start_at = $request->input('start_at');
         $data->end_at = $request->input('end_at');
         $data->entrance_deadline = $request->input('entrance_deadline');
         $data->cost = $request->input('cost');
         $data->sponsor = $request->input('sponsor');
         $data->online_tickets = $request->input('online_tickets');
         $data->offline_tickets = $request->input('offline_tickets');
         $data->reserve_tickets = $request->input('reserve_tickets');
         $data->free_tickets = $request->input('free_tickets');
         $data->organization = $request->input('organization');
         $data->media = $request->input('media');
         $data->offermedia = $request->input('offermedia');
         $data->prize = $request->input('prize'); 
         $data->offer = $request->input('offer');
         $data->status = $request->input('status');
         if($request->isMethod('post')){
             $data->stripe_product = $sProduct['stripe_product'];
             $data->stripe_product_price = $sProduct['stripe_product_price'];
         }
         $data->is_daily_prize = $request->input('is_daily_prize');
         $data->save();
            // Fetch Organization Name;
            $organization = Organization::findOrFail($request->input('organization'))->name;

        if ($request->isMethod('post'))
        {
            $this->ticketGenrator($data, $organization);
        }
         $message = $request->isMethod('put') ? response()->json(['action' => true, 'message' => "Game has been updated", 'record' => new Rgame($data)]) :
         response()->json(['action' => true, 'message' => "Game has been created", 'record' => new Rgame($data)]);
         return $message;
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
     }
        
    // Game info at Front_end
    public function gameInfo($id){
        try {
             return new Rgame(Game::findOrFail($id));
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
    }

    // Find by id
     public function show($id)
     {
         try {
             return new Rgame(Game::findOrFail($id));
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
 
     // update Status for Game
     public function statusUpdate(Request $request)
     {
         try {
             $data = Game::findOrFail($request->id);
             $data->id = $request->input('id');
             $data->status = $request->input('status');
             $data->save();
             $message = $request->status == 0 ? 'Game has been Deactivated' : 'Game has been Activated';
             return response()->json(['message'=> $message, 'action' => true]);
         } catch (Exception $error) {
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }
 
 
     // Delete Games
     public function destroy($id)
     {
         try {
            $del = new Game();
            $del->whereIn('id', explode(",", $id))->forcedelete();
            $delTicket = new Ticket();
            $delTicket->whereIn('game', explode(",", $id))->forcedelete();
            $delGameplay = new Gameplay();
            $delGameplay->whereIn('game', explode(",", $id))->forcedelete();
            $delGameplay_stock = new GameplayStock();
            $delGameplay_stock->whereIn('game', explode(",", $id))->forcedelete();
            $delGame_Prize = new GameWinner();
            $delGame_Prize->whereIn('game', explode(",", $id))->forcedelete();
            
            return response()->json(['action' => true, 'message' => "Selected Game has been deleted"]);
         } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
     }




    // ________________ Fetch Full Detail for the game ______________________
        
        public function fullGameDetails($id){
            $d = game::findOrFail($id);
            $saveData = array(
             'name' => $d->name,
             'game' => $d->id,
             'gameplay' => $this->latestGame($d->id),
             'totalPlayer' => $this->screenCount($d->id)
             );
            return $saveData;
        }
        
        private function latestGame($id){
        try{
            return  Game::join('gameplays', 'gameplays.game', '=', 'games.id')->where("gameplays.status",1)
            ->join('gameplay_stocks', 'gameplay_stocks.gameplay', '=', 'gameplays.id')
            ->join('users', 'users.id', '=', 'gameplays.player')
            ->join('tickets', 'tickets.id', '=', 'gameplays.ticket')
            ->select(DB::raw('SUM(gameplay_stocks.total_value) as grand_total'), 'gameplay_stocks.gameplay as gameplayId', 'users.name as player','users.email as email','tickets.access_code as access_code','tickets.ticket_number as ticket_number','tickets.ticket_type as ticket_type')
            ->groupBy('gameplay_stocks.gameplay','users.name','users.email','tickets.access_code','tickets.ticket_number','tickets.ticket_type')->orderBy('grand_total','DESC') 
            ->where('games.id',$id)
            ->get();
        } catch (Exception $error){
            return $error;
        }
    }
    
    private function screenCount($game){
        return gameplay::where('game',$game)->where('status',1)->count();
    }

     // __________________ Ticket Section __________________

    private function ticketGenrator($data = 0, $org)
    {

      //  try {
           
            $totalCount = $data->online_tickets + $data->offline_tickets + $data->free_tickets + $data->reserve_tickets;
                for ($i=1; $i<=$totalCount; $i++) {
                    $saveData[] = array(
                    'game' => $data['id'],
                    'access_code' => $this->accessCodeGenrator($org),
                    'sponsor' => $data['sponsor'],
                    'ticket_number' => $i,
                    'cost' => $data['cost'],
                    'organization' => $data['organization'],
                    'prize' => $data['prize'],
                    'start_at' => $data['start_at'],
                    'end_at' => $data['end_at'],
                    'entrance_deadline' => $data['entrance_deadline'],
                    'assign_symbol' => $this->pickStock()
                    );
                }

                $ticket = new Ticket();
   				foreach(array_chunk($saveData,1000) as $d):
                	$ticket->insert($d);
    			endforeach;
				$this->printTicketAssign($data->offline_tickets, $data->id);
                $this->reserveTicketAssign($data->reserve_tickets, $data->id);
    			$this->onlineTicketAssign($data->online_tickets, $data->id);
                $this->freeTicketAssign($data->free_tickets, $data->id);
                
               
                return true;
       // } catch (Exception $error) {
         //   return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
       //  }
    }



    // Generate Free Tickets
    private function freeTicketAssign($count, $id)
    {
        try {
            //for ($i=1; $i<=$count; $i++) {
                Ticket::where('game', '=', $id)->where('ticket_type', '=', 'not_type')->take($count)->update(['ticket_type' => 'free', 'cost' => '0']);
            //}
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
    }

	// Generate Reserve Tickets
	 private function reserveTicketAssign($count, $id)
    {
        try {
         //   $records = [];
          //  for ($i=1; $i<=$count; $i++) {
                Ticket::where('game', '=', $id)->where('ticket_type', '=', 'not_type')->take($count)->update(['ticket_type' => 'reserve']);
               // Ticket::where('game', '=', $id)->where('ticket_type', '=', 'not_type')->update(['ticket_type' => 'online']);
           // }
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
    }


    // Generate Online Tickets
    private function onlineTicketAssign($count, $id)
    {
        try {
         //   $records = [];
          //  for ($i=1; $i<=$count; $i++) {
                Ticket::where('game', '=', $id)->where('ticket_type', '=', 'not_type')->take($count)->update(['ticket_type' => 'online']);
               // Ticket::where('game', '=', $id)->where('ticket_type', '=', 'not_type')->update(['ticket_type' => 'online']);
           // }
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
    }



    // Generate Print Tickets
    private function printTicketAssign($count, $id)
    {
        try {
           // for ($i=1; $i<=$count; $i++) {
                Ticket::where('game', '=', $id)->where('ticket_type', '=', 'not_type')->take($count)->update(['ticket_type' => 'offline','is_paid' => 1]);
              //  Ticket::where('game', '=', $id)->where('ticket_type', '=', 'not_type')->update(['ticket_type' => 'offline']);
          //  }
           
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
         }
    }



    // Create Access_Code
    private function accessCodeGenrator($data)
    {
        $char = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
       // try {
       
            //$trimData = str_replace("O","",strtoupper($data));
            $result = mb_substr($data, 0, 3);
            //example: 2EY-5H9
            $full = "";
            $number = rand(1, 9);
            $full .= $number;

            $letter = $char[rand(0, 24)];
            $full .= $letter;

            $letter = $char[rand(0, 24)];
            $full .= $letter;

            $full .= "-";

            $letter = $char[rand(0, 24)];
            $full .= $letter;

            $letter = $char[rand(0, 24)];
            $full .= $letter;

            $number = rand(9, 1);
            $full .= $number;

            return strtoupper($result.''.$full);

        //} catch (Exception $error) {
        //    return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
      //   }
    }
    
    // Pick Random Stocks
    private function pickStock(){
        try{
        $data = StockList::inRandomOrder()->limit(5)->pluck('id');
        $final = implode(",",array($data));
        return $final;
        } catch (Exception $error) {
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
    // Player Side
    public function activGame()
    {
        try {
            return Rgame::collection(Game::where('status', 1)->get());
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        } 
     }
    
    public function leaderBoard($id){
        try{
            return  Game::join('gameplays', 'gameplays.game', '=', 'games.id')->where("gameplays.status",1)
            ->join('gameplay_stocks', 'gameplay_stocks.gameplay', '=', 'gameplays.id')
            ->join('users', 'users.id', '=', 'gameplays.player')
            ->join('tickets', 'tickets.id', '=', 'gameplays.ticket')
            ->select(DB::raw('SUM(gameplay_stocks.total_value) as grand_total'), 'gameplay_stocks.gameplay as gameplayId', 'users.name as player','tickets.ticket_number as ticket',DB::raw('DATE_FORMAT(gameplays.updated_at, "%m/%d/%Y %h:%i %p") as date'))
            ->groupBy('gameplay_stocks.gameplay','users.name','tickets.ticket_number','gameplays.updated_at')->orderBy('grand_total','DESC')
            ->where('games.id',$id)
            ->get();
            
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
            
        }
    }
    
    
    
    // Billings
    
    public function bill()
    {
        try {
            return Billing::collection(Game::all());
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    public function invoice($id)
    {
        try {
             return new Billing(Game::findOrFail($id));
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    public function exportTicket($id){
        try {
             return  Game::where('games.id',$id)
            ->join('tickets', 'tickets.game', '=', 'games.id')
            ->select('tickets.access_code as Access_Code','tickets.ticket_number as Ticket_Number' , 'tickets.ticket_type as Ticket_Type')->get();
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
    // Invoce send
	public function sendIt($game){
    try {
            $senddata = new Billing(Game::findOrFail($game));
        	return response()->json($senddata);
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    


    
     public function sendInvoice(Request $request){
        try {
            //$senddata = new Billing(Game::findOrFail($request->input('game')));
        	$senddata = http::get(route('sendit', ['game' =>  $request->input('game')]), [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $request->header('Authorization'),
                ],
            ]);
            EmailTemplateManager::sendInvoce(json_decode((string) $senddata->getBody(),true),$request->input('email'),$request->input('subject'),$request->input('message'));
             return response()->json(['action' => true, 'message' => "Email has sent."]);
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
}
