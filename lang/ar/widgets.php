<?php

// filePath: lang/ar/widgets.php
declare(strict_types=1);

return [
    'stats' => [
        'total_centers' => [
            'label' => 'إجمالي المراكز',
            'description' => 'المراكز المعتمدة في النظام',
        ],
        'active_centers' => [
            'label' => 'المراكز النشطة',
            'description' => 'المراكز النشطة حالياً',
        ],
        'total_certifications' => [
            'label' => 'إجمالي الشهادات',
            'description' => 'الشهادات المصدرة',
        ],
        'pending_requests' => [
            'label' => 'الطلبات المعلقة',
            'description' => 'في انتظار المراجعة',
        ],
        'admin_users' => [
            'label' => 'المستخدمون الإداريون',
            'description' => 'مديرو النظام',
        ],
        'trainers' => [
            'label' => 'المدربون',
            'description' => 'المدربون المسجلون في النظام',
        ],
        'expired_centers' => [
            'label' => 'انتهت تراخيص المراكز',
            'description' => 'المراكز التي انتهت صلاحية اعتمادها',
        ],
        'monthly_certifications' => [
            'label' => 'شهادات هذا الشهر',
            'description' => 'المصدرة هذا الشهر',
        ],
        'this_month' => [
            'label' => 'هذا الشهر',
            'description' => 'الشهادات المصدرة هذا الشهر',
        ],
        'accreditation_status' => [
            'label' => 'حالة الاعتماد',
            'description' => 'حالة الاعتماد الحالية',
        ],
    ],
    'charts' => [
        'accreditation_requests' => [
            'heading' => 'طلبات الاعتماد',
            'label' => 'طلبات الاعتماد',
            'empty' => [
                'no_requests' => 'لا توجد طلبات — كل شيء مكتمل',
                'description' => 'لا توجد طلبات اعتماد لعرضها حالياً.',
            ],
        ],
        'monthly_certifications' => [
            'heading' => 'الشهادات الشهرية',
            'label' => 'الشهادات المصدرة',
            'empty' => [
                'no_data' => 'لا توجد بيانات شهادات لهذه الفترة',
                'description' => 'لا توجد شهادات مسجلة للفترة المحددة.',
            ],
        ],
    ],

    'status' => [
        'active' => 'نشط',
        'expired' => 'منتهي الصلاحية',
        'no_accreditation_period' => 'لم يتم تحديد فترة الاعتماد',
        'accreditation_expired' => 'انتهت صلاحية الاعتماد',
        'expires_in_days' => 'ينتهي خلال :days أيام',
        'valid_until' => 'صالح حتى :date',
        'inactive' => 'غير نشط',
        'pending' => 'قيد الانتظار',
        'suspended' => 'موقوف',
    ],
    'export' => [
        'title' => 'تصدير :label ؟',
        'body' => 'تحميل بيانات :label كملف Excel.',
        'download' => 'تحميل Excel',
        'cancel' => 'إلغاء',
    ],
];
