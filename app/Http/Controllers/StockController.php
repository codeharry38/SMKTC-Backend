<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Stock;
use App\StockList;
use App\GameplayStock;
use App\Helpers\StockManager;

class StockController extends Controller
{
    //
    
     // Fetch All Records
    public function index()
    {
        try {
            return Stock::collection(StockList::all());
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
     public function list(Request $request)
    {
        try {
            $notSelected = GameplayStock::where('gameplay',$request->gameplay)->pluck('stock');
            return Stock::collection(StockList::whereNotIn('id',$notSelected)->inRandomOrder()->get());
        } catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }

	public function runStockUpdate(){
    //ini_set('max_execution_time', 0);
    	try{
        	StockList::orderBy('id')->chunk(60, function ($records) {
       	 	foreach ($records as $record) {
            	$fetched= StockManager::fetchFull($record->symbol);
            	$high = $fetched[0]['High'];
            	$low = $fetched[0]['Low'];
            	$open = $fetched[0]['Open'];
            	$close = $fetched[0]['Close'];
            	$volume = $fetched[0]['Volume'];
            	$stock_updated_at = $fetched[0]['Date'];
            	$data = StockList::findOrFail($record->id);
            	$data->id = $record->id;
            	$data->high = $high;
            	$data->low = $low;
            	$data->close = $close;
            	$data->open = $open;
            	$data->volume = $volume;
            	$data->stock_updated_at = $stock_updated_at;
            	$data->full_data = $fetched;
            	$data->save();
        	}
        	sleep(240); //4 min rest
    	});
       }catch (Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
}