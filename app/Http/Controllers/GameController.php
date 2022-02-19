<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * @return View
     */
    public function show(): View
    {
        return view('game');
    }
}
