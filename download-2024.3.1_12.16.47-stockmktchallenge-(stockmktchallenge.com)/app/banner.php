<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class banner extends Model
{
    public function mediaMeta()
    {
        return $this->belongsTo('App\Medias','media');
    }
}
