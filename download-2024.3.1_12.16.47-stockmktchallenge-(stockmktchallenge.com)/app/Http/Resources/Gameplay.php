<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Gameplay extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function total($data){
        if($data->status == 1){
             $one = $data->stock_one_item * $data->stock_one_current_value;
             $two = $data->stock_two_item * $data->stock_two_current_value;
             $three = $data->stock_three_item * $data->stock_three_current_value;
             $four = $data->stock_four_item * $data->stock_four_current_value;
             $five = $data->stock_five_item * $data->stock_five_current_value;
             $total = $one + $two + $three + $four + $five;
             return round($total, 2);
        }else {
            return 0;
        }
    }
    
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'game' => $this->gameMeta,
            'stock_one' => $this->stock_one_meta,
            'stock_two' => $this->stock_two_meta,
            'stock_three' => $this->stock_three_meta,
            'stock_four' => $this->stock_four_meta,
            'stock_five' => $this->stock_five_meta,
            'stock_one_item' => $this->stock_one_item,
            'stock_two_item' => $this->stock_two_item,
            'stock_three_item' => $this->stock_three_item,
            'stock_four_item' => $this->stock_four_item,
            'stock_five_item' => $this->stock_five_item,
            'stock_one_current_value' => $this->stock_one_current_value,
            'stock_two_current_value' => $this->stock_two_current_value,
            'stock_three_current_value' => $this->stock_three_current_value,
            'stock_four_current_value' => $this->stock_four_current_value,
            'stock_five_current_value' => $this->stock_five_current_value,
            'total' => $this->total($this),
            'ticket' => $this->TicketMeta,
            'start_at' => date("m-d-Y", strtotime($this->gameMeta->start_at)),
            'end_at' => date("m-d-Y", strtotime($this->gameMeta->end_at)), 
            'entrance_deadline' => date("m-d-Y", strtotime($this->gameMeta->entrance_deadline))
        ];
    }
}