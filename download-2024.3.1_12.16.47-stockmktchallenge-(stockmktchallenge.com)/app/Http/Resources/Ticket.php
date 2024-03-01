<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Ticket extends JsonResource
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
            'access_code' => $this->access_code,
            'game' => $this->gameMeta->name,
            'ticket_number' => $this->ticket_number,
            'ticket_type' => $this->ticket_type,
            'cost' => $this->cost,
            'sponsor' => $this->sponsor,
            'assign_to' => $this->assign_to,
            'claimed_by' => $this->used_by == 0 ? 'Not used' : $this->userMeta->name,
            'assign' => $this->assign_to == 0 ? 'Not assigned' : $this->assignMeta->name,
            'organization' => $this->organization,
            'prize' => $this->prize,
            'is_game_ative' => $this->is_game_ative,
            'is_used' => $this->is_used,
            'is_paid' => $this->is_paid,
            'is_promoted' => $this->is_promoted,
            'start_at' => date("m/d/Y", strtotime($this->start_at)),
            'end_at' => date("m/d/Y", strtotime($this->end_at)),
            'entrance_deadline' => date("m/d/Y", strtotime($this->entrance_deadline)),
            'status' => $this->status,
        ];
    }
}