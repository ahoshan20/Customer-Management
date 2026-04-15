<?php

namespace App\Http\Controllers\Frotnend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use function Termwind\render;

class HomeContrller extends Controller
{
    public function index(Request $request):Response
    {
        return Inertia::render('frontend/Home');
    }
}
