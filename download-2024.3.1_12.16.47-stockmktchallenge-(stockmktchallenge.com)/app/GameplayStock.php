<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameplayStock extends Model
{
    public function stockMeta(){
        return $this->belongsTo('App\StockList','stock');
    }
    public function gameplayMeta(){
        return $this->belongsTo('App\Gameplay','gameplay');
    }
    
    
    
}
