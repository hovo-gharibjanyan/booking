<?php

return [
    'credentials' => [
        /*
         * The path to the service account credentials file.
         *
         * If you have set the GOOGLE_APPLICATION_CREDENTIALS environment variable,
         * the SDK will automatically use it.
         */
        'file' => storage_path('app/firebase-credentials.json'),

        'auto_discovery' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | The project ID is a unique identifier for a Firebase project.
    |
    | It can be found in the project settings of the Firebase console.
    |
    */
    'project_id' => env('FIREBASE_PROJECT_ID'),
];