<?php namespace TagPlanet\UniversalAnalytics;

use Illuminate\View\Environment;
use Illuminate\Config\Repository;
use Exception;
use InvalidArgumentException;

class UniversalAnalytics
{
    /**
     * Laravel application
     * 
     * @var Illuminate\Foundation\Application
     */
    public $app;
    
    /**
     * Debug Mode
     * 
     * @var bool
     */
    public $debug;
    
    /**
     * Auto Pageview
     * 
     * @var bool
     */
    public $autoPageview;
    
    /**
     * Instances array
     * 
     * Stores each instance
     *
     * @array
     * @protected
     */
    protected $instances = array();

    /**
     * Initializes the class
     * 
     * @param Illuminate\Foundation\Application
     */
    public function __construct($app)
    {
        // Setup our defaults/configs
        $this->app = $app;
        $this->debug = (bool) \Config::get('universal-analytics::config.debug', false);
        $this->autoPageview = (bool) \Config::get('universal-analytics::config.autoPageview', true);
        
        // Check for any trackers we should auto create
        $accountConfig = \Config::get('universal-analytics::config.accounts', false);
        if(is_array($accountConfig) && count($accountConfig))
        {
            // Loop through all of the accounts we're going to use
            foreach($accountConfig as $name => $info)
            {
                if(is_string($info))
                {
                    // If the user didn't want options, they can pass the account string
                    $this->create($info, array('name' => $name));
                }
                else
                {
                    // We have options!
                    if(!isset($info['account']) || empty($info['account']))
                    {
                        throw new InvalidArgumentException('Unspecified Universal Analytics account ID');
                    }
                    
                    // Not sure why you'd use this syntax without adding options, but OK.
                    if(!isset($info['options'])) $info['options'] = array();
                    
                    // Add in our name
                    $info['options']['name'] = $name;
                    
                    // Create it!
                    $this->create($info['account'], $options);
                }
            }
        }
    }

    /**
     * Returns the Laravel application
     * 
     * @return Illuminate\Foundation\Application
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * Find a single tracker instance
     * 
     * @return TagPlanet\UniversalAnalytics\UniversalAnalyticsInstance
     */
    public function get($name)
    {
        if($name == '')
        {
            // If no name was passed, we'll assume they wanted the first (and usually only)
            if(count($this->instances) == 1)
            {
                // Grab the instance without knowing tracker name
                $instances = array_values($this->instances);
                return array_shift($instances);
            }
            
            // More or less than one instances
            throw new InvalidArgumentException('Unspecified Universal Analytics tracker name');
        }
        
        // Positive look up on the tracker name?
        if(isset($this->instances[$name]))
            return $this->instances[$name];
            
        // No UA instance with that tracker name! Are you sure?
        throw new Exception('Universal Analytics instance for "' . $name . '" doesn\'t exist');
    }
    
    /**
     * Create a new tracker instance, or update an existing a new config
     * 
     * @return TagPlanet\UniversalAnalytics\UniversalAnalyticsInstance
     */
    protected function create($account, $config = array())
    {
        if(!isset($config['name']))
        {
            // This shouldn't happen anymore... but just in case, force it!
            $config['name'] = 't' . count($this->instances);
        }
        
        // Grab overall config options
        $debug = $this->debug;
        $autoPageview = $this->autoPageview;
        
        // Finally, make a new instance
        $this->instances[$config['name']] = new UniversalAnalyticsInstance($account, $config, $debug, $autoPageview);
        
        // Return the newly created instance
        return $this->instances[$config['name']];
    }
    
    /**
     * Render the Universal Analytics code
     * 
     * @return string
     */
    public function render($renderedCodeBlock = true, $renderScriptTag = true)
    {
        // Setup our return array
        $js = array();
        
        // Do we need to add script tags?
        if($renderScriptTag)
            $js[] = '<script>';
            
        foreach($this->instances as $instance)
        {
            // Since we could have multiple trackers, we'll want to grab each render
            $js[] = $instance->render($renderedCodeBlock, false) . PHP_EOL;
            
            // Make sure we only render the UA code block once
            $renderedCodeBlock = false;
        }
        
        // Do we need to add script tags?
        if($renderScriptTag)
            $js[] = '</script>';
        
        // Return our combined render
        return implode(PHP_EOL, $js);
    }
    
    /*
     *  Call a Universal Analytics method
     */
    public function ga( )
    {
        // Grab the params
        $params = func_get_args();
        
        // If we have a create call, make sure we initialize a new tracker
        if($params[0] == 'create')
        {
            return call_user_func_array(array($this, 'create'), array_slice($params, 1));
        }
        
        foreach($this->instances as $instance)
        {
            // Call each instance->ga()
            call_user_func_array(array($instance, 'ga'), $params);
        }
    }
}