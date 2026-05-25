<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    public function index()
    {
        return view('pages.index');
    }

    public function checkQueue()
    {
        return view('pages.check-queue');
    }
}
