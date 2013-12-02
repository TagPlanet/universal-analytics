<?php

use TagPlanet\UniversalAnalytics\UniversalAnalytics;
use Mockery as m;

class UniversalAnalyticsTest extends PHPUnit_Framework_TestCase {

    /**
     * UA instance
     *
     * @var TagPlanet/UniversalAnalytics/UniversalAnalytics
     */
    protected $universalAnalytics;
    
    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        // Mocks the application
        $app = $this->mockApp();

        // Setup the new UA
        $this->universalAnalytics = new UniversalAnalytics($app);
    }

    public function testGa()
    {
        // Check for instance creation
        $instance = $this->universalAnalytics->ga('create', 'UA-123456-1');
        $this->assertInstanceOf('TagPlanet\UniversalAnalytics\UniversalAnalyticsInstance', $instance);
        
        // Since this isn't a create call, we shouldn't get anything
        $return = $this->universalAnalytics->ga('send', 'pageview');
        $this->assertNull($return);
        
        // Make sure we automatically got a name
        $this->assertEquals('t0.', $instance->name);
        
        // Make sure we automatically got a name
        $secondInstance = $this->universalAnalytics->ga('create', 'UA-654321-1', array('name' => 'foobar'));
        $this->assertEquals('foobar.', $secondInstance->name);
    }
    
    public function testGet( )
    {
        // Create a dummy object
        $this->universalAnalytics->ga('create', 'UA-654321-1', array('name' => 'foobar'));
        
        // Grab it & test it
        $instance = $this->universalAnalytics->get('foobar');
        $this->assertInstanceOf('TagPlanet\UniversalAnalytics\UniversalAnalyticsInstance', $instance);
        
    }
    
    private function mockApp()
    {
        // Mock our app
        $app = array();
        $app['config'] = m::mock( 'Config' );
        $app['config']->shouldReceive( 'get' )
            ->with( 'universal-analytics::config.debug', false )
            ->andReturn( true );
        $app['config']->shouldReceive( 'get' )
            ->with( 'universal-analytics::config.autoPageview', true )
            ->andReturn( true );
        $app['config']->shouldReceive( 'get' )
            ->with( 'universal-analytics::config.accounts', false )
            ->andReturn( true );
        return $app;
    }

}