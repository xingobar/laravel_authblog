<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Constellation extends Model
{
    public function descriptions()
    {
        return $this->hasMany(App\ConstellationDesc, 'constellation_id', 'id');
    }
}
