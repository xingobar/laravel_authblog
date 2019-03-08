<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConstellationLucky extends Model
{
    public function descriptions()
    {
        return $this->hasMany(App\ConstellationDesc, 'constellation_lucky_id', 'id');
    }
}
