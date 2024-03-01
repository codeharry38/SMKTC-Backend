<?php

namespace App\Http\Resources;

use App\Ticket;
use App\PrizeMeta;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Game extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function isSoldOut($request){
        $data = Ticket::where('game', '=', $request->id)->where('ticket_type', '=', 'online')->where('assign_to', '=', 0)->count();
        $available = $data;
        $action = $available > 0 ? false : true;
        return ['action' => $action, 'available' => $available];
    }
     public function availableFree($request){
        	return Ticket::where('game', '=', $request->id)->where('ticket_type', '=', 'free')->where('assign_to', '=', 0)->where('used_by', '=', 0)->count();
    }

	public function isOffer($request){
    	if($request->offer == 0){
        	return false;
        }else{
        	return $this->availableFree($request)  >= $request->offerMeta->freeticket ? true : false;
        }
    
    }
    
    public function isTimeOut($request){
        $start_Date= date("Y-m-d H:i:s", strtotime($this->entrance_deadline));
        $start = Carbon::parse($start_Date);
        $now = Carbon::now();
        return $now->diffInDays($start, false) < 0 ? true : false;
        
        /*$end_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->entrance_deadline)->format('d-m-Y');
        $date1 = Carbon::createMidnightDate($end_date);
        $date2 = Carbon::createMidnightDate(date('d-m-Y'));
        return $date1->diffAsCarbonInterval($date2, false); ;*/
    }
    public function lowerPrize($request){
        return PrizeMeta::where('prize', $request)->where('position_type','lower')->where('is_daily',0)->orderBy('position')->get();
    }
    
    public function highPrize($request){
        return PrizeMeta::where('prize', $request)->where('position_type','higher')->where('is_daily',0)->orderBy('position')->get();
    }
    
    public function lowerPrizeDaily($request){
        return PrizeMeta::where('prize', $request)->where('position_type','lower')->where('is_daily',1)->orderBy('position')->get();
    }
    
    public function highPrizeDaily($request){
        return PrizeMeta::where('prize', $request)->where('position_type','higher')->where('is_daily',1)->orderBy('position')->get();
    }
    
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_at' => date("Y-m-d", strtotime($this->start_at)),
            'end_at' => date("Y-m-d", strtotime($this->end_at)),
            'entrance_deadline' => date("Y-m-d", strtotime($this->entrance_deadline)),
            'f_start_at' => date("m/d/Y", strtotime($this->start_at)),
            'f_end_at' => date("m/d/Y", strtotime($this->end_at)),
            'f_entrance_deadline' => date("m/d/Y", strtotime($this->entrance_deadline)),
            'cost' => $this->cost,
            'media' => $this->media,
            'offermedia' => $this->offermedia,
        	'offermediaImg' => $this->offerMediaMeta == null ? null : 'https://apibackend.stockmktchallenge.com'.$this->offerMediaMeta->filePath,
            'mediaImg' => $this->mediaMeta == null ? 'https://cdn-icons-png.flaticon.com/512/1375/1375106.png' : 'https://apibackend.stockmktchallenge.com'.$this->mediaMeta->filePath,
            'sponsor' => $this->sponsor,
            "numberoftickets" => $this->online_tickets + $this->offline_tickets + $this->free_tickets + $this->reserve_tickets,
        	'reserve_tickets' => $this->reserve_tickets,
            'online_tickets' => $this->online_tickets,
            'offline_tickets' => $this->offline_tickets,
            'free_tickets' => $this->free_tickets,
            'organization' => $this->organization,
            'organization_meta' => $this->organizationMeta,
            'prize' => $this->prize,
            'low_prize' => $this->lowerPrize($this->prize),
            'high_prize' => $this->highPrize($this->prize),
            'low_prize_daily' => $this->lowerPrizeDaily($this->prize),
            'high_prize_daily' => $this->highPrizeDaily($this->prize),
            'offer' => $this->offer == 0 ? false : $this->offerMeta,
            'status' => $this->status,
            'is_sold_out' => $this->isSoldOut($this)['action'],
            'avilable_ticket' => $this->isSoldOut($this)['available'],
            'is_time_out' => $this->isTimeOut($this),
            'is_daily_prize' => $this->is_daily_prize,
            'is_offer' => $this->isOffer($this),
            'total_prize' => $this->prizecount($this->prizeData->id)
        ];
    }
}

