<?php
/**
 * @package     FrameworkOnFramework
 * @subpackage  database
 * @copyright   Copyright (C) 2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This file is adapted from the Joomla! Platform. It is used to iterate a database cursor returning FOFTable objects
 * instead of plain stdClass objects
 */

// Protect from unauthorized access
defined('FOF_INCLUDED') or die;

/**
 * Query Building Class.
 *
 * @since  11.1
 */
class FOFDatabaseQueryMysqli extends FOFDatabaseQuery implements FOFDatabaseQueryLimitable
{
    /**
     * @var    integer  The offset for the result set.
     * @since  12.1
     */
    protected $offset;

    /**
     * @var    integer  The limit for the result set.
     * @since  12.1
     */
    protected $limit;

    /**
     * Method to modify a query already in string format with the needed
     * additions to make the query limited to a particular number of
     * results, or start at a particular offset.
     *
     * @param   string   $query   The query in string format
     * @param   integer  $limit   The limit for the result set
     * @param   integer  $offset  The offset for the result set
     *
     * @return string
     *
     * @since 12.1
     */
    public function processLimit($query, $limit, $offset = 0)
    {
        if ($limit > 0 || $offset > 0) {
            $query .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        return $query;
    }

    /**
     * Concatenates an array of column names or values.
     *
     * @param   array   $values     An array of values to concatenate.
     * @param   string  $separator  As separator to place between each value.
     *
     * @return  string  The concatenated values.
     *
     * @since   11.1
     */
    public function concatenate($values, $separator = null)
    {
        if ($separator) {
            $concat_string = 'CONCAT_WS(' . $this->quote($separator);

            foreach ($values as $value) {
                $concat_string .= ', ' . $value;
            }

            return $concat_string . ')';
        } else {
            return 'CONCAT(' . implode(',', $values) . ')';
        }
    }

    /**
     * Sets the offset and limit for the result set, if the database driver supports it.
     *
     * Usage:
     * $query->setLimit(100, 0); (retrieve 100 rows, starting at first record)
     * $query->setLimit(50, 50); (retrieve 50 rows, starting at 50th record)
     *
     * @param   integer  $limit   The limit for the result set
     * @param   integer  $offset  The offset for the result set
     *
     * @return  FOFDatabaseQuery  Returns this object to allow chaining.
     *
     * @since   12.1
     */
    public function setLimit($limit = 0, $offset = 0)
    {
        $this->limit  = (int) $limit;
        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * Return correct regexp operator for mysqli.
     *
     * Ensure that the regexp operator is mysqli compatible.
     *
     * Usage:
     * $query->where('field ' . $query->regexp($search));
     *
     * @param   string  $value  The regex pattern.
     *
     * @return  string  Returns the regex operator.
     *
     * @since   11.3
     */
    public function regexp($value)
    {
        return ' REGEXP ' . $value;
    }

    /**
     * Return correct rand() function for Mysql.
     *
     * Ensure that the rand() function is Mysql compatible.
     *
     * Usage:
     * $query->Rand();
     *
     * @return  string  The correct rand function.
     *
     * @since   3.5
     */
    public function Rand()
    {
        return ' RAND() ';
    }
}
