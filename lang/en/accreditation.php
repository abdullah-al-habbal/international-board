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

    'messages' => [
        'pending_title' => 'Request already in progress',
        'pending_body' => 'You cannot create a new request because your previous accreditation request is still under review by the admin.',
        'approved_title' => 'Accreditation already active',
        'approved_body' => 'You have an approved accreditation until :end_date. Please wait until it finishes before creating a new request.',
    ],

    'errors' => [
        'pending_request_exists' => 'You already have a pending accreditation request under review.',
        'approved_request_exists' => 'You already have an active approved accreditation. You cannot create a new one until it expires.',
        'time_overlap' => 'Accreditation period overlaps with an existing approved request.',
    ],
];
