<?php

// filePath: lang/en/accreditation.php
return [
    'blocked' => [
        'title' => 'Account Restricted',
        'center_inactive' => 'Your center account is currently inactive. Please contact administration.',
        'trainer_inactive' => 'Your trainer account is currently inactive. Please contact administration.',
        'no_approved_request' => 'You do not have an approved accreditation request on file.',
        'period_expired' => 'Your current accreditation period has expired.',
        'membership_expired' => 'Your membership period has expired.',
    ],

    'banner' => [
        'title' => 'Accreditation Status',
        'active' => 'Your accreditation is active. You have full access to the system.',
        'blocked' => 'Your accreditation is inactive or expired. Access is restricted.',
        'action' => 'Manage Requests',
    ],

    'create_disabled' => [
        'has_active' => 'You already have an active or approved accreditation request.',
    ],

    'errors' => [
        'active_request_exists' => 'An active or pending accreditation request already exists for this account.',
        'time_overlap' => 'Accreditation period overlaps with an existing approved request.',
    ],
];
