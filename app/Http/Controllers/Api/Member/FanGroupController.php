<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\FanGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * "Hangi gruptasın?" — üye kendi taraftar grubunu seçer (isteğe bağlı, serbestçe değiştirilebilir).
 * Herkese açık isim listesi yok: grupta yalnız üye sayısı görünür, grup üyenin kendi kartında yazar.
 */
class FanGroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $groups = FanGroup::active()->withCount('users')->get();

        return response()->json([
            'my_group_id' => $request->user()->fan_group_id,
            'items' => $groups->map(fn (FanGroup $g) => [
                'id' => $g->id,
                'name' => $g->name,
                'logo' => $g->logoUrl(),
                'description' => $g->description,
                'tribune_area' => $g->tribune_area,
                'member_count' => $g->users_count,
            ])->values(),
        ]);
    }

    public function choose(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fan_group_id' => ['nullable', 'integer', Rule::exists('fan_groups', 'id')->where('is_active', true)],
            'consent' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        if (! empty($data['fan_group_id'])) {
            if (! $request->boolean('consent')) {
                return response()->json(['message' => 'Grubunu kaydetmek için onay kutusunu işaretlemelisin.'], 422);
            }
            $user->forceFill(['fan_group_id' => $data['fan_group_id'], 'fan_group_consent_at' => now()])->save();
            $name = FanGroup::find($data['fan_group_id'])->name;

            return response()->json(['message' => "Grubun kaydedildi: {$name} 💛❤️", 'my_group_id' => $user->fan_group_id]);
        }

        // "Bağımsız / grubum yok": grup ve rıza kaydı silinir
        $user->forceFill(['fan_group_id' => null, 'fan_group_consent_at' => null])->save();

        return response()->json(['message' => 'Bağımsız taraftar olarak kaydedildin.', 'my_group_id' => null]);
    }
}
