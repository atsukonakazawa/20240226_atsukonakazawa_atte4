<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Breaktime;
use App\Models\Work;



class AuthController extends Controller
{
    public function index()
    {
        $breaktimes = breaktime::with('work')->get();
        $works = Work::all();

        return view('index',compact('breaktimes','works'));
    }

}
