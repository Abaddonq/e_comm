<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the admin panel.
    | The route prefix is obfuscated for security purposes.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Admin Route Prefix
    |--------------------------------------------------------------------------
    |
    | The route prefix for admin panel URLs. This should be a non-standard
    | value to prevent unauthorized access attempts. The prefix is configured
    | via the ADMIN_ROUTE_PREFIX environment variable.
    |
    | Example: If set to 'secure-admin-xyz123', admin URLs will be:
    | https://yoursite.com/secure-admin-xyz123/dashboard
    |
    */

    'route_prefix' => env('ADMIN_ROUTE_PREFIX', 'management-panel-' . substr(md5(env('APP_KEY', 'default')), 0, 8)),

];
