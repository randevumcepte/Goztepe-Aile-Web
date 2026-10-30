@extends('layouts.admin')
@section('title', $group->name.' — Üyeler')

@section('content')
<div class="mb-4 flex items-center justify-between gap-4">
    <p class="text-sm text-slate-500">{{ $members->count() }} üye bu grubu seçti. Bu liste yalnız yönetimde görünür (KVKK).</p>
    <a href="{{ route('admin.fan-groups.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Gruplara dön</a>
</div>
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-100 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr><th class="px-4 py-3">Üye</th><th class="px-4 py-3">Üye No</th><th class="px-4 py-3">Üyelik</th><th class="px-4 py-3">Seçim / rıza tarihi</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($members as $u)
                <tr>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $u->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $u->member?->member_no ?? '—' }}</td>
                    <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $u->member?->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $u->member?->status ?? 'üye değil' }}</span></td>
                    <td class="px-4 py-3 text-slate-500">{{ $u->fan_group_consent_at ? \Illuminate\Support\Carbon::parse($u->fan_group_consent_at)->format('d.m.Y H:i') : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-10 text-center text-slate-400">Henüz bu grubu seçen üye yok.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
