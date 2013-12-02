<?php

use TagPlanet\UniversalAnalytics\UniversalAnalyticsInstance;
use Mockery as m;

class UniversalAnalyticsInstanceTest extends PHPUnit_Framework_TestCase {
    
    protected $fixtures;
    
    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->fixtures = $this->setupFixtures();
    }
    
    public function testProperties( )
    {
        // Shorthand
        $accounts = $this->fixtures['accounts'];
        
        // Check the default set properties
        foreach($accounts as $fixture)
        {
            // Create a new instance
            $instance = new UniversalAnalyticsInstance($fixture['account'], $fixture['options'], $fixture['debug'], $fixture['autoPageview']);
            
            // Check to make sure the name is set, with the trailing period
            $name = (isset($fixture['options']['name'])) ? $fixture['options']['name'] . "." : "";
            $this->assertEquals($name, $instance->name);
            
            // Check the debug mode
            $this->assertEquals($fixture['debug'], $instance->debug);
            
            // Check if auto pageviews is set or not
            $this->assertEquals($fixture['autoPageview'], $instance->autoPageview);
        }
    }

    public function testGa()
    {
        // Shorthand
        $accounts = $this->fixtures['accounts'];
        $calls = $this->fixtures['calls'];
       
        // Setup new instances
        foreach($accounts as $fixture)
        {
            // Create a new instance
            $instance = new UniversalAnalyticsInstance($fixture['account'], $fixture['options'], $fixture['debug'], $fixture['autoPageview']);
            
            // Should only have 1 data row (create)
            $this->assertCount(1, $instance->data);
            
            // Loop through calls
            foreach($calls as $call)
            {
                // Call UA
                $instance->ga($call['type'], $call['params']);            
            }
            
            // There should be the same number of calls we just made, plus 1 for the original create
            $this->assertCount((count($calls)+1), $instance->data);
        }
    }
    
    public function testRender( )
    {
        // Shorthand
        $accounts = $this->fixtures['accounts'];
        $calls = $this->fixtures['calls'];
        $renders = $this->fixtures['renders'];
       // Loop through each of the render options
        foreach($renders as $render)
        {
            // Setup new instances
            foreach($accounts as $fixture)
            {
                // Create a new instance
                $instance = new UniversalAnalyticsInstance($fixture['account'], $fixture['options'], $fixture['debug'], $fixture['autoPageview']);
                
                // Loop through calls
                foreach($calls as $call)
                {
                    // Call UA
                    $instance->ga($call['type'], $call['params']);            
                }
                
                $rendered = $instance->render($render['codeBlock'], $render['scriptTag'], $render['clearData']);
            
                // Should we have a code block?
                if($render['codeBlock'])
                {
                    // We should, yes
                    $this->assertContains('GoogleAnalyticsObject', $rendered);
                    
                    // Which means we should have a analytics.js or analytics_debug.js
                    if($instance->debug)
                    {
                        $this->assertContains('analytics_debug.js', $rendered);
                    }
                    else
                    {
                        $this->assertContains('analytics.js', $rendered);
                    }
                }
                else
                {
                    // We shouldn't have a code block here...s
                    $this->assertNotContains('GoogleAnalyticsObject', $rendered);
                }
                
                // Should we have a script tag
                if($render['scriptTag'])
                {
                    $this->assertContains('<script', $rendered);
                }
                else
                {
                    $this->assertNotContains('<script', $rendered);
                }
                
                if($instance->autoPageview)
                {
                    // @TODO: fix this false positive
                    // It should count how many pageviews occured and match
                    $this->assertContains('pageview', $rendered);
                }
                
                
                // Did we clear the data?
                if( $render['clearData'])
                {
                    $this->assertCount(0, $instance->data);
                }
                else
                {
                    // Did we have an autopage view add a call?
                    if($instance->autoPageview)
                    {
                        $count = count($calls)+2;
                    }
                    else
                    {
                        $count = count($calls)+1;
                    }
                    $this->assertCount($count, $instance->data);
                }
            }
        }
    }
    
    public function testClearData( )
    {
        // Shorthand
        $accounts = $this->fixtures['accounts'];
        $calls = $this->fixtures['calls'];
       
        // Setup new instances
        foreach($accounts as $fixture)
        {            
            // Create a new instance
            $instance = new UniversalAnalyticsInstance($fixture['account'], $fixture['options'], $fixture['debug'], $fixture['autoPageview']);
            
            // Loop through calls
            foreach($calls as $call)
            {
                // Call UA
                $instance->ga($call['type'], $call['params']);            
            }
            
            // Clear it
            $instance->clearData();
            
            // We should have nothing remaining!
            $this->assertCount(0, $instance->data);
        }
    }
    
    protected function setupFixtures( )
    {
        return array(
            'accounts' => array(
                array(
                         'account' => 'UA-123456-1',
                         'options' => array(),
                           'debug' => false,
                    'autoPageview' => true,
                ),
                array(
                         'account' => 'UA-123456-2',
                         'options' => array('name' => 'foobar'),
                           'debug' => false,
                    'autoPageview' => true,
                ),
                array(
                         'account' => 'UA-123456-3',
                         'options' => array('domain' => 'localhost'),
                           'debug' => true,
                    'autoPageview' => true,
                ),
                array(
                         'account' => 'UA-123456-3',
                         'options' => array(),
                           'debug' => false,
                    'autoPageview' => false,
                ),
            ),
            'calls' => array(
                array(
                    'type' => 'send', 
                    'params' => 'pageview',
                ),
            ),
            'renders' => array(
                array(
                    'codeBlock' => true,
                    'scriptTag' => true,
                    'clearData' => true,
                ),
                array(
                    'codeBlock' => false,
                    'scriptTag' => true,
                    'clearData' => true,
                ),
                array(
                    'codeBlock' => true,
                    'scriptTag' => false,
                    'clearData' => true,
                ),
                array(
                    'codeBlock' => true,
                    'scriptTag' => true,
                    'clearData' => false,
                ),
                array(
                    'codeBlock' => false,
                    'scriptTag' => false,
                    'clearData' => false,
                ),
            ),
        );
    }
}