<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SliderController extends Controller
{
    public function index(): View
    {
        $sliders = Slider::orderBy('sort')->orderBy('id')->get();

        return view('admin.sliders.index', compact('sliders'));
    }

    public function create(): View
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('sliders', 'uploads');
        }
        Slider::create($data);

        return redirect()->route('admin.sliders.index')->with('status', 'Slider eklendi.');
    }

    public function edit(Slider $slider): View
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('sliders', 'uploads');
        }
        $slider->update($data);

        return redirect()->route('admin.sliders.index')->with('status', 'Slider güncellendi.');
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        $slider->delete();

        return back()->with('status', 'Slider silindi.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:60'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
