<?php

namespace App\Exceptions;

/**
 * Rzucany, gdy saldo rachunku źródłowego nie pokrywa kwoty przelewu.
 * Osobny typ pozwala kontrolerowi zwrócić czytelny komunikat użytkownikowi.
 */
class InsufficientFundsException extends TransferException
{
}
