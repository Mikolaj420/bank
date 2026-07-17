<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    /** Zgłoszenie widzi jego autor oraz personel banku. */
    public function view(User $user, SupportTicket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->isStaff();
    }

    /** Zgłoszenia zakłada klient. */
    public function create(User $user): bool
    {
        return $user->isClient();
    }

    /** Zgłoszenie obsługuje (odpowiada/zamyka) pracownik banku. */
    public function handle(User $user, SupportTicket $ticket): bool
    {
        return $user->isEmployee() && $ticket->isOpen();
    }
}
