<?php

declare(strict_types=1);

return [
    'errors' => [
        'total_payment_not_positive' => 'The total amount must be greater than zero.',
        'amount_paid_negative' => 'The paid amount cannot be negative.',
        'amount_paid_exceeds_total' => 'The paid amount (:paid) cannot exceed the total amount (:total).',
        'currency_in_use' => 'The currency :code is used by existing financial records and cannot be deleted.',
    ],
];
