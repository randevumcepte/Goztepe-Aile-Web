<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MemberCategory;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::with('user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q');
                $query->whereHas('user', fn ($u) => $u->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%"))
                    ->orWhere('member_no', 'like', "%$q%");
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.members.index', [
            'members' => $members,
            'categories' => MemberCategory::cases(),
        ]);
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'in:'.implode(',', array_column(MemberCategory::cases(), 'value'))],
            'status' => ['required', 'in:beklemede,aktif,pasif'],
        ]);

        $member->update($data);

        return back()->with('status', 'Üye güncellendi.');
    }
}
