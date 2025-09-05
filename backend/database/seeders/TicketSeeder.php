<?php
namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        Ticket::factory()->count(30)->create();

        Ticket::create([
            'subject' => 'Invoice shows extra charges',
            'body' => 'Customer reports double-billing on order #12993.',
            'status' => 'open',
            'category' => 'billing',
            'note' => 'Verify Stripe event logs; potential duplicate charge.',
        ]);

        Ticket::create([
            'subject' => 'Password reset link expired',
            'body' => 'User cannot reset password; link expired immediately.',
            'status' => 'in_progress',
            'category' => 'account',
            'note' => 'Check email queue delay; consider increasing token TTL.',
        ]);
    }
}
