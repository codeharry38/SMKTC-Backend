<?php

namespace App\Http\Resources;

use App\Ticket;
use App\PrizeMeta;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Billing extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
      public function freeClaimed($d){
        return Ticket::where('game',$d->id)->where('ticket_type' , 'free')->where('is_used',1)->count();
    }
    
    public function onlineClaimed($d){
        return Ticket::where('game',$d->id)->where('ticket_type' , 'online')->where('is_used',1)->count();
    }
    
    public function offlineClaimed($d){
        return Ticket::where('game',$d->id)->where('ticket_type' , 'offline')->where('is_used',1)->count();
    }
	public function reserveClaimed($d){
        return Ticket::where('game',$d->id)->where('ticket_type' , 'reserve')->where('is_used',1)->count();
    }
	
	
     public function reservePaid($d){
        return Ticket::where('game',$d->id)->where('ticket_type' , 'reserve')->where('is_paid',1)->count();
    }
    
    public function onlinePaid($d){
        return Ticket::where('game',$d->id)->where('ticket_type' , 'online')->where('is_paid',1)->count();
    }
    
    public function offlinePaid($d){
        return Ticket::where('game',$d->id)->where('ticket_type' , 'offline')->where('is_used',1)->count();
    }
    
    
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_at' => date("d, M Y", strtotime($this->start_at)),
            'end_at' => date("d, M Y", strtotime($this->end_at)),
            'entrance_deadline' => date("d, M Y", strtotime($this->entrance_deadline)),
            'cost' => $this->cost,
            'sponsor' => $this->sponsor,
            "numberoftickets" => $this->online_tickets + $this->offline_tickets + $this->free_tickets + $this->reserve_tickets,
            'online_tickets' => $this->online_tickets,
        	'reserve_tickets' => $this->reserve_tickets,
            'offline_tickets' => $this->offline_tickets,
            'free_tickets' => $this->free_tickets,
            'organization' => $this->organizationMeta,
            'prize' => $this->prize,
            'offer' => $this->offerMeta,
            'status' => $this->status,
        	'reserve_claim'  => $this->reserveClaimed($this),
            'online_claim'  => $this->onlineClaimed($this),
            'free_claim'  => $this->freeClaimed($this),
            'offline_claim'  => $this->offlineClaimed($this),
            'offline_paid'  => $this->offlinePaid($this),
            'online_paid'  => $this->onlinePaid($this),
        	'reserve_paid'  => $this->reservePaid($this)
        ];
    }
}

