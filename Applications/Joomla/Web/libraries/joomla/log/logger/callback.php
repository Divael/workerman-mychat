<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla! Callback Log class
 *
 * This class allows logging to be handled by a callback function.
 * This allows unprecedented flexibility in the way logging can be handled.
 *
 * @since  12.2
 */
class JLogLoggerCallback extends JLogLogger
{
    /**
     * The function to call when an entry is added
     *
     * @var    callable
     * @since  12.2
     */
    protected $callback;

    /**
     * Constructor.
     *
     * @param   array  &$options  Log object options.
     *
     * @since   12.2
     * @throws  RuntimeException
     */
    public function __construct(array &$options)
    {
        // Call the parent constructor.
        parent::__construct($options);

        // Throw an exception if there is not a valid callback
        if (!isset($this->options['callback']) || !is_callable($this->options['callback'])) {
            throw new RuntimeException('JLogLoggerCallback created without valid callback function.');
        }

        $this->callback = $this->options['callback'];
    }

    /**
     * Method to add an entry to the log.
     *
     * @param   JLogEntry  $entry  The log entry object to add to the log.
     *
     * @return  void
     *
     * @since   12.2
     * @throws  RuntimeException
     */
    public function addEntry(JLogEntry $entry)
    {
        // Pass the log entry to the callback function
        call_user_func($this->callback, $entry);
    }
}
