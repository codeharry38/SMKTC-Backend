<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\PrizeMeta;

class Game extends Model
{
    use SoftDeletes;
    
    
    public function offerMeta(){
        return $this->belongsTo('App\Offer','offer');
    }
    
    public function ticketMeta(){
        return $this->hasMany('App\Ticket','game');
    }
    
     public function organizationMeta(){
        return $this->belongsTo('App\Organization','organization');
    }
    
    public function gamePlay()
    {
        return $this->hasMany('App\Gameplay','game');
    }
    
    public function prizecount($id)
    {
        return PrizeMeta::where('prize',$id)->where('is_daily',0)->where('prize_type','cash')->sum('prize_value');
    }
    
     public function prizeData()
    {
        return $this->belongsTo('App\Prize','prize');
    }
    
    public function mediaMeta()
    {
        return $this->belongsTo('App\Medias','media');
    }
	public function offerMediaMeta()
    {
        return $this->belongsTo('App\Medias','offermedia');
    }
    
    public function freeClaimd(){
        return $this->hasMany('App\Ticket','game')->where('ticket_type' , 'free')->where('is_used',1)->count();
    }
    
    public function onlineClaimed(){
        return $this->hasMany('App\Ticket','game')->where('ticket_type' , 'online')->where('is_used',1)->count();
    }
    
    public function offlineClaimed(){
        return $this->hasMany('App\Ticket','game')->where('ticket_type' , 'offline')->where('is_used',1)->count();
    }
    
    public function onlinePaid(){
        return $this->hasMany('App\Ticket','game')->where('ticket_type' , 'online')->where('is_paid',1)->count();
    }
    
    public function offlinePaid(){
        return $this->hasMany('App\Ticket','game')->where('ticket_type' , 'offline')->where('is_paid',1)->count();
    }
    
}
