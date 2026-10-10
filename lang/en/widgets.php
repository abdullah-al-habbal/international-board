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
        'trainers' => [
            'label' => 'Trainers',
            'description' => 'Registered trainers in system',
        ],
        'trainees' => [
            'label' => 'Trainees',
            'description' => 'Total registered trainees',
        ],
        'expired_centers' => [
            'label' => 'Expired Licenses',
            'description' => 'Centers with expired accreditation',
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
        'trainer' => [
            'total_certifications' => [
                'label' => 'Total Certifications',
                'description' => 'Certifications created by you',
            ],
            'this_month' => [
                'label' => 'This Month',
                'description' => 'Certifications created this month',
            ],
            'financial_requests' => [
                'label' => 'Financial Requests',
                'description' => 'Total financial requests submitted',
            ],
        ],
    ],
    'charts' => [
        'accreditation_requests' => [
            'heading' => 'Accreditation Requests',
            'label' => 'Accreditation Requests',
            'empty' => [
                'no_requests' => 'No requests — all done',
                'description' => 'There are currently no accreditation requests to display.',
            ],
        ],
        'monthly_certifications' => [
            'heading' => 'Monthly Certifications',
            'label' => 'Certifications Issued',
            'empty' => [
                'no_data' => 'No certification data for this period',
                'description' => 'There are no certifications recorded for the selected period.',
            ],
        ],
    ],

    'status' => [
        'active' => 'Active',
        'expired' => 'Expired',
        'no_accreditation_period' => 'No accreditation period set',
        'accreditation_expired' => 'Accreditation expired',
        'expires_in_days' => 'Expires in :days days',
        'valid_until' => 'Valid until :date',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'suspended' => 'Suspended',
    ],
    'export' => [
        'title' => 'Export :label?',
        'body' => 'Download the :label data as an Excel file.',
        'download' => 'Download Excel',
        'cancel' => 'Cancel',
    ],
];
