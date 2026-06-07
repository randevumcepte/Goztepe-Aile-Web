<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function hakkimizda(): View
    {
        return view('pages.hakkimizda');
    }

    public function iletisim(): View
    {
        return view('pages.iletisim');
    }

    public function uyelikAvantajlari(): View
    {
        return view('pages.uyelik-avantajlari');
    }
}
