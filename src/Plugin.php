<?php
/**
 * Phergie plugin to provide URL shortening services for Url plugin (and others) (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 *
 * @link https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten for the canonical source repository
 * @copyright Copyright (c) 2015 Patrick Schwisow (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 * @license http://phergie.org/license New BSD License
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */

namespace PSchwisow\Phergie\Plugin\UrlShorten;

use Phergie\Irc\Bot\React\AbstractPlugin;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Event\EventInterface as Event;
use React\Promise\Deferred;
use WyriHaximus\Phergie\Plugin\Url\UrlInterface;

/**
 * Plugin class.
 *
 * @category PSchwisow
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */
class Plugin extends AbstractPlugin
{
    /**
     * Accepts plugin configuration.
     *
     * Supported keys:
     *
     *
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {

    }

    /**
     *
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
        $this->logDebug('[' . $requestId . ']Shortening url: ' . $url);

        // TODO some real logic
        $shortUrl = 'http://example.com/' . urlencode($url);

        $deferred->resolve($shortUrl);
    }
}
