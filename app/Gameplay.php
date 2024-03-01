<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\GameplayStock;

class Gameplay extends Model
{
     public function gameMeta(){
        return $this->belongsTo('App\Game','game');
    }
    
    public function TicketMeta(){
        return $this->belongsTo('App\Ticket','ticket');
    }
    
    public function StockMeta(){
        return $this->hasMany('App\GameplayStock','gameplay')->with('stockMeta');
    }
    
    public function gameStock(){
        $gameplayData = GameplayStock::where('gameplay',36)->get();
        return $gameplayData;
    }
    public function playerMeta(){
        return $this->belongsTo('App\User','player');
    }
    
    public function totalValue()
    {
        return $this->hasMany('App\GameplayStock','gameplay')->select(DB::raw('SUM(total_value) as total'));
    }
    
    public function Positioning()
    {
        return $this->hasMany('App\GameplayStock','gameplay')->select('*', DB::raw('SUM(total_value) as total'))->orderBy('total');
    }
}
