<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Http\Resources\GameWinner as RGameWinner;
use App\Game;
use App\Gameplay;
use App\GameplayStock;
use App\Helpers\StockManager;
use Illuminate\Http\Request;
use App\StockList;
//use Illuminate\Support\Sleep;
use Session;
class StockUpdateController extends Controller
{
   
   public function updateStocks(Request $request){
       try{
       		$count = Game::where('status',true)->where('is_end',0)->whereDate('start_at', '<=', Carbon::now()->toDateTimeString())->pluck('id');
        	return $this->updateGamePlay($count);
       } catch (Exception $error){
            return $error;
        }
   }
    
    private function updateGamePlay($game){
   // ini_set('max_execution_time', 0);
    
        $gameplay = Gameplay::whereIn('game',$game)->where('status',1)->pluck('id');
    	Gameplay::whereIn('game',$game)->update(['updated_at' => Carbon::now()]);
    
    	//return $gameplay;
    //die();
        	//foreach($gameplay as $d):
            	return $this->setupCalc($gameplay);
        	//endforeach;
        
    }
    
    private function setupCalc($gameplay){
          try{
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
            return 'updated';
          } catch(Exception $error){
              return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
          }
    }
}