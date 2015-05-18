<?php
/**
 * Phergie plugin to provide URL shortening services for Url plugin (and others) (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 *
 * @link https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten for the canonical source repository
 * @copyright Copyright (c) 2015 Patrick Schwisow (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 * @license http://phergie.org/license Simplified BSD License
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */

namespace PSchwisow\Phergie\Tests\Plugin\UrlShorten;

use Phake;
use PSchwisow\Phergie\Plugin\UrlShorten\Plugin;

/**
 * Tests for the Plugin class.
 *
 * @category PSchwisow
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests handleShortenEvent().
     */
    public function testHandleShortenEvent()
    {
        $adapter = $this->getMockAdapter();
        $deferred = $this->getMockDeferred();
        Phake::when($adapter)->getApiUrl($this->isType('string'))->thenReturn('http://short.com/blah');
        Phake::when($adapter)->getApiRequest('http://short.com/blah', $deferred)->thenReturn('foo');
        $logger = $this->getMockLogger();
        $emitter = $this->getMockEventEmitter();

        $plugin = new Plugin(['service' => $adapter, 'minimumLength' => 10]);
        $plugin->setLogger($logger);
        $plugin->setEventEmitter($emitter);

        $url = 'http://abc.com/abcdefghijklmnop';
        $plugin->handleShortenEvent($url, $deferred);

        Phake::inOrder(
            Phake::verify($logger)->debug($this->stringContains('Shortening url: ')),
            Phake::verify($emitter)->emit('http.request', ['foo'])
        );
    }

    /**
     * Tests handleShortenEvent().
     */
    public function testHandleShortenEventMinLength()
    {
        $adapter = $this->getMockAdapter();
        $deferred = $this->getMockDeferred();
        $logger = $this->getMockLogger();

        $plugin = new Plugin(['service' => $adapter, 'minimumLength' => 20]);
        $plugin->setLogger($logger);

        $url = 'http://abc.com/';
        $plugin->handleShortenEvent($url, $deferred);

        Phake::inOrder(
            Phake::verify($logger)->debug($this->stringContains('Skip shortening url (too short): ')),
            Phake::verify($deferred)->resolve($url)
        );
    }

    /**
     * Tests handleShortenEvent().
     */
    public function testHandleShortenEventSkipHost()
    {
        $adapter = $this->getMockAdapter();
        $deferred = $this->getMockDeferred();
        $logger = $this->getMockLogger();

        $plugin = new Plugin(['service' => $adapter, 'minimumLength' => 20, 'skipHosts' => ['abc.com', 'xyz.net']]);
        $plugin->setLogger($logger);

        $url = 'http://abc.com/abcdefghijklmnop';
        $plugin->handleShortenEvent($url, $deferred);

        Phake::inOrder(
            Phake::verify($logger)->debug($this->stringContains('Skip shortening url (based on hostname): ')),
            Phake::verify($deferred)->resolve($url)
        );
    }

    /**
     * Tests that getSubscribedEvents() returns an array.
     */
    public function testGetSubscribedEvents()
    {
        $plugin = new Plugin;
        $this->assertInternalType('array', $plugin->getSubscribedEvents());
    }

    /**
     * Data provider for testSetAdapter().
     *
     * @return array
     */
    public function dataProviderSetAdapter()
    {
        return [
            ['Gscio', null],
            ['PSchwisow\Phergie\Plugin\UrlShorten\Adapter\Gscio', null],
            [$this->getMockAdapter(), null],
            [$this, 'Shortener service must extend'],
            ['stdClass', 'Shortener service must extend'],
            ['NonExistentClass', 'Class not found'],
        ];
    }

    /**
     * Tests setAdapter().
     *
     * @param mixed $adapter
     * @param string|null $exceptionMessage
     * @dataProvider dataProviderSetAdapter
     */
    public function testSetAdapter($adapter, $exceptionMessage)
    {
        $plugin = new Plugin;

        if (!is_null($exceptionMessage)) {
            $this->setExpectedException('InvalidArgumentException', $exceptionMessage);
        }

        $plugin->setAdapter($adapter);
        $this->assertAttributeInstanceOf(
            'PSchwisow\Phergie\Plugin\UrlShorten\Adapter\AbstractAdapter', 'adapter', $plugin
        );
        if (is_object($adapter)) {
            $this->assertAttributeSame($adapter, 'adapter', $plugin);
        }
    }

    /**
     * Returns a mock shortener adapter.
     *
     * @return \\PSchwisow\Phergie\Plugin\UrlShorten\Adapter\AbstractAdapter
     */
    protected function getMockAdapter()
    {
        return Phake::partialMock('\PSchwisow\Phergie\Plugin\UrlShorten\Adapter\AbstractAdapter');
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

    /**
     * Returns a mock logger.
     *
     * @return \Monolog\Logger
     */
    protected function getMockLogger()
    {
        return Phake::mock('Monolog\Logger');
    }

    /**
     * Returns a mock event emitter.
     *
     * @return \Evenement\EventEmitterInterface
     */
    protected function getMockEventEmitter()
    {
        return Phake::mock('Evenement\EventEmitterInterface');
    }
}
