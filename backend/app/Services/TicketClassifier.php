<?php


namespace App\Services;


use App\Enums\TicketCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;


class TicketClassifier
{
    public function classify(string $subject, string $body): array
    {
        $enabled = filter_var(config('services.openai.enabled', env('OPENAI_CLASSIFY_ENABLED', false)), FILTER_VALIDATE_BOOL);

        $rpm = (int) env('CLASSIFY_RPM', 30);
        $key = 'classify:global';

        $attempt = Cache::lock($key, 2)->get(function () use ($rpm) {
            $bucketKey = 'classify:counter:' . now()->format('YmdHi');
            $count = Cache::increment($bucketKey);
            Cache::put($bucketKey, $count, now()->addMinutes(2));
            return $count <= $rpm;
        });

        if (!$attempt) {
            throw new \RuntimeException('Classification rate limit exceeded. Try again later.');
        }


        if (!$enabled) {
            $category = fake()->randomElement(array_column(TicketCategory::cases(), 'value'));
            return [
                'category' => $category,
                'explanation' => 'Dummy classification (OPENAI_CLASSIFY_ENABLED=false).',
                'confidence' => fake()->randomFloat(2, 40, 95),
            ];
        }


        $system = <<<SYS
            You are an assistant that classifies customer support tickets. Respond with **ONLY** valid JSON object with keys:
            - "category": one of [billing, technical, account, shipping, sales, other]
            - "explanation": short reason for the classification
            - "confidence": number 0-100 (integer or float) representing confidence
            Return JSON only.
            SYS;


        $user = "Subject: {$subject}\n\nBody: {$body}";


        $response = OpenAI::completions()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'temperature' => 0.2,
        ]);


        $content = $response->choices[0]->message->content ?? '';

        $json = $this->extractJson($content);

        return [
            'category' => $json['category'] ?? 'other',
            'explanation' => $json['explanation'] ?? 'No explanation provided.',
            'confidence' => (float) ($json['confidence'] ?? 50),
        ];
    }

    /**
    * Extract the first JSON object from a string.
    */
    private function extractJson(string $content): array
    {
        if (str_starts_with(trim($content), '{')) {
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        }


        if (preg_match('/\{.*\}/s', $content, $m)) {
            return json_decode($m[0], true, 512, JSON_THROW_ON_ERROR);
        }

        return [];
    }
}
