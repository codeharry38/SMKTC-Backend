<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Claim;
use App\Ticket;
use App\Gameplay;
use Illuminate\Support\Facades\Http;
use App\Helpers\StockManager;
use App\GameplayStock;


class ClaimController extends Controller
{
    //
    
    public function TicketData($access_code){
        try {
            return new Claim(Ticket::where('access_code', $access_code)->first());
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    public function Claim(Request $request){
        try {
            $data = new Gameplay();
            $data->game = $request->input('game');
            $data->player = auth('api')->user()->id;
            $data->ticket = $request->input('ticket');
            $data->prize = $request->input('prize');
            $data->start_at = date("Y-m-d", strtotime($request->input('start_at')));
            $data->end_at = date("Y-m-d", strtotime($request->input('end_at')));
            $data->save();
            $this->setupStocks($data, $request->input('ticket'));
            $this->updateTicket($request->input('ticket'),auth('api')->user()->id);
           return response()->json(['action' => true, 'message' => 'Thank you']);
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
    private function setupStocks($gamePlay, $ticket){
        try{
            $data = array();
            foreach($this->getAssignedStock($ticket) as $stock):
                array_push($data,['gameplay' => $gamePlay['id'], 'game' => $gamePlay['game'], 'ticket' => $ticket, 'stock' => $stock]);
            endforeach;
            GameplayStock::insert($data);
            $this->updateOCLHV($gamePlay['id']);
            return true;
        }catch(Exception $error){
             return response()->json(['action' => false, 'message' => "Somthing went wrong while assining stocks, Please contact manager", 'error' => $error]);
        }
    }
    
    private function updateOCLHV($gameplay){
        try{
            $stockData = GameplayStock::where('gameplay',$gameplay)->with(['stockMeta' => function ($query) {
                return $query->select('id', 'symbol');
            }])->get();
            //$array = array();
            foreach($stockData as $sData):
                $fetched= StockManager::fetch($sData->stockMeta->symbol);
                $high = $fetched['High'];
                $low = $fetched['Low'];
                $open = $fetched['Open'];
                $close = $fetched['Close'];
                $volume = $fetched['Volume'];
                $stock_updated_at = $fetched['Date'];
                //array_push($array,['high' => $high, 'low' => $low, 'open' => $open, 'close' => $close, 'volume' => $volume]);
                $data = GameplayStock::findOrFail($sData->id);
                $data->id = $sData->id;
                $data->high = $high;
                $data->low = $low;
                $data->close = $close;
                $data->open = $open;
                $data->volume = $volume;
                $data->stock_updated_at = $stock_updated_at;
                $data->save();
            endforeach;
            return true;
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somthing went wrong while assining stocks, Please contact manager", 'error' => $error]);
        }
        
    }
    
    private function getAssignedStock(/*Ticket id */ $id){
        try{
                $data = Ticket::findOrFail($id);
                $selectedStock = str_replace(array('[',']'),'', $data->assign_symbol);
                return explode (",", $selectedStock);
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somthing went wrong while fetching stocks, Please contact manager", 'error' => $error]);
        }
    }
    
    public function startNow(Request $request){
        try {
             $data = new Gameplay();
             $data->status = true;
             $data->save();
             return response()->json(['action' => true, 'message' => "Your game has been started."]);
             
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    public function fetchSymbole($symbol){
        
        $data = Http::withHeaders([
            'X-RapidAPI-Key' => '40fb84b57cmsh5b41434482e28d7p153ec5jsnfa72446023f7',
        	'X-RapidAPI-Host' => 'apistocks.p.rapidapi.com'
            ])->get('https://apistocks.p.rapidapi.com/intraday',[
        	'symbol' => $symbol,
        	'interval' => '1min',
	        'maxreturn' => '1'
        ]);
        $get = json_decode((string) $data->getBody(), true)['Results'];
        return $get;
    }
    
    
    // Update Ticket after claim ticket --------------
    private function updateTicket($id, $player){
         try {
            $data = Ticket::findOrFail($id);
             $data->id = $id;
             $data->used_by = $player;
             $data->assign_to = $player;
             $data->is_used = true;
             $data->save();
             return true;
         } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
    
    
}