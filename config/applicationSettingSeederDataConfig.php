<?php

// filePath: config/applicationSettingSeederDataConfig.php
declare(strict_types=1);

return [
    [
        'key' => 'site_name',
        'value' => 'Certification Board',
        'type' => 'text',
    ],
    [
        'key' => 'site_email',
        'value' => 'admin@certificationboard.com',
        'type' => 'email',
    ],
    [
        'key' => 'site_phone',
        'value' => '+966-11-123-4567',
        'type' => 'phone',
    ],
    [
        'key' => 'site_logo_primary',
        'value' => 'assets/website/images/logo.png',
        'type' => 'url',
    ],
    [
        'key' => 'site_logo_white',
        'value' => 'assets/website/images/logo-white.png',
        'type' => 'url',
    ],
    [
        'key' => 'facebook_url',
        'value' => 'https://facebook.com/certificationboard',
        'type' => 'url',
    ],
    [
        'key' => 'twitter_url',
        'value' => 'https://twitter.com/certboard',
        'type' => 'url',
    ],
    [
        'key' => 'linkedin_url',
        'value' => 'https://linkedin.com/company/certboard',
        'type' => 'url',
    ],
    [
        'key' => 'max_upload_size',
        'value' => '10',
        'type' => 'number',
    ],
    [
        'key' => 'maintenance_mode',
        'value' => 'false',
        'type' => 'boolean',
    ],
    [
        'key' => 'home_testimonials',
        'value' => '[{"name":"Ahmed Al-Farsi","role":"Certified Trainer","text":"The certification process was incredibly smooth and professional. Highly recommended!","avatar":"assets\/website\/images\/about\/member.jpg"},{"name":"Sarah Johnson","role":"Center Manager","text":"We have been an accredited center for 3 years, and the support from the board is top-notch.","avatar":"assets\/website\/images\/about\/member.jpg"},{"name":"Mohammed Al-Saeed","role":"Professional Trainee","text":"Verified my certificate in seconds. Extremely reliable platform.","avatar":"assets\/website\/images\/about\/member.jpg"}]',
        'type' => 'json',
    ],
    [
        'key' => 'trainer_evaluation_text',
        'value' => '<p>Trainer evaluation is based on several criteria including experience, methodology, and student feedback. Please contact us for more details on how to get evaluated.</p>',
        'type' => 'html',
    ],
    [
        'key' => 'whatsapp_number',
        'value' => '966123456789',
        'type' => 'phone',
    ],
    [
        'key' => 'memberships_intro',
        'value' => '{"ar":"<p>يقدم البورد الدولي للتدريب والتأهيل الاحترافي عدة أنواع من العضويات لتناسب احتياجاتكم.</p>","en":"<p>The International Board for Professional Training and Qualification offers several membership types to suit your needs.</p>"}',
        'type' => 'json',
    ],
];
