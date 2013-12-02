# Universal Analytics for Laravel 4
#### Add Universal Analytics to your Laravel 4 application easily.

[Universal Analytics](https://support.google.com/analytics/answer/2790010?hl=en) is the new code base for 
[Google Analytics](https://www.google.com/analytics/). This package allows you to easily 
add it to your Laravel 4 application.

## Table of Contents
  1. [Installation](#installation)
  1. [Usage](#usage)
    1. [Overview](#overview)
    1. [Creating new instances](#creating-new-instances)
    1. [Getting an existing instance](#getting-an-existing-instance)
    1. [Calling methods](#calling-methods)
    1. [Rendering](#rendering)
  1. [Configuration](#configuration)
    1. [Debug Mode](#debug-mode)
    1. [Auto Pageviews](#auto-pageviews)
    1. [Accounts](#accounts)
  1. [Release Notes](#release-notes)
  1. [License](#license)
  1. [Finding Help](#finding-help)

## Installation

Installation is simple thanks to [composer](http://getcomposer.org/). First, add the following `require` key to your 
`composer.json` file:
```
"tag-planet/universal-analytics": "dev-master"
```

And run the Composer update command:
```
$ composer update
```

Then, in your `config/app.php` file add `'TagPlanet\UniversalAnalytics\UniversalAnalyticsServiceProvider'` to the 
  end of the `$providers` array:

```php
'providers' => array(
    'Illuminate\Foundation\Providers\ArtisanServiceProvider',
    'Illuminate\Auth\AuthServiceProvider',
    ...
    'TagPlanet\UniversalAnalytics\UniversalAnalyticsServiceProvider',
),
``` 
Also in your `config/app.php` file add `'UniversalAnalytics' => 'TagPlanet\UniversalAnalytics\UniversalAnalyticsFacade'` 
  to the end of the `$aliases` array:

```php
'aliases' => array(
    'App'        => 'Illuminate\Support\Facades\App',
    'Artisan'    => 'Illuminate\Support\Facades\Artisan',
    ...
    'UniversalAnalytics' => 'TagPlanet\UniversalAnalytics\UniversalAnalyticsFacade'
),
```

Next, you'll want to publish the config files:

```
$ php artisan config:publish tag-planet/universal-analytics
```

Now you'll be able to edit the configuration options within `app/config/packages/tag-planet/universal-analytics/settings.php`

## Usage

#### Overview

This package closely replicates the JavaScript code syntax to help developers easily transition to and from using this package. 
Below is an example of the same call, one in JavaScript and the other in PHP:
```
// JavaScript:
ga('create', 'UA-123456-1', {'name': 'foo'});

// PHP
UniversalAnalytics:ga('create', 'UA-123456-1', ['name'=>'foo']);
```

You can use any of the `ga` calls, just like you would with the JavaScript version. This will output the same code. 

```php
UniversalAnalytics:ga('create', 'UA-123456-1', ['name'=>'foo', 'domainName' => 'tagpla.net']);
```

Will output as the following in JS:

```javascript
ga("create", "UA-123456-1", {
    "name": "foo",
    "domainName": "tagpla.net"
});
```

#### Creating new instances

While the option exists to [auto create trackers](#accounts) via the configuration file, you can also create new instances on your own. 
Again, all you'll need to do is call `UniversalAnalytics::ga( ... )` with the same arguments as you would pass in the JavaScript version:

```php
// Setup a new tracker with "foo" as its name:
$fooTracker = UniversalAnalytics:ga('create', 'UA-123456-1', ['name'=>'foo', 'domainName' => 'tagpla.net']);
```

It is highly recommended to pass a name to the tracker, but one will automatically be generated for you in the event it is missing. 
In this case, the name is `foo`, as seen in the options array. If a name hadn't been passed in the naming schema is `tX` where X is the 
count of previous instances. E.g.:

```php
// Name would be "foo":
UniversalAnalytics:ga('create', 'UA-123456-1', ['name'=>'foo']);

// Name would be "t1":
UniversalAnalytics:ga('create', 'UA-123456-2');

// Name would be "bar":
UniversalAnalytics:ga('create', 'UA-123456-3', ['name'=>'bar', 'domainName' => 'tagpla.net']);

// Name would be "t3":
UniversalAnalytics:ga('create', 'UA-123456-4', ['domainName' => 'tagpla.net']);
```

#### Getting an existing instance

Now that you've created a tracker instance, you may need to grab it to call additional methods. If you created the tracker instance via 
the configuration, you'll use the friendly name you used as the key (_See: [Accounts](#accounts)_). 

```php
// Grab the "foo" instance
$tracker = UniversalAnalytics::get('foo');
```


#### Calling methods

It is likely that you'll want to add custom variables (dimensions / metrics) or track certain events. 
Since the argument format is the same as the JavaScript version, explaining all of the options 
is out of scope for this document. You can read up on the format over at [Google's documentation](https://developers.google.com/analytics/devguides/collection/analyticsjs/).

You can push to a single instance once you've grabbed it (_see: [Getting an existing instance](getting-an-existing-instance)_):

```php
// Grab the "foo" instance
$fooTracker = UniversalAnalytics::get('foo');

// Call a pageview event to the "foo" instance
$fooTracker->ga('send', 'pageview');
```

A more complex example, using ecommerce tracking:

```php
// Grab the "foo" instance
$fooTracker = UniversalAnalytics::get('foo');

// Require the ecommerce JS file:
$fooTracker->ga('require', 'ecommerce', 'ecommerce.js');

// Setup a transaction:
$fooTracker->ga('ecommerce:addTransaction', [
    'id'          => $order->id
    'addiliation' => $store->name,
    'revenue'     => $order->total,
    'shipping'    => $order->shipping->cost,
    'tax'         => $order->tax,  
]);
```

In addition to pushing to a single instance, you can also push to all existing instances in one call:

```php
// Have a pageview across all instances
UniversalAnalytics::ga('send', 'pageview');
```

#### Rendering

Now that you've created an instance and pushed data to it, you're going to want to render the JavaScript output at some point. 

##### Render options

There are 3 optional boolean arguments you can pass in to the `render` method.

```php
function render($renderCodeBlock = true, $renderJavaScriptTags = true, $clearData = true);
```

`$renderCodeBlock`, will render the default Universal Analytics code block when set to `true`. By default this is set to `true`. 
If you've already output the code block on the page, set it to `false`. 

```javascript
// A sample of what it'll render when set to true:
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
```

> Remember, you only need to have the code block rendered once per page.

`$renderJavaScriptTags`, will render the `<script> ... </script>` tags around the code. By default this is set to `true`. 
If you're appending the output to an existing JavaScript tag, set it to `false`. 

`$clearData`, will clear all previous calls once the render is complete. If you have multiple renders on a page 
for any reason, you should set this to `true`. Otherwise you'll get duplicate calls.

All of these arguments can be used when rendering all instances or just a single instance.

##### Render all instances

In most cases you'll want to render all of the instances at once. To do this, place the following code just before the `</head>` of your main view layout:

```php
// For blade templates:
{{ UniversalAnalytics::render() }}

// For raw PHP templates:
echo UniversalAnalytics::render();
```

> If you prefer, you can also use a [View composer](http://laravel.com/docs/responses#view-composers).

##### Rendering single instances

You can also render single instances if you need to:

```php
// Grab the "foo" instance
$fooTracker = UniversalAnalytics::get('foo');

// render it!
echo $fooTracker->render();
```


## Configuration

Once you've published your configuration file, you can edit it at `app/config/packages/tag-planet/universal-analytics/settings.php`. 
If you'd like to have configuration files on an enviroment-based level, you can do so via [these instructions](http://laravel.com/docs/configuration#environment-configuration).

#### Debug mode
Debug mode uses a different JS file that outputs information about what is sent to Universal Analytics via the browser console. 
This mode can be enabled by changing `debug` to `true`, or disabled by changing it to `false`. By default this is set to `true`.

```php
'debug' => false,
```

> This should be set to `false` on production domains.

#### Auto Pageviews
When auto pageviews are enabled, any time the `render` call has occured, a 
[pageview event](https://developers.google.com/analytics/devguides/collection/analyticsjs/pages#implementation) 
will automatically be appended. If there is not a need to pass in customized page locations or page titles, then it is recommended 
to leave this enabled.  While the configuration setting is for a global basis, this setting can be overwritten on a per-instance level.

```php
'autoPageview' => true,
```

#### Accounts
While you can manually create new tracker instances (as discussed earlier in this guide) you can also have them auto-created. 
There are 2 different configuration formats, depending on your needs. Both require a friendly name and an account ID. The friendly name 
should be a **unique** alpha-numeric name that is used to identify each tracker. You can use something as simple as "default" to more complex names 
such as "tagplanetInstall". You can read more about tracker names over at  
[Google's documentation](https://developers.google.com/analytics/devguides/collection/analyticsjs/advanced#multipletrackers). Account IDs are also required 
and are provided to you when you create a new account or property within Google Analytics. If you're not sure on what this is, please take a look at 
[Google's help file](https://support.google.com/analytics/answer/1032385?hl=en) to find it.

##### Basic Format
For those that don't need extra configuration options you can use the simplified format. The following example uses the friendly name 
"trackerName" and an account ID of "UA-123456-1":

```php
'accounts' => array(
    'trackerName' => 'UA-123456-1',
),
```

##### Advanced Format
For those that do need extra configuration when you do a create call (e.g. setting the cookie domain or any of the 
[create-only fields](https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#create)), 
you'll need to use the more advanced format. The following examples uses a friendly name of "foobar" and an account of "UA-654321-1":

```php
'accounts' => array(
    'foobar' => array(
        'account' => 'UA-654321-1',
        'options' => array(
            'domainName' => 'foobar.com',
        ),
    ),
),
```

## Release Notes

#### Version 1.0.0
Initial Version

## License
Tag Planet's Universal Analytics for Laravel 4 is free software distributed under the terms of the MIT license.

## Finding Help
Should you have any questions, bug reports, or feedback please utilize our [issue tracker](https://github.com/TagPlanet/universal-analytics/issues).