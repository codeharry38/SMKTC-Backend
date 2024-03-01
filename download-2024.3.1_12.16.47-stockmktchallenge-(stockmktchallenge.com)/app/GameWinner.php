<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameWinner extends Model
{
    use SoftDeletes;
    
    public function ticketMeta(){
        return $this->belongsTo('App\Ticket','ticket');
    }
    public function playerMeta(){
        return $this->belongsTo('App\User','player');
    }
    public function prizeMeta(){
        return $this->belongsTo('App\PrizeMeta','prize');
    }
    public function gameMeta(){
        return $this->belongsTo('App\Game','game');
    }
}