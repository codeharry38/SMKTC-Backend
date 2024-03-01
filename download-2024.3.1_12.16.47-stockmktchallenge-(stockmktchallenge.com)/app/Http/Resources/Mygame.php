<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Medias;
class Mygame extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
 
    
    public function toArray($request)
    {
        return [
            'id' => $this->id,
           'access_code' => $this->TicketMeta->access_code,
           'ticket_number' => $this->TicketMeta->ticket_number,
            'game' => $this->gameMeta,
            'mediaImg' => $this->gameMeta->mediaMeta == null ? 'https://cdn-icons-png.flaticon.com/512/1375/1375106.png' : 'https://apibackend.stockmktchallenge.com'.$this->gameMeta->mediaMeta->filePath,
            //'ticket_number' => $this->ticket_number,
           // 'ticket_type' => $this->ticket_type,
           // 'cost' => $this->cost,
            'sponsor' => $this->sponsor,
            'assign_to' => $this->assign_to,
            'used_by' => $this->used_by == null ? 'Not Used' : $this->used_by,
            'organization' => $this->organization,
           // 'prize' => $this->prize,
            'is_game_ative' => $this->is_game_ative,
          //  'is_used' => $this->is_used,
          //  'is_paid' => $this->is_paid,
            'is_promoted' => $this->is_promoted,
            'start_at' => date("m/d/Y", strtotime($this->start_at)),
            'end_at' => date("m/d/Y", strtotime($this->end_at)),
            'entrance_deadline' => date("m/d/Y", strtotime($this->gameMeta->entrance_deadline)),
            'status' => $this->status,
        ];
    }
}