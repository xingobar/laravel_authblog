<?php

namespace App\Http\Controllers;

use App\ConstellationDesc;

class ConstellationController extends Controller
{
    //

    public function showConstellationDetail($id)
    {

        return view('constellation', [
            'detail' => ConstellationDesc::where('constellation_id', $id)->get(),
        ]);
    }
}
