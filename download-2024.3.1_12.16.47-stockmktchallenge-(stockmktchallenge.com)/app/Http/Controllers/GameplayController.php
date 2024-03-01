<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\Claim;
use App\Http\Resources\Gameplay as Rgameplay;
use App\Http\Resources\ContinueGameplay;
use App\Http\Resources\Mygame;
use App\Ticket;
use App\Gameplay;
use App\Gamelog;
use App\StockList;
use App\Helpers\StockManager;
use App\GameplayStock;

class GameplayController extends Controller
{
    // Fetch Ticket Data -------------------------------------
     public function TicketData($access_code) {
        try {
            // Claim Resource check all the possibilities that player is eligible to claim this ticket.
            $data = Ticket::where('access_code', $access_code)->first();
            if($data == ''){
                return $data;
            }else{
                return new Claim($data);
            }
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    // Player Claim the Ticket -------------------------------------
    public function claim(Request $request) {
        try {
             $data = new Gameplay();
             $data->game = $request->input('game');
             $data->player = auth('api')->user()->id;
             $data->ticket = $request->input('ticket');
             $data->stock_one = $this->assignStock($request->input('ticket'),0);
             $data->stock_two = $this->assignStock($request->input('ticket'),1);
             $data->stock_three = $this->assignStock($request->input('ticket'),2);
             $data->stock_four = $this->assignStock($request->input('ticket'),3);
             $data->stock_five = $this->assignStock($request->input('ticket'),4);
             $data->prize = $request->input('prize');
             $data->start_at = $request->input('start_at');
             $data->end_at = $request->input('end_at');
             $data->save();
             $this->updateTicket($request->input('ticket'),auth('api')->user()->id);
             return response()->json(['action' => true, 'message' => 'Thank you']);
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    // This will make game status to true.
    public function start(Request $request) {
        try {
            $this->setupCalc($request->id);
            $data = Gameplay::findOrFail($request->id);
            $data->id = $request->input('id');
            $data->status = true;
            $data->save();
            return response()->json(['action' => true, 'message' => "Your game has been started."]);
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    // Fetch Games By Player -------------------------------------
    public function myGame() {
        try {
            return Mygame::collection(Gameplay::where('player', auth('api')->user()->id)->get());
        } catch (Exception $error){
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    // Fetching the current game data as requsted from player -------------------------------------
    public function continue_game($id){
        try {
            return new ContinueGameplay(Gameplay::findOrFail($id));
        } catch (Exception $error){
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    // Mannual Pick Stock and create Logs -------------------------
    
    public function manualpick(Request $request){
         try {
            $newStock = StockList::findOrFail($request->newStock);
            $data = GameplayStock::where('id',$request->selectedStock)->with(['stockMeta' => function ($query) {
                return $query->select('id', 'symbol');
            }])->with(['gameplayMeta' => function ($query) {
                return $query->select('id', 'status');
            }])->first();
         
         	$fetched= StockList::findOrFail($newStock->id);
            $data->id = $data->id;
            $data->high = $fetched->high;
            $data->low = $fetched->low;
        	$data->stock = $request->newStock;
            $data->close = $fetched->close;
            $data->open = $fetched->open;
            $data->volume = $fetched->volume;
            $data->stock_updated_at = $fetched->stock_updated_at;
        	$data->shares = $data->gameplayMeta->status == 0 ? 0 : $data->total_value / $fetched->close;
            $data->share_rate = $data->gameplayMeta->status == 0 ? 0 :  $fetched->close;
            $data->total_value = $data->gameplayMeta->status == 0 ? 20000 :  $data->shares * $fetched->close;
            $data->save();
            return response()->json(['action' => true, 'message' =>  'Stock has been replaced.']);
        } catch (Exception $error){
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    // autopic Stock and create Logs -------------------------
    
    public function autopick(Request $request){
        try {
            
            $data = GameplayStock::where('id',$request->selectedStock)->with(['stockMeta' => function ($query) {
                return $query->select('id', 'symbol');
            }])->with(['gameplayMeta' => function ($query) {
                return $query->select('id', 'status');
            }])->first();
            $newstock = $this->selectSingleStock($data);
            $stockData = StockList::findOrFail($newstock);
            //$fetched= StockManager::fetch($stockData->symbol);
        
        	$fetched= StockList::findOrFail($stockData->id);
            $data->id = $data->id;
            $data->high = $fetched->high;
            $data->low = $fetched->low;
        	$data->stock = $newstock;
            $data->close = $fetched->close;
            $data->open = $fetched->open;
            $data->volume = $fetched->volume;
            $data->stock_updated_at = $fetched->stock_updated_at;
        	$data->shares = $data->gameplayMeta->status == 0 ? 0 : $data->total_value / $fetched->close;
            $data->share_rate = $data->gameplayMeta->status == 0 ? 0 :  $fetched->close;
            $data->total_value = $data->gameplayMeta->status == 0 ? 20000 :  $data->shares * $fetched->close;
            $data->save();
            return response()->json(['action' => true, 'message' =>  'Stock has been replaced.']);
        } catch (Exception $error){
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    // Calculate the value of requested stock as per last updated data
    private function currentStockValue($data,$stock){
        $stockItems = $data[$stock.'_item'];
        $stockValue = $data[$stock.'_current_value'];
        return $stockItems * $stockValue;
    }
    
    // Update Ticket after claim ticket --------------
    private function updateTicket($id, $player){
         try {
            $data = Ticket::findOrFail($id);
             $data->id = $id;
             $data->used_by = $player;
             $data->is_used = true;
             $data->save();
         } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    // Update Ticket after starting game. --------------------- 
    private function gameActive($id){
        try {
         $data = Ticket::findOrFail($id);
         $data->id = $id;
         $data->is_game_ative = true;
         $data->save();
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    //Assign Stocks  --------------------
    private function assignStock($id,$position){
        $data = Ticket::where('id',$id)->pluck('assign_symbol')->first();
        $data = trim($data, '[]');
        $data = explode(',',$data);
        return $data[$position];
    }
    
    
    // update stock values and itmes on Starting Game ------------------------

/*
 * 
 * 
      $stockData = GameplayStock::whereIn('gameplay',$gameplay)->select('id', 'stock')->get();
            //$array = array();
            foreach($stockData as $sData):
                $fetched= StockList::findOrFail($sData->stock);
                $data = GameplayStock::findOrFail($sData->id);
                $data->id = $sData->id;
                $data->high = $fetched->high;
                $data->low = $fetched->low;
                $data->close = $fetched->close;
                $data->open = $fetched->open;
                $data->volume = $fetched->volume;
                $data->stock_updated_at = $fetched->stock_updated_at;
                $data->share_rate = $fetched->close;
                $data->total_value = $data->shares * $fetched->close;
                $data->save();
            endforeach;
 * 
 * 
 */
    private function setupCalc($gameplay){
          try{
               $stockData = GameplayStock::where('gameplay',$gameplay)->get();
            //$array = array();
            foreach($stockData as $sData):
          		$fetched= StockList::findOrFail($sData->stock);
                $data = GameplayStock::findOrFail($sData->id);
                $data->id = $sData->id;
                $data->high = $fetched->high;
                $data->low = $fetched->low;
                $data->close = $fetched->close;
                $data->open = $fetched->open;
                $data->volume = $fetched->volume;
                $data->stock_updated_at = $fetched->stock_updated_at;
                $data->shares = 20000 / $fetched->close;
                $data->share_rate = $fetched->close;
                $data->save();
            endforeach;
            return true;
            //return response()->json(['action' => true,'resutl' => $data]);
          } catch(Exception $error){
              return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
          }
    }
    
    // update stock values and itmes on Change Stocks ------------------------
    public function setupsinglecalc($id, $stock, $money){
          try{
            $data = Gameplay::findOrFail($id);
            $stock_value = StockManager::closeValue($data->{$stock.'_meta'}['symbol']);
            $data->{$stock.'_item'} = $money / $stock_value;
            $data->{$stock.'_current_value'} = $stock_value;
            $data->save();
            //return response()->json(['action' => true,'resutl' => $data]);
          } catch(Exception $error){
              return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
          }
    }
    
    // Creating Gamelogs ---------------------------
    private function genrateLog($gameplay, $oldStock, $selectedStock,$action_type){
        try{
            $data = new Gamelog();
            $data->game_play = $gameplay->id;
            $data->game = $gameplay->game;
            $data->player = $gameplay->player;
            $data->ticket = $gameplay->ticket;
            $data->stock_one = $gameplay->stock_one;
            $data->stock_two = $gameplay->stock_two;
            $data->stock_three = $gameplay->stock_three;
            $data->stock_four = $gameplay->stock_four;
            $data->stock_five = $gameplay->stock_five;
            $data->replace_stock = $oldStock;
            $data->selected_stock = $selectedStock;
            $data->prize = $gameplay->prize;
            $data->stock_one_value = $gameplay->stock_one_value;
            $data->stock_two_value = $gameplay->stock_two_value;
            $data->stock_three_value = $gameplay->stock_three_value;
            $data->stock_four_value = $gameplay->stock_four_value;
            $data->stock_five_value = $gameplay->stock_five_value;
            $data->action_type = $action_type;
            $data->action_at = now();
            $data->save();
        } catch (Exception $error){
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    // Select Single stock for auto pic
    private function selectSingleStock($data){
        $notSelected = GameplayStock::where('gameplay', $data['gameplay'])->pluck('stock');
        $selectedStock = StockList::whereNotIn('id',$notSelected)->inRandomOrder()->first();
        return $selectedStock->id;
    }
    
    
    
    // Use it for Backend afte this
    
    
    
    public function selectActiveByPlayer($id){
        try{
           return Rgameplay::collection(Gameplay::where('player', $id)->whereDate('end_at', '>', now())->get());
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    public function selectInactiveByPlayer($id){
        try{
           return Rgameplay::collection(Gameplay::where('player', $id)->whereDate('end_at', '<', now())->get());
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
    
    
    
    
    
}
