<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ustawienia domenowe banku
    |--------------------------------------------------------------------------
    |
    | Przelewy o kwocie równej lub większej od progu wymagają akceptacji
    | kierownika przed zaksięgowaniem środków u odbiorcy.
    |
    */

    'approval_threshold' => (float) env('BANK_APPROVAL_THRESHOLD', 10000),

    // Domyślna waluta kont.
    'currency' => 'PLN',

    // Liczba cyfr generowanego numeru rachunku (format NRB — 26 cyfr).
    'account_number_length' => 26,

];
