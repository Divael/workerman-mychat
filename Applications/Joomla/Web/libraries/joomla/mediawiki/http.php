<?php
/**
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;

/**
 * HTTP client class for connecting to a MediaWiki instance.
 *
 * @since  12.3
 */
class JMediawikiHttp extends JHttp
{
    /**
     * Constructor.
     *
     * @param   Registry        $options    Client options object.
     * @param   JHttpTransport  $transport  The HTTP transport object.
     *
     * @since   12.3
     */
    public function __construct(Registry $options = null, JHttpTransport $transport = null)
    {
        // Override the JHttp contructor to use JHttpTransportStream.
        $this->options = isset($options) ? $options : new Registry;
        $this->transport = isset($transport) ? $transport : new JHttpTransportStream($this->options);

        // Make sure the user agent string is defined.
        $this->options->def('api.useragent', 'JMediawiki/1.0');

        // Set the default timeout to 120 seconds.
        $this->options->def('api.timeout', 120);
    }

    /**
     * Method to send the GET command to the server.
     *
     * @param   string   $url      Path to the resource.
     * @param   array    $headers  An array of name-value pairs to include in the header of the request.
     * @param   integer  $timeout  Read timeout in seconds.
     *
     * @return  JHttpResponse
     *
     * @since   12.3
     */
    public function get($url, array $headers = null, $timeout = null)
    {
        // Look for headers set in the options.
        $temp = (array) $this->options->get('headers');

        foreach ($temp as $key => $val) {
            if (!isset($headers[$key])) {
                $headers[$key] = $val;
            }
        }

        // Look for timeout set in the options.
        if ($timeout === null && $this->options->exists('api.timeout')) {
            $timeout = $this->options->get('api.timeout');
        }

        return $this->transport->request('GET', new JUri($url), null, $headers, $timeout, $this->options->get('api.useragent'));
    }

    /**
     * Method to send the POST command to the server.
     *
     * @param   string   $url      Path to the resource.
     * @param   mixed    $data     Either an associative array or a string to be sent with the request.
     * @param   array    $headers  An array of name-value pairs to include in the header of the request
     * @param   integer  $timeout  Read timeout in seconds.
     *
     * @return  JHttpResponse
     *
     * @since   12.3
     */
    public function post($url, $data, array $headers = null, $timeout = null)
    {
        // Look for headers set in the options.
        $temp = (array) $this->options->get('headers');

        foreach ($temp as $key => $val) {
            if (!isset($headers[$key])) {
                $headers[$key] = $val;
            }
        }

        // Look for timeout set in the options.
        if ($timeout === null && $this->options->exists('api.timeout')) {
            $timeout = $this->options->get('api.timeout');
        }

        return $this->transport->request('POST', new JUri($url), $data, $headers, $timeout, $this->options->get('api.useragent'));
    }
}
