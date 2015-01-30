<?php
/**
 * Phergie plugin to provide URL shortening services for Url plugin (and others) (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 *
 * @link https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten for the canonical source repository
 * @copyright Copyright (c) 2015 Patrick Schwisow (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 * @license http://phergie.org/license New BSD License
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */

namespace PSchwisow\Phergie\Tests\Plugin\UrlShorten\Adapter;

use Phake;
use PSchwisow\Phergie\Plugin\UrlShorten\Adapter\AbstractAdapter;

/**
 * Tests for the Gscio adapter.
 *
 * @category PSchwisow
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */
abstract class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * FQCN of the adapter to test (override in every child class)
     *
     * @var string
     */
    protected $adapterClass;

    /**
     * The shortener adapter.
     *
     * @var AbstractAdapter
     */
    protected $adapter;

    /**
     * Set up before each test case.
     */
    protected function setUp()
    {
        $this->adapter = new $this->adapterClass;
    }

    /**
     * Tests getMinimumLength() and setMinimumLength().
     */
    public function testMinimumLength()
    {
        $min = $this->adapter->getMinimumLength();
        $this->assertInternalType('integer', $min);
        $this->assertGreaterThanOrEqual(0, $min);

        $newValue = $min + 2;
        $this->adapter->setMinimumLength($newValue);
        $this->assertEquals($newValue, $this->adapter->getMinimumLength());
    }

    /**
     * Tests getApiUrl().
     */
    public function testGetApiUrl()
    {
        $apiUrl = $this->adapter->getApiUrl('http://example.com/');
        $this->assertRegExp(
            '/^https?:\/\/([0-9a-z.\-]+)\.([a-z.]{2,6})\/[0-9a-z\-._~+%\/?=]*$/i',
            $apiUrl,
            'getApiUrl failed to return a valid URL'
        );
    }

    /**
     * Tests getApiRequest().
     */
    public function testGetApiRequest()
    {
        $apiUrl = 'http://gsc.io/u/?rl=http%3A%2F%2Fexample.com%2F';
        $shortUrl = 'http://gsc.io/u/112';
        $deferred = $this->getMockDeferred();
        $request = $this->adapter->getApiRequest($apiUrl, $deferred);

        $this->assertInstanceOf('WyriHaximus\Phergie\Plugin\Http\Request', $request);
        $this->assertEquals($apiUrl, $request->getUrl());

        $request->callReject('foo');
        $request->callResolve($shortUrl, '', 201);
        $request->callResolve('Error: bar', '', 500);

        Phake::inOrder(
            Phake::verify($deferred)->reject('foo'),
            Phake::verify($deferred)->resolve($shortUrl),
            Phake::verify($deferred)->reject()
        );
    }

    /**
     * Returns a mock deferred promise.
     *
     * @return \React\Promise\Deferred
     */
    protected function getMockDeferred()
    {
        return Phake::mock('React\Promise\Deferred');
    }
}
