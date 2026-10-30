<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\Admin\FanGroupController as ApiFanGroupController;
use App\Http\Controllers\Controller;
use App\Models\FanGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Taraftar grupları (web admin). Üye isimlerini yalnız yönetim görür. */
class FanGroupController extends Controller
{
    public function index(): View
    {
        $groups = FanGroup::withCount('users')->orderBy('sort')->orderBy('name')->get();
        $independent = \App\Models\User::whereNull('fan_group_id')->whereHas('member')->count();

        return view('admin.fan-groups.index', compact('groups', 'independent'));
    }

    public function create(): View
    {
        return view('admin.fan-groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        FanGroup::create(ApiFanGroupController::data($request));

        return redirect()->route('admin.fan-groups.index')->with('status', 'Grup eklendi.');
    }

    public function show(FanGroup $fanGroup): View
    {
        $members = $fanGroup->users()->with('member')->orderBy('name')->get();

        return view('admin.fan-groups.show', ['group' => $fanGroup, 'members' => $members]);
    }

    public function edit(FanGroup $fanGroup): View
    {
        return view('admin.fan-groups.edit', ['group' => $fanGroup]);
    }

    public function update(Request $request, FanGroup $fanGroup): RedirectResponse
    {
        $fanGroup->update(ApiFanGroupController::data($request));

        return redirect()->route('admin.fan-groups.index')->with('status', 'Grup güncellendi.');
    }

    public function destroy(FanGroup $fanGroup): RedirectResponse
    {
        $fanGroup->delete();

        return back()->with('status', 'Grup silindi; üyeleri bağımsız oldu.');
    }
}
