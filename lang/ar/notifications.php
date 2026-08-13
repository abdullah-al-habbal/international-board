<?php

return [
    'greeting' => 'مرحباً :name!',
    'thank_you' => 'شكراً لاستخدامك تطبيقنا!',
    'view_request' => 'عرض الطلب',
    'renew_accreditation' => 'تجديد الاعتماد',

    'accreditation_status_changed' => [
        'subject' => 'تم تحديث حالة طلب الاعتماد',
        'message' => 'تم تغيير حالة طلب الاعتماد رقم :request_id إلى :status.',
        'admin_notes' => 'ملاحظات الإدارة',
    ],

    'accreditation_expiring' => [
        'subject' => 'الاعتماد على وشك الانتهاء',
        'message' => 'سينتهي اعتمادك خلال :days أيام في :expiry_date. يرجى التجديد في أقرب وقت ممكن.',
    ],

    'admin_action_performed' => [
        'title' => 'طلب جديد: :label',
        'body' => ':actor_type “:actor_name” :action :label.',
        'actor' => [
            'center' => 'المركز المعتمد',
            'trainer' => 'المدرب',
        ],
        'created' => 'أنشأ',
        'updated' => 'حدّث',
        'deleted' => 'حذف',
        'view' => 'عرض',
    ],
];
