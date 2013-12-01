<?php namespace TagPlanet\UniversalAnalytics;

class UniversalAnalyticsFacade extends \Illuminate\Support\Facades\Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'universal-analytics'; }

}