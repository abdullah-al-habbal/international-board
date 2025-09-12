<?php

declare(strict_types=1);

return [
    'stats' => [
        'total_centers' => [
            'label' => 'Total Centers',
            'description' => 'Certified centers in system',
        ],
        'active_centers' => [
            'label' => 'Active Centers',
            'description' => 'Currently active centers',
        ],
        'total_certifications' => [
            'label' => 'Total Certifications',
            'description' => 'Certificates issued',
        ],
        'pending_requests' => [
            'label' => 'Pending Requests',
            'description' => 'Awaiting review',
        ],
        'admin_users' => [
            'label' => 'Admin Users',
            'description' => 'System administrators',
        ],
        'monthly_certifications' => [
            'label' => 'Certifications This Month',
            'description' => 'Issued this month',
        ],
        'this_month' => [
            'label' => 'This Month',
            'description' => 'Certificates issued this month',
        ],
        'accreditation_status' => [
            'label' => 'Accreditation Status',
            'description' => 'Current accreditation status',
        ],
    ],
    'charts' => [
        'accreditation_requests' => [
            'heading' => 'Accreditation Requests',
            'label' => 'Accreditation Requests',
        ],
        'monthly_certifications' => [
            'heading' => 'Monthly Certifications',
            'label' => 'Certifications Issued',
        ],
    ],
    'status' => [
        'active' => 'Active',
        'expired' => 'Expired',
        'no_accreditation_period' => 'No accreditation period set',
        'accreditation_expired' => 'Accreditation expired',
        'expires_in_days' => 'Expires in :days days',
        'valid_until' => 'Valid until :date',
    ],
];
