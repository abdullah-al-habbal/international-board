<?php

return [
    'greeting' => 'Hello :name!',
    'thank_you' => 'Thank you for using our application!',
    'view_request' => 'View Request',
    'renew_accreditation' => 'Renew Accreditation',

    'accreditation_status_changed' => [
        'subject' => 'Accreditation Request Status Updated',
        'message' => 'Your accreditation request #:request_id status has been changed to :status.',
        'admin_notes' => 'Admin Notes',
    ],

    'accreditation_expiring' => [
        'subject' => 'Accreditation Expiring Soon',
        'message' => 'Your accreditation will expire in :days days on :expiry_date. Please renew as soon as possible.',
    ],

    'admin_action_performed' => [
        'title' => 'New :label',
        'body' => ':actor_type “:actor_name” :action :label.',
        'actor' => [
            'center' => 'Certified Center',
            'trainer' => 'Trainer',
        ],
        'created' => 'created a new',
        'updated' => 'updated',
        'deleted' => 'deleted',
        'view' => 'View',
    ],
];
