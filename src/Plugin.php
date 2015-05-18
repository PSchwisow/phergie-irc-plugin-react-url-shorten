<?php
/**
 * Phergie plugin to provide URL shortening services for Url plugin (and others) (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 *
 * @link https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten for the canonical source repository
 * @copyright Copyright (c) 2015 Patrick Schwisow (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 * @license http://phergie.org/license Simplified BSD License
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */

namespace PSchwisow\Phergie\Plugin\UrlShorten;

use Phergie\Irc\Bot\React\AbstractPlugin;
use PSchwisow\Phergie\Plugin\UrlShorten\Adapter\AbstractAdapter;
use React\Promise\Deferred;

/**
 * Plugin class.
 *
 * @category PSchwisow
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */
class Plugin extends AbstractPlugin
{
    /**
     * The adapter class to use if none is specified
     */
    const DEFAULT_ADAPTER = 'PSchwisow\Phergie\Plugin\UrlShorten\Adapter\Gscio';

    /**
     * The shortener adapter.
     *
     * @var AbstractAdapter
     */
    private $adapter;

    /**
     * List of hosts not to shorten (presumably because the URL is already shortened).
     *
     * @var array
     */
    private $skipHosts = [
        'goo.gl',
        'lnkd.in',
        't.co',
        'youtu.be',
        'gsc.io',
        'is.gd',
    ];

    /**
     * Accepts plugin configuration.
     *
     * Supported keys:
     *
     * service - classname of the adapter to use
     *   (either relative to PSchwisow\Phergie\Plugin\UrlShorten\Adapter or FQCN)
     *
     * minimumLength - minimum length of URL to attempt to shorten (overrides what is set in the adapter)
     *
     * skipHosts - array of hostname to not attempt to shorten
     *
     * disableDefaultSkipHosts - when set to true, disables the default list of hostnames not to attempt to shorten
     *
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        $this->setAdapter(isset($config['service']) ? $config['service'] : self::DEFAULT_ADAPTER);

        if (isset($config['minimumLength'])) {
            $this->adapter->setMinimumLength(intval($config['minimumLength']));
        }

        $disableDefaults = isset($config['disableDefaultSkipHosts']) && $config['disableDefaultSkipHosts'] == true;
        if (isset($config['skipHosts']) && is_array($config['skipHosts'])) {
            if ($disableDefaults) {
                $this->skipHosts = $config['skipHosts'];
            } else {
                $this->skipHosts = array_merge($this->skipHosts, $config['skipHosts']);
            }
        } elseif ($disableDefaults) {
            $this->skipHosts = [];
        }
    }

    /**
     * Set adapter
     *
     * @param string|\PSchwisow\Phergie\Plugin\UrlShorten\Adapter\AbstractAdapter
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setAdapter($adapter)
    {
        if (is_string($adapter)) {
            if (class_exists($adapter)) {
                $class = $adapter;
            } elseif (class_exists("PSchwisow\\Phergie\\Plugin\\UrlShorten\\Adapter\\{$adapter}")) {
                $class = "PSchwisow\\Phergie\\Plugin\\UrlShorten\\Adapter\\{$adapter}";
            } else {
                throw new \InvalidArgumentException("Class not found: '$adapter'");
            }
            $adapter = new $class;
        }

        if (!$adapter instanceof AbstractAdapter) {
            throw new \InvalidArgumentException(
                'Shortener service must extend PSchwisow\Phergie\Plugin\UrlShorten\Adapter\AbstractAdapter'
            );
        }
        $this->adapter = $adapter;

    }

    /**
     * Get the events that this plugin handles
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'url.shorting.all' => 'handleShortenEvent',
        ];
    }

    /**
     * Log debugging messages
     *
     * @param string $message
     */
    public function logDebug($message)
    {
        $this->logger->debug('[UrlShorten]' . $message);
    }

    /**
     * Handle the event
     *
     * @param string $url
     * @param \React\Promise\Deferred $deferred
     */
    public function handleShortenEvent($url, Deferred $deferred)
    {
        $requestId = uniqid();
        // Only urls longer than the minimum length should be shortened
        if (strlen($url) < $this->adapter->getMinimumLength()) {
            $this->logDebug('[' . $requestId . ']Skip shortening url (too short): ' . $url);
            $deferred->resolve($url);
            return;
        }

        // Check if URL host is one we're skipping
        $host = parse_url($url, \PHP_URL_HOST);
        if (in_array($host, $this->skipHosts)) {
            $this->logDebug('[' . $requestId . ']Skip shortening url (based on hostname): ' . $url);
            $deferred->resolve($url);
            return;
        }

        $this->logDebug('[' . $requestId . ']Shortening url: ' . $url);
        $request = $this->adapter->getApiRequest($this->adapter->getApiUrl($url), $deferred);
        $this->getEventEmitter()->emit('http.request', [$request]);
    }
}
