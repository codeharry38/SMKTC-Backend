<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class StockManager
{
    
    public static function fetchData($stock){
        $json  = file_get_contents("API-LINK/query?function=TIME_SERIES_INTRADAY&symbol=".$stock."&interval=1min&outputsize=full&apikey=API-KEY");
        $data = json_decode($json,true);
        $key = array_keys($data['Time Series (1min)']);
        return $data['Time Series (1min)'][$key[0]];
    }
    
    public static function closeValue($stock){
        $json  = file_get_contents("API-LINK/query?function=TIME_SERIES_INTRADAY&symbol=".$stock."&interval=1min&outputsize=full&apikey=API-KEY");
        $data = json_decode($json,true);
        $key = array_keys($data['Time Series (1min)']);
        return $data['Time Series (1min)'][$key[0]]['4. close'];
    }
    
    public static function fetch($symbol){
        try{
       $data = Http::withHeaders([
            'X-RapidAPI-Key' => 'API-HASH',
        	'X-RapidAPI-Host' => 'API-HOST'
            ])->get('https://API-HOST/intraday',[
        	'symbol' => $symbol,
        	'interval' => '1min',
	        'maxreturn' => '1'
        ]);
        $get = json_decode((string) $data->getBody(), true);
        return $get['Results'][0];
        }catch(Exception $e){
            return $e;
        }
    }

	public static function fetchFull($symbol){
        try{
       $data = Http::withHeaders([
            'X-RapidAPI-Key' => 'API-HASH',
        	'X-RapidAPI-Host' => 'API-HOST'
            ])->get('https://API-HOST/intraday',[
        	'symbol' => $symbol,
        	'interval' => '1min',
	        'maxreturn' => '1'
        ]);
        $get = json_decode((string) $data->getBody(), true);
        return $get['Results'];
        }catch(Exception $e){
            return $e;
        }
    }

}