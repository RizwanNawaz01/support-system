<?php
namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $ticketId) {}

    public function handle(TicketClassifier $classifier): void
    {
        $ticket = Ticket::findOrFail($this->ticketId);

        $result = $classifier->classify($ticket->subject, $ticket->body);

        $newCategory = $ticket->manually_overridden ? $ticket->category : $result['category'];

        $ticket->fill([
            'category' => $newCategory,
            'ai_explanation' => $result['explanation'],
            'ai_confidence' => $result['confidence'],
        ])->save();
    }
}
