<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\MemberCategory;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request): JsonResponse
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

        return response()->json([
            'items' => $members->getCollection()->map(fn (Member $m) => $this->payload($m))->values(),
            'current_page' => $members->currentPage(),
            'last_page' => $members->lastPage(),
            'total' => $members->total(),
            'categories' => array_map(fn (MemberCategory $c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ], MemberCategory::cases()),
            'statuses' => ['beklemede', 'aktif', 'pasif'],
        ]);
    }

    public function update(Request $request, Member $member): JsonResponse
    {
        $data = $request->validate([
            'category' => ['required', 'in:'.implode(',', array_column(MemberCategory::cases(), 'value'))],
            'status' => ['required', 'in:beklemede,aktif,pasif'],
        ]);

        $member->update($data);

        $member->load('user');

        return response()->json([
            'message' => 'Üye güncellendi.',
            'item' => $this->payload($member),
        ]);
    }

    private function payload(Member $m): array
    {
        return [
            'id' => $m->id,
            'member_no' => $m->member_no,
            'name' => $m->user?->name,
            'email' => $m->user?->email,
            'phone' => $m->user?->phone,
            'category' => $m->category?->value,
            'category_label' => $m->category?->label(),
            'status' => $m->status,
            'has_vote' => $m->hasVote(),
            'joined_at' => $m->joined_at?->toDateString(),
            'created_at' => $m->created_at?->toIso8601String(),
        ];
    }
}
