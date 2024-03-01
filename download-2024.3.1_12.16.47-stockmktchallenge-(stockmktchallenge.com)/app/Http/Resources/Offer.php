<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Offer extends JsonResource
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
            'name' => $this->name,
            'offercode' => $this->offercode,
            'paidticket' => $this->paidticket,
            'freeticket' => $this->freeticket,
            'start_at' => date("Y-m-d", strtotime($this->start_at)),
            'end_at' => date("Y-m-d", strtotime($this->end_at)),
            'f_start_at' => date("m/d/Y", strtotime($this->start_at)),
            'f_end_at' => date("m/d/Y", strtotime($this->end_at)),
            'status' => $this->status,
        ];
    }
}
