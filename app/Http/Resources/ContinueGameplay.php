<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Game;
class ContinueGameplay extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
     public function canStart($data){
         return Game::where('id',$data->game)->whereDate('start_at', '<=', now())->count();
         
     }
     
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'game' => $this->gameMeta,
            'stocks' => $this->StockMeta,
            'total' => $this->totalValue[0]['total'],
            'ticket' => $this->TicketMeta,
            'start_at' => date("m/d/Y", strtotime($this->gameMeta->start_at)),
            'end_at' => date("m/d/Y", strtotime($this->gameMeta->end_at)), 
            'entrance_deadline' => date("m/d/Y", strtotime($this->gameMeta->entrance_deadline)),
            'is_end' => $this->gameMeta->is_end,
            'is_start' => $this->canStart($this)
        ];
    }
}