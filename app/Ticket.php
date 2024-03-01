<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;
    //
    
    public function gameMeta(){
        return $this->belongsTo('App\Game','game');
    }
    
    public function userMeta(){
        return $this->belongsTo('App\User','used_by');
    }
    
    public function assignMeta(){
        return $this->belongsTo('App\User','assign_to');
    }
    
    public function orgMeta(){
        return $this->belongsTo('App\Organization','organization');
    }
}
