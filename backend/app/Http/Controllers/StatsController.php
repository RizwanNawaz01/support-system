<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Routing\Controller;

class StatsController extends Controller
{
    public function __invoke()
    {
        return [
            'total' => Ticket::count(),
            'by_status' => Ticket::select('status')
                ->selectRaw('count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_category' => Ticket::select('category')
                ->selectRaw('count(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category'),
            'avg_confidence' => Ticket::whereNotNull('ai_confidence')->avg('ai_confidence'),
        ];
    }
}
