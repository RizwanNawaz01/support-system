<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Jobs\ClassifyTicket;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query();

        if ($search = $request->input('search')) {
            $query->where('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $tickets = $query->latest()->paginate(15);

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = Ticket::create($request->validated());

        return new TicketResource($ticket);
    }

    public function show(Ticket $ticket)
    {
        return new TicketResource($ticket);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $data = $request->validated();

        if (isset($data['category']) && $data['category'] !== $ticket->category) {
            $data['manually_overridden'] = true;
        }

        $ticket->update($data);

        return new TicketResource($ticket);
    }

    public function classify(Ticket $ticket)
    {
        ClassifyTicket::dispatch($ticket->id);

        return response()->json(['message' => 'Classification job dispatched']);
    }
}
