<?php
namespace Database\Factories;

use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Ticket> */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $categories = array_column(TicketCategory::cases(), 'value');
        $statuses = array_column(TicketStatus::cases(), 'value');

        return [
            'id' => Str::ulid()->toBase32(),
            'subject' => fake()->sentence(6),
            'body' => fake()->paragraphs(asText: true),
            'status' => fake()->randomElement($statuses),
            'category' => fake()->randomElement($categories),
            'note' => fake()->boolean(40) ? fake()->sentence(12) : null,
            'ai_explanation' => null,
            'ai_confidence' => null,
            'manually_overridden' => false,
        ];
    }
}
