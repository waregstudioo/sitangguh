<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Set CLOUDINARY_URL (cloudinary://API_KEY:API_SECRET@CLOUD_NAME) in your
    | environment variables (e.g. Render). When configured, compressed report
    | images are uploaded and served directly from Cloudinary.
    |
    */

    'url' => env('CLOUDINARY_URL'),

    'folder' => env('CLOUDINARY_FOLDER', 'kalikurmo/reports'),
];
