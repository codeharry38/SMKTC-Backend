<?php

namespace App\Http\Controllers;

use App\User;
use App\Game;
use App\Ticket;
use App\Gameplay;
use App\Http\Resources\Offer as Roffer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function index(){
        try{
            $total_player = $this->totalPlayers();
            $active_player = $this->activePlayers();
            $active_game = $this->activeGames();
            $latest_game = $this->game();
            //$revenu = $this->revenue();
            return response()->json(['total_player'=> $total_player, 'active_player' => $active_player, 'active_game' => $active_game, 'latest_game' => $latest_game]);
        } catch (Exception $error) {
           return response()->json(['action' => false, 'message' => "Somting not good!", 'error' => $error]);
       }
    }
    
    private function game(){
        $data = game::where('status', '1')->whereDate('end_at','>',now())->take(2)->get();
         $saveData = array();
        foreach($data as $d):
            array_push($saveData, array(
                'name' => $d->name,
                'game' => $d->id,
                'gameplay' => $this->latestGame($d->id),
                'totalPlayer' => $this->screenCount($d->id),
            ));
        endforeach;
        return $saveData;
    }
    
    private function screenCount($game){
        return gameplay::where('game',$game)->where('status',1)->count();
    }
    
    
    private function latestGame($id){
        try{
            return  Game::where('games.id',$id)->join('gameplays', 'gameplays.game', '=', 'games.id')->where('gameplays.status',1)
            ->join('gameplay_stocks', 'gameplay_stocks.gameplay', '=', 'gameplays.id')
            ->join('users', 'users.id', '=', 'gameplays.player')
            ->join('tickets', 'tickets.id', '=', 'gameplays.ticket')
            ->select(DB::raw('SUM(gameplay_stocks.total_value) as grand_total'), 'gameplay_stocks.gameplay as gameplayId', 'users.name as player','users.email as email','tickets.access_code as access_code','tickets.ticket_number as ticket_number','tickets.ticket_type as ticket_type')
            ->groupBy('gameplay_stocks.gameplay','users.name','users.email','tickets.access_code','tickets.ticket_number','tickets.ticket_type')->orderBy('grand_total','DESC')
            ->get();
        } catch (Exception $error){
            return $error;
        }
    }
        /*public function positionH($id){
        try{
            return Gameplay::where(['game' => $id, 'status' => 1])->select('*')->selectRaw('(stock_one_current_value * stock_one_item) + (stock_two_current_value * stock_two_item) + (stock_three_current_value * stock_three_item) + (stock_four_current_value * stock_four_item) + (stock_five_current_value * stock_five_item) as total')->orderBy('total', 'DESC')->get();
        }catch(Exception $error){
            return $error;
        }
    }*/
    
    private function revenue(){
        try{
            return Ticket::where('is_paid',1)->sum('cost')
            ->groupBy(function($date) {
            return date("F", strtotime($date->updated_at)); // grouping by Months
        });
        }catch(Exception $error){
            return $error;
        }
    }
    
   // Count Number of Player that are signup
    private function totalPlayers(){
        try{
            return User::where('role','player')->count();
        }catch(Exception $error){
            return $error;
        }
    }
     
    // Count Number of Player that are active
    private function activePlayers(){
        try{
            return User::where(['role'=>'player', 'status' => 1])->count();
        }catch(Exception $error){
            return $error;
        }
    }
    
    private function activeGames(){
        try{
            return Game::where('status', 1)->whereDate('end_at','>',now())->count();
        }catch(Exception $error){
            return $error;
        }
    }
    
}
