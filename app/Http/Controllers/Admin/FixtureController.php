<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FixtureController extends Controller
{
    public function index(): View
    {
        $fixtures = Fixture::orderByDesc('kickoff_at')->get();

        return view('admin.fixtures.index', compact('fixtures'));
    }

    public function create(): View
    {
        return view('admin.fixtures.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('opponent_logo')) {
            $data['opponent_logo_path'] = $request->file('opponent_logo')->store('fixtures', 'uploads');
        }
        Fixture::create($data);

        return redirect()->route('admin.fixtures.index')->with('status', 'Maç eklendi.');
    }

    public function edit(Fixture $fixture): View
    {
        return view('admin.fixtures.edit', compact('fixture'));
    }

    public function update(Request $request, Fixture $fixture): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('opponent_logo')) {
            $data['opponent_logo_path'] = $request->file('opponent_logo')->store('fixtures', 'uploads');
        }
        $fixture->update($data);

        return redirect()->route('admin.fixtures.index')->with('status', 'Maç güncellendi.');
    }

    public function destroy(Fixture $fixture): RedirectResponse
    {
        $fixture->delete();

        return back()->with('status', 'Maç silindi.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'opponent' => ['required', 'string', 'max:120'],
            'competition' => ['required', 'string', 'max:80'],
            'is_home' => ['nullable', 'boolean'],
            'kickoff_at' => ['required', 'date'],
            'venue' => ['nullable', 'string', 'max:160'],
            'broadcast' => ['nullable', 'string', 'max:80'],
            'home_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'away_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'opponent_logo' => ['nullable', 'image', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_home'] = $request->boolean('is_home');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
