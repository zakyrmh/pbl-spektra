<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    public function index()
    {
        return view('public.index');
    }

    public function checkQueue()
    {
        return view('public.check-queue');
    }
}
