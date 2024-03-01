<?php

namespace App\Http\Resources;
use App\Gameplay;
use Illuminate\Http\Resources\Json\JsonResource;

class GameWinner extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
public function getGamePlayId($data){
	return Gameplay::where('ticket',$data)->first();
}
    
    public function toArray($request)
    {
        return [
            'position' => $this->position,
            'game' => $this->gameMeta->name,
            'player' => $this->playerMeta->name,
            'Prize' => $this->prizeMeta,
            'Ticket' => $this->ticketMeta,
        	'gameplayId' => $this->getGamePlayId($this->ticketMeta->id)->id,
            'grand_total' => $this->grand_total,
            'created_at' => date("m-d-Y g:i a", strtotime($this->created_at)),
            'total_prize' => $this->gameMeta->prizecount($this->gameMeta->prizeData->id)
        ];
    }
}