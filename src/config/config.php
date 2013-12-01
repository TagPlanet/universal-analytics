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
     * send the data to. This should be an "account id" => "option array" 
     * key/value pair. An example is below:
     * 
     * 'UA-000000-0' => array(
     *    'name' => 'foobar',
     *    'domainName' => 'foobar.com',
     * ),
     *
     * If the options are not needed, you can simply add it as a string:
     * 'UA-123456-1',
     */
    'accounts' => array(
        
    ),
);