<?php

namespace App\Http\Controllers;

use App\Constellation;
use App\ConstellationDesc;

class ConstellationController extends Controller
{
    //

    public function showConstellationDetail($id)
    {

        return view('constellation', [
            'detail' => ConstellationDesc::where([
                'constellation_id' => $id,
                'date' => date('Y-m-d'),
            ])->get(),
            'constellation' => Constellation::select('name')->where('id', $id)->get()[0],
        ]);
    }
}
