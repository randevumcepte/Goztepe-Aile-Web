<?php

namespace App\Services\Notifications;

use App\Models\CampaignImpression;
use App\Models\InAppMessage;
use App\Models\User;

/**
 * In-app kampanya / reklam pop-up motoru.
 * Hedefleme + frequency cap + rıza kontrolü + gösterim takibi.
 */
class CampaignService
{
    /** Kullanıcıya şu an gösterilecek en uygun kampanyayı döner (yoksa null). */
    public function nextFor(User $user): ?InAppMessage
    {
        $candidates = InAppMessage::query()->live()->get();

        foreach ($candidates as $message) {
            if (! $this->eligible($user, $message)) {
                continue;
            }

            return $message;
        }

        return null;
    }

    public function eligible(User $user, InAppMessage $message): bool
    {
        // Ticari ise rıza şart
        if ($message->is_commercial && ! $user->member?->commercial_consent) {
            return false;
        }

        // Hedef segment (kategori) kontrolü
        $segment = $message->segment ?? [];
        if (! empty($segment['kategori']) && $user->member?->category?->value !== $segment['kategori']) {
            return false;
        }

        // Frequency cap: bu kullanıcıya kaç kez gösterildi
        $shown = CampaignImpression::query()
            ->where('in_app_message_id', $message->id)
            ->where('user_id', $user->id)
            ->whereNotNull('shown_at')
            ->count();

        return $shown < $message->frequency_cap;
    }

    public function recordImpression(User $user, InAppMessage $message): void
    {
        CampaignImpression::create([
            'in_app_message_id' => $message->id,
            'user_id' => $user->id,
            'shown_at' => now(),
        ]);
    }

    public function recordClick(User $user, InAppMessage $message): void
    {
        $imp = CampaignImpression::query()
            ->where('in_app_message_id', $message->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $imp?->update(['clicked_at' => now()]);
    }
}
