<?php

namespace App\Services;

use Anthropic\Client as AnthropicClient;
use Anthropic\Core\Exceptions\APIException;
use Illuminate\Support\Facades\Log;

/**
 * Kısa Türkçe metin üretimi (Haiku). Anahtar ya da SDK yoksa null döner;
 * çağıran taraf her zaman kural tabanlı bir yedek metin hazır tutar.
 */
class AiTextService
{
    private const MODEL = 'claude-haiku-5-5';

    public function available(): bool
    {
        return (bool) config('services.anthropic.key') && class_exists(AnthropicClient::class);
    }

    public function write(string $system, string $prompt): ?string
    {
        if (! $this->available()) {
            return null;
        }

        try {
            $client = new AnthropicClient(
                apiKey: config('services.anthropic.key'),
                requestOptions: ['timeout' => 20.0, 'maxRetries' => 1],
            );
            $message = $client->messages->create(
                model: self::MODEL,
                maxTokens: 2000,
                system: $system,
                outputConfig: ['effort' => 'low'],
                messages: [['role' => 'user', 'content' => $prompt]],
            );

            if ($message->stopReason === 'refusal') {
                return null;
            }
            foreach ($message->content as $block) {
                if ($block->type === 'text' && trim($block->text) !== '') {
                    return trim($block->text);
                }
            }
        } catch (APIException $e) {
            Log::warning('AI metni alınamadı: '.$e->getMessage());
        } catch (\Throwable $e) {
            Log::warning('AI metin hatası: '.$e->getMessage());
        }

        return null;
    }
}
