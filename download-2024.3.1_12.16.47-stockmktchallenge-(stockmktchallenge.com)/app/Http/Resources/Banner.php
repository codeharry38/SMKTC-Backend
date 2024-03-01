<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Banner extends JsonResource
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
            'media' => $this->media,
            'mediaImg' => $this->mediaMeta == null ? 'https://cdn-icons-png.flaticon.com/512/1375/1375106.png' : 'https://apibackend.stockmktchallenge.com'.$this->mediaMeta->filePath,
            'status' => $this->status,
            'mini' => $this->mini,
        ];
    }
}