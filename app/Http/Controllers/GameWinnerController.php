<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Http\Resources\GameWinner as RGameWinner;
use App\Game;
use App\Gameplay;
use App\PrizeMeta;
use App\GameWinner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameWinnerController extends Controller
{
    public function index(){
        try{
            return RGameWinner::collection(GameWinner::all());
        }catch(Exception $error){
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    public function winners($id){
        try{
            return RGameWinner::collection(GameWinner::where('game',$id)->where('is_daily', 0)->orderBy('position_type')->orderBy('position')->get());
        }catch(Exception $error){
             return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    public function setupWinner(){
        try{
            $games = Game::where('status',true)->where('is_end',0)->whereDate('end_at', '<', now()->toDateTimeString())->get();
        //return $games;
        //die();
            foreach($games as $game):
                $highPosition = $this->highPositons($game->prize);
                $lowPosition = $this->lowPositons($game->prize);
                $totalPlayer = $this->totalPlayer($game->id);
                if($highPosition > $totalPlayer){
                    // Setting only limited Player
                    $this->setHeighWinner($this->topPlayer($game->id,$totalPlayer),$game->prize);
                }else{
                    //Setting only limited Player
                    $this->setHeighWinner($this->topPlayer($game->id,$highPosition),$game->prize);
                    if(($totalPlayer - $highPosition) > $lowPosition){
                        //Setting Lower Player
                        $this->setLowWinner($this->lowerPlayer($game->id,$lowPosition), $game->prize);
                        
                    }else{
                        // Setting Winner Counter for Lower possition using dynamic methods.... if confused look methods below 
                        $this->setLowWinner($this->lowerPlayer($game->id,($totalPlayer - $highPosition)), $game->prize);
                    }
                }
                $this->gameupdate($game->id);
            endforeach;
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    private function gameupdate($id){
        try{
            $data = Game::findOrFail($id);
            $data->is_end = 1;
            $data->save();
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    // Setting up upper Winner
    private function setHeighWinner($data,$prize){
        try{
            $count = 0;
            $saveData = [];
            foreach($data as $d):
                $count++;
                 $saveData[] = array(
                    'game' => $d['game'],
                    'position' => $count,
                    'is_daily' => 0,
                    'position_type' => 'higher',
                    'player' => $d['player'],
                    'ticket' => $d['ticket'],
                    'prize' => $this->positionDetails($prize, $count, 'higher')['id'],
                    'grand_total' => $d['grand_total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    
                    );
            endforeach;
            $GameWinner = new GameWinner();
            $GameWinner->insert($saveData);
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
        
    }
    
    // Setting up Lower Winner
    private function setLowWinner($data,$prize){
        try{
            $count = 0;
            $saveData = [];
            foreach($data as $d):
                $count++;
                 $saveData[] = array(
                    'game' => $d['game'],
                    'position' => $count,
                    'is_daily' => 0,
                    'position_type' => 'lower',
                    'player' => $d['player'],
                    'ticket' => $d['ticket'],
                    'prize' => $this->positionDetails($prize, $count, 'lower')['id'],
                    'grand_total' => $d['grand_total'],
                    'created_at' => now(),
                    'updated_at' => now()
                    );
            endforeach;
            $GameWinner = new GameWinner();
            $GameWinner->insert($saveData);
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }



    // Fetching Top Player as per Prize
    private function topPlayer($id,$take){
        try{
            return  Game::where('games.id',$id)->join('gameplays', 'gameplays.game', '=', 'games.id')
            ->join('tickets', 'tickets.id', '=', 'gameplays.ticket')
            ->join('gameplay_stocks', 'gameplay_stocks.gameplay', '=', 'gameplays.id')
            ->join('users', 'users.id', '=', 'gameplays.player')
            ->select(DB::raw('SUM(gameplay_stocks.total_value) as grand_total'), 'gameplay_stocks.gameplay as gameplayId', 'users.id as player', 'games.id as game','tickets.id as ticket')
            ->groupBy('gameplay_stocks.gameplay','users.id','games.id','tickets.id')->orderBy('grand_total','DESC')->take($take)
            ->get();
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
     // Fetching Lower Player as per Prize
    private function lowerPlayer($id,$take){
        try{
            return  Game::where('games.id',$id)->join('gameplays', 'gameplays.game', '=', 'games.id')
            ->join('tickets', 'tickets.id', '=', 'gameplays.ticket')
            ->join('gameplay_stocks', 'gameplay_stocks.gameplay', '=', 'gameplays.id')
            ->join('users', 'users.id', '=', 'gameplays.player')
            ->select(DB::raw('SUM(gameplay_stocks.total_value) as grand_total'), 'gameplay_stocks.gameplay as gameplayId', 'users.id as player','tickets.id as ticket','games.id as game')
            ->groupBy('gameplay_stocks.gameplay','users.id','games.id','tickets.id')->orderBy('grand_total','ASC')->take($take)
            ->get();
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    private function avilablePosition($prize){
        try{
             return PrizeMeta::where('prize',$prize)->where('is_daily',0)->count(); 
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        } 
    }
    
    private function lowPositons($prize){
        try{
             return PrizeMeta::where('prize',$prize)->where('position_type','lower')->where('is_daily',0)->count(); 
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        } 
    }
    
    private function highPositons($prize){
        try{
             return PrizeMeta::where('prize',$prize)->where('position_type','higher')->where('is_daily',0)->count();  
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        } 
    }
    
    private function totalPlayer($game){
        try{
            return Gameplay::where('game',$game)->where('status',1)->count();
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    private function steup(){
        try{
            return Gameplay::where('game',$game)->where('status',1)->count();
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    private function positionDetails($prize, $position, $position_type){
        try{
            return PrizeMeta::where('prize',$prize)->where('position_type',$position_type)->where('position',$position)->where('is_daily',0)->first();  
        }catch(Exception $error){
            return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
        }
    }
    
    
}