<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class Claim extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function message($data){
        if($data->is_paid == 0 && $data->ticket_type != 'free'){
            return 'Please purchase first :)';
        } elseif($data->is_used == 1){
            return 'The given access code is already Claimed.';
        } else {
            return 'You are eligible to claim this ticket.'; 
        }
    }
    public function can_purchased($data){
        if($data->is_paid == 0 && $data->ticket_type != 'free'){
            return true;
        }else{
            return false;
        }
    }
    
    public function can_claim($data){
        if($data->is_paid == 0 && $data->ticket_type != 'free'){
            return false;
        } elseif($data->is_used == 1){
            return false;
        } else {
            return true;
        }
    }
    
    public function isTimeOut($request){
        $start_Date= date("Y-m-d H:i:s", strtotime($request->entrance_deadline));
        $start = Carbon::parse($start_Date);
        $now = Carbon::now();
        return $now->diffInDays($start, false) < 0 ? true : false;
        
        /*$end_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->entrance_deadline)->format('d-m-Y');
        $date1 = Carbon::createMidnightDate($end_date);
        $date2 = Carbon::createMidnightDate(date('d-m-Y'));
        return $date1->diffAsCarbonInterval($date2, false); ;*/
    }
    
    public function is_find($data){
        return $data;
    }
    
    public function toArray($request)
    {
        if($this->is_find($this) == ''){
            return [''];
        }else{
            return [
                'id' => $this->id,
                'access_code' => $this->access_code,
                'game' => $this->gameMeta,
                'mediaImg' => $this->gameMeta->mediaMeta == null ? 'https://cdn-icons-png.flaticon.com/512/1375/1375106.png' : 'https://apibackend.stockmktchallenge.com'.$this->gameMeta->mediaMeta->filePath,
                'ticket_number' => $this->ticket_number,
                'ticket_type' => $this->ticket_type,
                'cost' => $this->cost,
                'sponsor' => $this->sponsor,
                'assign_to' => $this->assign_to,
                'used_by' => $this->used_by == null ? 'Not Used' : $this->used_by,
                'organization' => $this->orgMeta,
                'prize' => $this->prize,
                'is_game_ative' => $this->is_game_ative,
                'is_used' => $this->is_used,
                'is_paid' => $this->is_paid,
                'is_promoted' => $this->is_promoted,
                'start_at' => date("m/d/Y", strtotime($this->start_at)),
                'end_at' => date("m/d/Y", strtotime($this->end_at)),
                'entrance_deadline' => date("m/d/Y", strtotime($this->entrance_deadline)),
                'status' => $this->status,
                'message' => $this->message($this),
                'can_claim' => $this->can_claim($this),
                'result_code' => 1,
                'can_buy' => $this->can_purchased($this),
                'is_time_out' => $this->isTimeOut($this)
            ];
        }
    }
}
