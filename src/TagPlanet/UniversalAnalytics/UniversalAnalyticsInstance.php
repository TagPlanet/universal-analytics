<?php namespace TagPlanet\UniversalAnalytics;

use Exception;
use InvalidArgumentException;

class UniversalAnalyticsInstance
{
    /**
     * Debug Mode
     * 
     * @var bool
     */
    public $debug;
    
    /**
     * Auto pageview
     * 
     * @var bool
     */
    public $autoPageview;
    
    /** 
     * Account ID
     *
     * @protected
     * @var string
     */
    protected $account = '';
    
    /** 
     * Tracker Name
     *
     * @protected
     * @var string
     */
    protected $name = '';
    
    /** 
     * UA Data
     *
     * @protected
     * @var array
     */
    protected $data = array( );
    
    /** 
     * UniversalAnalytics Instance
     *
     * @param string Account ID
     * @param array Options
     */
    public function __construct($account, $options = array(), $debug = false, $autoPageview = true)
    {    
        if(is_array($options) && count($options))
        {
            // We have options, let's make sure they are used
            $this->ga('create', $account, $options);
            
            if(isset($options['name']))
            {
                // Grab the name, since it exists!
                $this->name = $options['name'] . '.';
            }
        }
        else
        {
            // No options, sad day
            $this->ga('create', $account);
        }
        
        // Do we need debugging?
        $this->debug = (bool) $debug;
        
        // Do we need to add a pageview?
        $this->autoPageview = (bool) $autoPageview;
    }
    
    /** 
     * GA call
     *
     * Replicates the call within analytics.js
     */
    public function ga( )
    {
        // Grab the args (since they can vary in count)
        $args = func_get_args();
        
        if(count($args) < 2)
        {
            // Error checking
            throw new InvalidArgumentException('Missing arguments for ga() call. Minimum of 2 required');
        }
        
        // Grab the correct items
        $method = array_shift($args);
        $params = $args;
        
        // Push to the array
        $this->data[] = array('method' => $method, 'params' => $params);
    }
    
    /*
     * Render UA
     * 
     * @param bool Render UA code block
     * @param bool Render script tags
     * 
     * @return string
     */
    public function render($renderCodeBlock = true, $renderScriptTag = true)
    {        
        if($this->autoPageview)
        {
            // User wanted us to automatically add the page view call..
            $this->ga('send', 'pageview');
        }
        
        $js = array();
        
        // Do we need to add script tags?
        if($renderScriptTag)
            $js[] = '<script>';
        
        if($renderCodeBlock)
        {
            // Setup the default code block
            $fileName = ($this->debug) ? 'analytics_debug.js' : 'analytics.js';
            $js[] = <<< EOT
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/{$fileName}','ga');
EOT;
        }
        
        // Let's go through all of the data we've had called to us
        foreach($this->data as $call)
        {
            // Let's prettify the parameters
            $params = $this->renderParams($call['params']);
            
            // Remember, some methods require different formats
            switch($call['method'])
            {
                case 'create':
                    $js[] = "ga(\"{$call['method']}\", {$params});";
                break;
                case 'send':
                case 'set':
                default:
                    $js[] = "ga(\"{$this->name}{$call['method']}\", {$params});";
                break;
            }
        
        }
        
        // Do we need to add script tags?
        if($renderScriptTag)
            $js[] = '</script>';
        
        // Return our joined JS
        return implode($js, PHP_EOL);
    }
    
    /*
     * Render UA call parameters
     * 
     * @param mixed parameters
     * 
     * @return string
     */
    private function renderParams($parameters)
    {
        if(is_array($parameters))
        {
            // We have an array, we'll need to make sure we get all of the items setup correctly
            $paramArray = array();
            foreach($parameters as $param)
            {
                // make it JS ready for all params
                $paramArray[] = json_encode($param, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            }
            $parameters = implode(", ", $paramArray);
        }
        
        // Return what we were given.. just in a better format!
        return $parameters;
    }
}