<?php

return [
    'app' => \App\Native\NativeApp::class,
    
    'windows' => [
        'main' => [
            'width' => 1280,
            'height' => 720,
            'min_width' => 1024,
            'min_height' => 600,
            'resizable' => true,
            'fullscreenable' => true,
            'title' => 'A.M Pharmacy System',
        ],
    ],
    
    'build' => [
        'app_id' => 'com.ampharmacy.app',
        'product_name' => 'AM Pharmacy System',
        'copyright' => 'Copyright © 2024 A.M Pharmacy',
        'output_directory' => 'build',
    ],
];