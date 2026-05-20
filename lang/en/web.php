<?php
// lang\en\web.php
declare(strict_types=1);

return [
    'default_title' => 'National Certification Authority',
    'site_logo' => 'National Certification Authority Logo',

    'footer' => [
        'description' => 'The National Certification Authority issues, verifies, and accredits professional certifications through a trusted network of training centers and certified trainers.',
    ],

    'pages' => [
        'home' => [
            'title' => 'Home',
            'hero_title' => 'Trusted Certification Authority',
            'hero_text' => 'Verify, discover, and connect with certified professionals and accredited training centers.',
            'hero_title_2' => 'Accredited Trainers & Centers',
            'hero_text_2' => 'Browse our network of certified trainers and accredited training centers worldwide.',
        ],
        'about' => [
            'title' => 'About Us',
            'subtitle' => 'Learn more about our mission, vision, and values.',
            'checklist' => [
                'Internationally recognized certification standards.',
                'Transparent and secure verification process.',
                'Network of accredited trainers and centers.',
                'Serving professionals across multiple countries.',
                'Committed to quality and integrity.',
            ],
        ],
        'certifications' => [
            'title' => 'Certification Verification',
            'subtitle' => 'Enter a serial number to verify the authenticity of a certification.',
            'search_title' => 'Verify a Certification',
            'search_placeholder' => 'Enter certification serial or document code...',
            'result_valid' => 'Valid Certification',
            'result_not_found' => 'Certification Not Found',
            'not_found_message' => 'No certification was found with the value ":serial". Please check and try again.',
        ],
        'centers' => [
            'title' => 'Certified Centers',
            'subtitle' => 'Browse our network of accredited training centers.',
        ],
        'trainers' => [
            'title' => 'Certified Trainers',
            'subtitle' => 'Browse our network of accredited professional trainers.',
            'evaluation_title' => 'Trainer Evaluation',
        ],
        'blog' => [
            'title' => 'Latest News',
            'subtitle' => 'Stay updated with our latest news and announcements.',
        ],
    ],

    'features' => [
        'title' => 'Our Services',
        'subtitle' => 'Professional certification services you can trust.',
        'items' => [
            [
                'icon' => 'tf-ion-ios-ribbon-outline',
                'title' => 'Certification Issuance',
                'description' => 'Official certifications issued by accredited centers and trainers.',
                'route' => 'web.certifications.index',
            ],
            [
                'icon' => 'tf-ion-ios-search',
                'title' => 'Serial Verification',
                'description' => 'Instantly verify the authenticity of any issued certification.',
                'route' => 'web.certifications.index',
            ],
            [
                'icon' => 'tf-ion-ios-people-outline',
                'title' => 'Certified Trainers',
                'description' => 'Browse and connect with our network of accredited professional trainers.',
                'route' => 'web.trainers.index',
            ],
            [
                'icon' => 'tf-ion-ios-location-outline',
                'title' => 'Accredited Centers',
                'description' => 'Find accredited training centers in your region.',
                'route' => 'web.centers.index',
            ],
        ],
    ],

    'statistics' => [
        'title' => 'Our Impact in Numbers',
        'subtitle' => 'Trusted by professionals and organizations worldwide.',
        'certifications' => 'Certifications Issued',
        'trainers' => 'Certified Trainers',
        'centers' => 'Accredited Centers',
    ],

    'cta' => [
        'title' => 'Verify a Certification Today',
        'text' => 'Enter a serial number to instantly verify the authenticity and validity of any issued certification.',
        'button' => 'Verify Now',
    ],

    'buttons' => [
        'explore_us' => 'Explore Us',
        'learn_more' => 'Learn More',
        'contact_us' => 'Contact Us',
        'read_more' => 'Read More',
        'search' => 'Search',
        'verify' => 'Verify',
        'verify_now' => 'Verify Now',
        'view_details' => 'View Details',
        'back' => 'Back',
        'clear' => 'Clear Filters',
    ],

    'labels' => [
        'all' => 'All',
        'search_placeholder' => 'Search...',
        'no_results' => 'No results found.',
        'country' => 'Country',
        'specialization' => 'Specialization',
        'specializations' => 'Specializations',
        'serial_number' => 'Serial Number',
        'accreditation_number' => 'Accreditation Number',
        'document_code' => 'Document Code',
        'issue_date' => 'Issue Date',
        'document_type' => 'Document Type',
        'document_types' => 'Document Types',
        'trainer' => 'Trainer',
        'center' => 'Certified Center',
        'trainee' => 'Trainee',
        'status' => 'Status',
        'certifications_count' => 'Certifications Issued',
        'nationality' => 'Nationality',
        'no_trainer' => 'No trainer assigned',
        'not_assigned' => 'N/A',
    ],
];
