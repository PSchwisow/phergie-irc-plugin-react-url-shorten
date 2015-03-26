<?php
/**
 * Phergie plugin to provide URL shortening services for Url plugin (and others) (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 *
 * @link https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten for the canonical source repository
 * @copyright Copyright (c) 2015 Patrick Schwisow (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 * @license http://phergie.org/license Simplified BSD License
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */

namespace PSchwisow\Phergie\Tests\Plugin\UrlShorten\Adapter;

/**
 * Tests for the Gscio adapter.
 *
 * @category PSchwisow
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */
class GscioTest extends AbstractAdapterTest
{
    /**
     * FQCN of the adapter to test (override in every child class)
     *
     * @var string
     */
    protected $adapterClass = 'PSchwisow\Phergie\Plugin\UrlShorten\Adapter\Gscio';

    /**
     * Tests handleResponse().
     */
    public function testHandleResponse()
    {
        $out = $this->adapter->handleResponse('http://gsc.io/u/112', null, 201);
        $this->assertRegExp(
            '/^https?:\/\/([0-9a-z.\-]+)\.([a-z.]{2,6})\/[0-9a-z\-._~+%\/?=]*$/i',
            $out,
            'handleResponse failed to return a valid URL'
        );

        $out = $this->adapter->handleResponse('foo', null, 500);
        $this->assertFalse($out);
    }
}
