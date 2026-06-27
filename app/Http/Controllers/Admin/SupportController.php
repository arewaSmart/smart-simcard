<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupportController extends Controller
{
    /**
     * Display administrative tickets listing with statistics and search filters.
     */
    public function index(Request $request): View
    {
        $query = Ticket::with(['user', 'messages']);

        // Search filter (Subject, user name, or email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        // Compute dashboard support stats
        $totalTickets    = Ticket::count();
        $openTickets     = Ticket::where('status', Ticket::STATUS_OPEN)->count();
        $respondedTickets = Ticket::where('status', Ticket::STATUS_RESPONDED)->count();
        $closedTickets   = Ticket::where('status', Ticket::STATUS_CLOSED)->count();

        return view('admin.manage.support.index', compact(
            'tickets',
            'totalTickets',
            'openTickets',
            'respondedTickets',
            'closedTickets'
        ));
    }

    /**
     * Display a specific support ticket details for admin viewing and response.
     */
    public function show(Ticket $ticket): View
    {
        $ticket->load(['messages.user', 'user']);

        return view('admin.manage.support.show', compact('ticket'));
    }

    /**
     * Store admin reply and transition the ticket status to 'responded'.
     */
    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        DB::beginTransaction();
        try {
            $ticketMessage = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => Auth::id(),
                'message'   => $request->message,
                'is_admin'  => true,
            ]);

            // Set ticket status to 'responded'
            $ticket->status = Ticket::STATUS_RESPONDED;
            $ticket->save();

            DB::commit();

            try {
                \Illuminate\Support\Facades\Mail::to($ticket->user->email)->send(new \App\Mail\AdminRepliedMail($ticket, $ticketMessage));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send AdminRepliedMail for Ticket #' . $ticket->id . ': ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Support ticket reply sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to send reply. Please try again.');
        }
    }

    /**
     * Explicitly update support ticket status (e.g. close or reopen).
     */
    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:open,responded,closed'],
        ]);

        $ticket->status = $request->status;
        $ticket->save();

        $action = match ($request->status) {
            Ticket::STATUS_CLOSED => 'closed',
            Ticket::STATUS_OPEN => 'reopened',
            default => 'updated',
        };

        if ($request->status === Ticket::STATUS_CLOSED) {
            try {
                \Illuminate\Support\Facades\Mail::to($ticket->user->email)->send(new \App\Mail\TicketClosedMail($ticket));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send TicketClosedMail for Ticket #' . $ticket->id . ': ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Ticket #' . $ticket->id . ' status has been successfully ' . $action . '.');
    }
}
