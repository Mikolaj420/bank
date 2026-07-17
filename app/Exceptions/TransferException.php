<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Wyjątek domenowy sygnalizujący, że przelewu nie można zrealizować
 * (np. nieprawidłowy odbiorca, przelew na własne konto, zmieniony status).
 */
class TransferException extends RuntimeException
{
}
