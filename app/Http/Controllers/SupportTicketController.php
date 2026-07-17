<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplySupportTicketRequest;
use App\Http\Requests\StoreSupportTicketRequest;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    /**
     * Lista zgłoszeń: personel widzi wszystkie, klient — wyłącznie własne.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $tickets = SupportTicket::query()
            ->with(['user', 'handler'])
            ->when(! $user->isStaff(), fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate(12);

        return view('tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        $this->authorize('create', SupportTicket::class);

        return view('tickets.create');
    }

    public function store(StoreSupportTicketRequest $request): RedirectResponse
    {
        $this->authorize('create', SupportTicket::class);

        $request->user()->tickets()->create([
            'subject' => $request->validated('subject'),
            'message' => $request->validated('message'),
            'status' => SupportTicket::STATUS_OPEN,
        ]);

        return redirect()->route('tickets.index')->with('success', 'Zgłoszenie zostało wysłane.');
    }

    public function show(SupportTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['user', 'handler']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Odpowiedź pracownika — zapisuje treść i zamyka zgłoszenie.
     */
    public function reply(ReplySupportTicketRequest $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('handle', $ticket);

        $ticket->update([
            'response' => $request->validated('response'),
            'status' => SupportTicket::STATUS_CLOSED,
            'handled_by' => $request->user()->id,
            'handled_at' => now(),
        ]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Zgłoszenie zostało obsłużone.');
    }
}
