<?php

declare(strict_types=1);

return [
    'errors' => [
        'total_payment_not_positive' => 'يجب أن يكون المبلغ الإجمالي أكبر من صفر.',
        'amount_paid_negative' => 'لا يمكن أن يكون المبلغ المدفوع سالباً.',
        'amount_paid_exceeds_total' => 'لا يمكن أن يتجاوز المبلغ المدفوع (:paid) المبلغ الإجمالي (:total).',
        'currency_in_use' => 'العملة :code مستخدمة في سجلات مالية قائمة ولا يمكن حذفها.',
    ],
];
