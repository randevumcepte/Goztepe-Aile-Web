<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignImpression extends Model
{
    protected $fillable = ['in_app_message_id', 'user_id', 'shown_at', 'clicked_at'];

    protected function casts(): array
    {
        return [
            'shown_at' => 'datetime',
            'clicked_at' => 'datetime',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(InAppMessage::class, 'in_app_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
