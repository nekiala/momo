<?php

namespace App\Http\Controllers;

use App\Services\MoMoRequest;
use Illuminate\Http\Request;

class MomoController extends Controller
{
    public function create(string $phone, string $amount)
    {
        $request = new MoMoRequest();

        $request->setParams($phone, $amount);

        $request->start();
    }
}
