<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * PostgreSQL database iterator.
 *
 * @since  13.1
 */
class JDatabaseIteratorPostgresql extends JDatabaseIterator
{
    /**
     * Get the number of rows in the result set for the executed SQL given by the cursor.
     *
     * @return  integer  The number of rows in the result set.
     *
     * @since   13.1
     * @see     Countable::count()
     */
    public function count()
    {
        return pg_num_rows($this->cursor);
    }

    /**
     * Method to fetch a row from the result set cursor as an object.
     *
     * @return  mixed   Either the next row from the result set or false if there are no more rows.
     *
     * @since   13.1
     */
    protected function fetchObject()
    {
        return pg_fetch_object($this->cursor, null, $this->class);
    }

    /**
     * Method to free up the memory used for the result set.
     *
     * @return  void
     *
     * @since   13.1
     */
    protected function freeResult()
    {
        pg_free_result($this->cursor);
    }
}
