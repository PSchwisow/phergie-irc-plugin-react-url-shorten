# pschwisow/phergie-irc-plugin-react-url-shorten

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin to provide URL shortening services for Url plugin (and others).

[![Build Status](https://secure.travis-ci.org/PSchwisow/phergie-irc-plugin-react-url-shorten.png?branch=master)](http://travis-ci.org/PSchwisow/phergie-irc-plugin-react-url-shorten) [![Code Climate](https://codeclimate.com/github/PSchwisow/phergie-irc-plugin-react-url-shorten/badges/gpa.svg)](https://codeclimate.com/github/PSchwisow/phergie-irc-plugin-react-url-shorten) [![Test Coverage](https://codeclimate.com/github/PSchwisow/phergie-irc-plugin-react-url-shorten/badges/coverage.svg)](https://codeclimate.com/github/PSchwisow/phergie-irc-plugin-react-url-shorten)

## Install

The recommended method of installation is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "pschwisow/phergie-irc-plugin-react-url-shorten": "dev-master"
    }
}
```

See Phergie documentation for more information on
[installing and enabling plugins](https://github.com/phergie/phergie-irc-bot-react/wiki/Usage#plugins).

## Configuration

```php
return [
    'plugins' => [
        // dependencies
        new \WyriHaximus\Phergie\Plugin\Dns\Plugin, // Handles DNS lookups for the HTTP plugin
        new \WyriHaximus\Phergie\Plugin\Http\Plugin, // Handles the HTTP requests for this plugin
        new \WyriHaximus\Phergie\Plugin\Url\Plugin([
            'handler' => new \WyriHaximus\Phergie\Plugin\Url\DefaultUrlHandler('[%url-short%] %composed-title%')
        ]), // Emits url.shorting.* events

        // configuration
        new \PSchwisow\Phergie\Plugin\UrlShorten\Plugin([
            // All configuration is optional

            // Specify the classname of the shortener adapter
            'service' => 'Gscio', // FQCN or relative to PSchwisow\Phergie\Plugin\UrlShorten\Adapter

            // Override adapter's minimum length to attempt to shorten
            'minimumLength' => 15
        ])
    ]
];
```

## Tests

To run the unit test suite:

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
./vendor/bin/phpunit
```

## License

Released under the BSD License. See `LICENSE`.
