<?php

return [
    /**
     * The prefix applied to all the routes, eg. /translations/home
     */
    'prefix' => 'translations',

    /**
     * The list of middlewares that all routes should use.
     * You can use this to authenticate users into the dashboard via the appropriate middleware.
     */
    'middlewares' => ['web', 'auth']
];