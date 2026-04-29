<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DOMPDF Settings
    |--------------------------------------------------------------------------
    */

    'show_warnings' => false,

    'public_path' => null,

    'convert_entities' => true,

    'options' => [

        /**
         * Font directories
         */
        'font_dir' => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),

        /**
         * Temporary directory
         */
        'temp_dir' => sys_get_temp_dir(),

        /**
         * Chroot - Important for security
         */
        'chroot' => realpath(base_path()),

        /**
         * Enable loading remote images and CSS (recommended)
         */
        'enable_remote' => true,

        /**
         * Allowed protocols
         */
        'allowed_protocols' => [
            'data://' => ['rules' => []],
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],

        /**
         * PDF Backend
         */
        'pdf_backend' => 'CPDF',

        /**
         * Default paper settings
         */
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'portrait',

        /**
         * Default font
         */
        'default_font' => 'sans-serif',

        /**
         * Other useful settings
         */
        'dpi' => 96,
        'enable_php' => false,
        'enable_javascript' => false,
        'enable_font_subsetting' => true,
        'font_height_ratio' => 1.1,

        /**
         * HTML5 Parser (always enabled in newer versions)
         */
        'enable_html5_parser' => true,
    ],

];