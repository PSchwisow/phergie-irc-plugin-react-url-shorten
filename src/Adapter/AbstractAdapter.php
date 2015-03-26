<?php
/**
 * Phergie plugin to provide URL shortening services for Url plugin (and others) (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 *
 * @link https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten for the canonical source repository
 * @copyright Copyright (c) 2015 Patrick Schwisow (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 * @license http://phergie.org/license Simplified BSD License
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */

namespace PSchwisow\Phergie\Plugin\UrlShorten\Adapter;

use React\Promise\Deferred;
use WyriHaximus\Phergie\Plugin\Http\Request;

/**
 * Base class for adapters that will connect to each of the possible shortener services.
 *
 * @category PSchwisow
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */
abstract class AbstractAdapter
{
    /**
     * Minimum length to attempt to shorten.
     *
     * @return int
     */
    protected $minimumLength = 0;

    /**
     * Set the minimum length to attempt to shorten.
     *
     * @param int $length
     */
    public function setMinimumLength($length)
    {
        $this->minimumLength = $length;
    }

    /**
     * Get the minimum length to attempt to shorten.
     *
     * @return int
     */
    public function getMinimumLength()
    {
        return $this->minimumLength;
    }

    /**
     * Get the URL for the API Request
     *
     * @param $url
     * @return string
     */
    public abstract function getApiUrl($url);

    /**
     * Create the API Request
     *
     * @param string $apiUrl
     * @param Deferred $deferred
     * @return Request
     */
    public function getApiRequest($apiUrl, $deferred)
    {
        return new Request([
            'url' => $apiUrl,
            'resolveCallback' =>
                function ($data, $headers, $code) use ($deferred) {
                    $shortUrl = $this->handleResponse($data, $headers, $code);
                    if ($shortUrl === false) {
                        $deferred->reject();
                    } else {
                        $deferred->resolve($shortUrl);
                    }
                },
            'rejectCallback' => [$deferred, 'reject']
        ]);
    }

    /**
     * Parse the reply
     *
     * @param $data
     * @param $headers
     * @param $code
     * @return string|false
     */
    public abstract function handleResponse($data, $headers, $code);
}
