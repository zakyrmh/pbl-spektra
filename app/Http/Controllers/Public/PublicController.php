<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

final class PublicController extends Controller
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
