<?php

return array(
    /*
     * Debug Mode
     * 
     * When set to true, debug mode will use ua_debug.js instead analytics.js 
     * which pushes information to the user's console about the requests made
     *
     * @bool
     */
    'debug' => true,
    
    /*
     * Auto Pageview
     * 
     * When enabled, this will add a "send" pageview event upon render.
     * This can also be enabled or disabled on a per-tracker level via:
     * 
     * UniversalAnalytics::get('UA-123456-1')->autoPageview = false;
     */
    'autoPageview' => true,
    
    /*
     * Account IDs
     * 
     * Your account ID, provided by Google, will let the server know where to 
     * send the data to. All accounts should have a friendly name alpha-numeric key
     * that allows you to access it at a later time. 
     *
     * 
     * For basic installations, simply use a name => account key/value pair:
     *
     * 'trackerName' => 'UA-123456-1',
     *
     * 
     * If you need to pass in more options with the create call (e.g. setting a domain)
     * you'll need to create an array with an 'account' string and 'options' associative 
     * array of configuration values:
     * 
     * 'foobar' => array(
     *    'account' => 'UA-000000-0',
     *    'options' => array(
     *      'domainName' => 'foobar.com',
     *    ),
     * ),
     * 
     * The code will use the friendly name you used for the 'name' field in the 
     * configuration option by default. You can override this in the configuration "name" field:
     * 
     * 'foobar' => array(
     *    'account' => 'UA-000000-0',
     *    'options' => array(
     *      'domainName' => 'foobar.com',
     *      'name' => 'customName',
     *    ),
     * ),
     * 
     * Read more about working with tracker names:
     * https://developers.google.com/analytics/devguides/collection/analyticsjs/advanced#multipletrackers
     */
    'accounts' => array(
        'trackerName' => 'UA-123456-1',
    ),
);