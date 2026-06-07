<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipFeature;
use App\Models\MembershipPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipFeatureController extends Controller
{
    public function index(): View
    {
        return view('admin.membership.features.index', [
            'features' => MembershipFeature::orderBy('sort')->orderBy('id')->get(),
            'plans' => MembershipPlan::active()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.membership.features.create', ['plans' => MembershipPlan::active()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        MembershipFeature::create($data);

        return redirect()->route('admin.membership.features.index')->with('status', 'Avantaj eklendi.');
    }

    public function edit(MembershipFeature $feature): View
    {
        return view('admin.membership.features.edit', [
            'feature' => $feature,
            'plans' => MembershipPlan::active()->get(),
        ]);
    }

    public function update(Request $request, MembershipFeature $feature): RedirectResponse
    {
        $feature->update($this->validateData($request));

        return redirect()->route('admin.membership.features.index')->with('status', 'Avantaj güncellendi.');
    }

    public function destroy(MembershipFeature $feature): RedirectResponse
    {
        $feature->delete();

        return back()->with('status', 'Avantaj silindi.');
    }

    private function validateData(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'values' => ['nullable', 'array'],
        ]);

        // Her plan için girilen değeri normalize et: var / yok / metin
        $values = [];
        foreach ((array) $request->input('values', []) as $key => $raw) {
            $values[$key] = $this->normalize($raw);
        }

        return [
            'name' => $request->string('name'),
            'sort' => (int) $request->input('sort', 0),
            'is_active' => $request->boolean('is_active'),
            'values' => $values,
        ];
    }

    private function normalize(?string $raw): string
    {
        $v = mb_strtolower(trim((string) $raw));

        if (in_array($v, ['', 'yok', '-', 'hayır', 'hayir', 'no', 'x'], true)) {
            return 'no';
        }
        if (in_array($v, ['var', 'evet', 'yes', '✓', '+', 'true', '1'], true)) {
            return 'yes';
        }

        return trim((string) $raw); // örn. "%5"
    }
}
