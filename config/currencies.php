<?php

declare(strict_types=1);

return [

    /*
     * ISO code used when a financial record has no currency yet. Historical
     * rows created before `currencies` existed carry a NULL `currency_id`;
     * they are rendered with this code rather than being back-stamped, so no
     * stored financial history is rewritten.
     */
    'fallback_code' => 'USD',

    'data' => [
        [
            'code' => 'USD',
            'name' => [
                'en' => 'US Dollar',
                'ar' => 'الدولار الأمريكي',
            ],
            'symbol' => [
                'en' => '$',
                'ar' => '$',
            ],
            'is_default' => true,
        ],
        [
            'code' => 'SYP',
            'name' => [
                'en' => 'Syrian Pound',
                'ar' => 'الليرة السورية',
            ],
            'symbol' => [
                'en' => 'SYP',
                'ar' => 'ل.س',
            ],
            'is_default' => false,
        ],
    ],
];
