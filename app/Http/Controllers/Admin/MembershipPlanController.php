<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipPlanController extends Controller
{
    public function index(): View
    {
        $plans = MembershipPlan::orderBy('sort')->orderBy('id')->get();

        return view('admin.membership.plans.index', compact('plans'));
    }

    public function edit(MembershipPlan $plan): View
    {
        return view('admin.membership.plans.edit', compact('plan'));
    }

    public function update(Request $request, MembershipPlan $plan): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'price' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:120'],
            'card_features' => ['nullable', 'string'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'is_popular' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Satır satır girilen kart maddelerini diziye çevir
        $features = collect(preg_split('/\r\n|\r|\n/', (string) ($data['card_features'] ?? '')))
            ->map(fn ($l) => trim($l))->filter()->values()->all();

        $plan->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'card_features' => $features,
            'sort' => $data['sort'] ?? 0,
            'is_popular' => $request->boolean('is_popular'),
            'is_active' => $request->boolean('is_active'),
        ]);

        // Tek "popüler" plan olsun
        if ($request->boolean('is_popular')) {
            MembershipPlan::where('id', '!=', $plan->id)->update(['is_popular' => false]);
        }

        return redirect()->route('admin.membership.plans.index')->with('status', 'Plan güncellendi.');
    }
}
