<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use Illuminate\View\View;

class FixtureController extends Controller
{
    public function index(): View
    {
        $upcoming = Fixture::upcoming()->get();
        $played = Fixture::played()->limit(20)->get();

        return view('member.fikstur', compact('upcoming', 'played'));
    }
}
