<?php
/**
 * Phergie plugin to provide URL shortening services for Url plugin (and others) (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 *
 * @link https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten for the canonical source repository
 * @copyright Copyright (c) 2015 Patrick Schwisow (https://github.com/PSchwisow/phergie-irc-plugin-react-url-shorten)
 * @license http://phergie.org/license New BSD License
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */

namespace PSchwisow\Phergie\Plugin\UrlShorten\Adapter;

/**
 * Adapter for gsc.io.
 *
 * @category PSchwisow
 * @package PSchwisow\Phergie\Plugin\UrlShorten
 */
class Gscio extends AbstractAdapter
{
    /**
     * Minimum length to attempt to shorten.
     *
     * @return int
     */
    protected $minimumLength = 19;

    /**
     * Get the URL for the API Request
     *
     * @param $url
     * @return string
     */
    public function getApiUrl($url)
    {
        return 'http://gsc.io/u/?rl=' . rawurlencode($url);
    }

    /**
     * Parse the reply
     *
     * @param $data
     * @param $headers
     * @param $code
     * @return string|false
     */
    public function handleResponse($data, $headers, $code)
    {
        if ($code == 201) {
            return $data;
        }
        return false;
    }
}