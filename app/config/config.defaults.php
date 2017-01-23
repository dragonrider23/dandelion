<?php
/**
 * DO NOT EDIT THIS FILE
 *
 * User settings should be placed in config.php
 */
return array(
    // Database Configuration
    'db' => [],

    'cheesto' => [
        // Pre-made options for statuses
        'statusOptions' => [
            'Available',
            'Away From Desk',
            'At Lunch',
            'Out for Day',
            'Out',
            'Appointment',
            'Do Not Disturb',
            'Meeting',
            'Out Sick',
            'Vacation'
        ]
    ],

    // Application configuration
    // FQDN/IP for the application
    'hostname' => '',
    // Prefix for cookies managed by Dandelion
    'cookiePrefix' => 'dan_',
    // Name of PHP session for Dandelion, make unique for each instance of Dandelion
    'phpSessionName' => 'session_1',
    // Garbage collection lottery, the odds that a GC run will happen on a session open
    // Default is 1 out of 100.
    'gcLottery' => [1, 100],
    // Session timeout in minutes. Default is 6 hours
    'sessionTimeout' => 360,
    // Debug mode => set to false in prod
    'debugEnabled' => false,
    // Is the app installed, set to false to rerun install script
    'installed' => false,
    // Application title displayed at top of pages
    'appTitle' => 'Dandelion Web Log',
    // Application tagline displayed below title
    'tagline' => '',
    // Application default theme
    'defaultTheme' => 'modern',
    // Cheesto status system is enabled
    'cheestoEnabled' => true,
    // If the public api is enabled
    'publicApiEnabled' => false,
    // If the public api is enabled for whitelisted users only
    'whitelistApiEnabled' => false,
    // Have Dandelion check online for a new version
    'checkForUpdates' => true,
    // URL to check for updates
    'updateUrl' => 'http://blog.onesimussystems.com/dandelion/versioncheck',
);
