<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(): View
    {
        $sponsors = Sponsor::orderBy('sort')->orderBy('id')->get();

        return view('admin.sponsors.index', compact('sponsors'));
    }

    public function create(): View
    {
        return view('admin.sponsors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('sponsors', 'uploads');
        }
        Sponsor::create($data);

        return redirect()->route('admin.sponsors.index')->with('status', 'Sponsor eklendi.');
    }

    public function edit(Sponsor $sponsor): View
    {
        return view('admin.sponsors.edit', compact('sponsor'));
    }

    public function update(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('sponsors', 'uploads');
        }
        $sponsor->update($data);

        return redirect()->route('admin.sponsors.index')->with('status', 'Sponsor güncellendi.');
    }

    public function destroy(Sponsor $sponsor): RedirectResponse
    {
        $sponsor->delete();

        return back()->with('status', 'Sponsor silindi.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'max:255'],
            'tier' => ['required', 'in:ana,resmi,destekci'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
