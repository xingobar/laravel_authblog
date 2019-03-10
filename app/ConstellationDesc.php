<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConstellationDesc extends Model
{
    protected $fillable = [
        'constellation_id', 'constellation_lucky_id', 'lucky_star', 'description',
    ];

    protected $table = 'constellation_desc';

    public function constellations()
    {
        return $this->belongsTo(App\Constellation, 'constellation_id');
    }

    public function constellation_luckies()
    {
        return $this->belongsTo(App\ConstellationLucky, 'constellation_lucky');
    }
}
