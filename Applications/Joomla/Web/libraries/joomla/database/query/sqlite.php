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
 * SQLite Query Building Class.
 *
 * @since  12.1
 */
class JDatabaseQuerySqlite extends JDatabaseQueryPdo implements JDatabaseQueryPreparable, JDatabaseQueryLimitable
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
     * @var    array  Bounded object array
     * @since  12.1
     */
    protected $bounded = array();

    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query execution. Also
     * removes a variable that has been bounded from the internal bounded array when the passed in value is null.
     *
     * @param   string|integer  $key            The key that will be used in your SQL query to reference the value. Usually of
     *                                          the form ':key', but can also be an integer.
     * @param   mixed           &$value         The value that will be bound. The value is passed by reference to support output
     *                                          parameters such as those possible with stored procedures.
     * @param   integer         $dataType       Constant corresponding to a SQL datatype.
     * @param   integer         $length         The length of the variable. Usually required for OUTPUT parameters.
     * @param   array           $driverOptions  Optional driver options to be used.
     *
     * @return  JDatabaseQuerySqlite
     *
     * @since   12.1
     */
    public function bind($key = null, &$value = null, $dataType = PDO::PARAM_STR, $length = 0, $driverOptions = array())
    {
        // Case 1: Empty Key (reset $bounded array)
        if (empty($key)) {
            $this->bounded = array();

            return $this;
        }

        // Case 2: Key Provided, null value (unset key from $bounded array)
        if (is_null($value)) {
            if (isset($this->bounded[$key])) {
                unset($this->bounded[$key]);
            }

            return $this;
        }

        $obj = new stdClass;

        $obj->value = &$value;
        $obj->dataType = $dataType;
        $obj->length = $length;
        $obj->driverOptions = $driverOptions;

        // Case 3: Simply add the Key/Value into the bounded array
        $this->bounded[$key] = $obj;

        return $this;
    }

    /**
     * Retrieves the bound parameters array when key is null and returns it by reference. If a key is provided then that item is
     * returned.
     *
     * @param   mixed  $key  The bounded variable key to retrieve.
     *
     * @return  mixed
     *
     * @since   12.1
     */
    public function &getBounded($key = null)
    {
        if (empty($key)) {
            return $this->bounded;
        } else {
            if (isset($this->bounded[$key])) {
                return $this->bounded[$key];
            }
        }
    }

    /**
     * Gets the number of characters in a string.
     *
     * Note, use 'length' to find the number of bytes in a string.
     *
     * Usage:
     * $query->select($query->charLength('a'));
     *
     * @param   string  $field      A value.
     * @param   string  $operator   Comparison operator between charLength integer value and $condition
     * @param   string  $condition  Integer value to compare charLength with.
     *
     * @return  string  The required char length call.
     *
     * @since   13.1
     */
    public function charLength($field, $operator = null, $condition = null)
    {
        return 'length(' . $field . ')' . (isset($operator) && isset($condition) ? ' ' . $operator . ' ' . $condition : '');
    }

    /**
     * Clear data from the query or a specific clause of the query.
     *
     * @param   string  $clause  Optionally, the name of the clause to clear, or nothing to clear the whole query.
     *
     * @return  JDatabaseQuerySqlite  Returns this object to allow chaining.
     *
     * @since   12.1
     */
    public function clear($clause = null)
    {
        switch ($clause) {
            case null:
                $this->bounded = array();
                break;
        }

        parent::clear($clause);

        return $this;
    }

    /**
     * Concatenates an array of column names or values.
     *
     * Usage:
     * $query->select($query->concatenate(array('a', 'b')));
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
            return implode(' || ' . $this->quote($separator) . ' || ', $values);
        } else {
            return implode(' || ', $values);
        }
    }

    /**
     * Method to modify a query already in string format with the needed
     * additions to make the query limited to a particular number of
     * results, or start at a particular offset. This method is used
     * automatically by the __toString() method if it detects that the
     * query implements the JDatabaseQueryLimitable interface.
     *
     * @param   string   $query   The query in string format
     * @param   integer  $limit   The limit for the result set
     * @param   integer  $offset  The offset for the result set
     *
     * @return  string
     *
     * @since   12.1
     */
    public function processLimit($query, $limit, $offset = 0)
    {
        if ($limit > 0 || $offset > 0) {
            $query .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        return $query;
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
     * @return  JDatabaseQuerySqlite  Returns this object to allow chaining.
     *
     * @since   12.1
     */
    public function setLimit($limit = 0, $offset = 0)
    {
        $this->limit = (int) $limit;
        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * Add to the current date and time.
     * Usage:
     * $query->select($query->dateAdd());
     * Prefixing the interval with a - (negative sign) will cause subtraction to be used.
     *
     * @param   datetime  $date      The date or datetime to add to
     * @param   string    $interval  The string representation of the appropriate number of units
     * @param   string    $datePart  The part of the date to perform the addition on
     *
     * @return  string  The string with the appropriate sql for addition of dates
     *
     * @since   13.1
     * @link    http://www.sqlite.org/lang_datefunc.html
     */
    public function dateAdd($date, $interval, $datePart)
    {
        // SQLite does not support microseconds as a separate unit. Convert the interval to seconds
        if (strcasecmp($datePart, 'microseconds') == 0) {
            $interval = .001 * $interval;
            $datePart = 'seconds';
        }

        if (substr($interval, 0, 1) != '-') {
            return "datetime('" . $date . "', '+" . $interval . " " . $datePart . "')";
        } else {
            return "datetime('" . $date . "', '" . $interval . " " . $datePart . "')";
        }
    }

    /**
     * Gets the current date and time.
     *
     * Usage:
     * $query->where('published_up < '.$query->currentTimestamp());
     *
     * @return  string
     *
     * @since   3.4
     */
    public function currentTimestamp()
    {
        return 'CURRENT_TIMESTAMP';
    }

    /**
     * Magic function to convert the query to a string.
     *
     * @return  string  The completed query.
     *
     * @since   11.1
     */
    public function __toString()
    {
        switch ($this->type) {
            case 'select':
                if ($this->selectRowNumber) {
                    $orderBy          = $this->selectRowNumber['orderBy'];
                    $orderColumnAlias = $this->selectRowNumber['orderColumnAlias'];

                    $column = "ROW_NUMBER() AS $orderColumnAlias";

                    if ($this->select === null) {
                        $query = PHP_EOL . "SELECT 1"
                            . (string) $this->from
                            . (string) $this->where;
                    } else {
                        $tmpOffset    = $this->offset;
                        $tmpLimit     = $this->limit;
                        $this->offset = 0;
                        $this->limit  = 0;
                        $tmpOrder    = $this->order;
                        $this->order = null;
                        $query       = parent::__toString();
                        $column      = "w.*, $column";
                        $this->order = $tmpOrder;
                        $this->offset = $tmpOffset;
                        $this->limit  = $tmpLimit;
                    }

                    // Special sqlite query to count ROW_NUMBER
                    $query = PHP_EOL . "SELECT $column"
                        . PHP_EOL . "FROM ($query" . PHP_EOL . "ORDER BY $orderBy"
                        . PHP_EOL . ") AS w,(SELECT ROW_NUMBER(0)) AS r"
                        // Forbid to flatten subqueries.
                        . ((string) $this->order ?: PHP_EOL . 'ORDER BY NULL');

                    return $this->processLimit($query, $this->limit, $this->offset);
                }

                break;

            case 'update':
                if ($this->join) {
                    $table = $this->update->getElements();
                    $table = $table[0];

                    $tableName = explode(' ', $table);
                    $tableName = $tableName[0];

                    if ($this->columns === null) {
                        $fields = $this->db->getTableColumns($tableName);

                        foreach ($fields as $key => $value) {
                            $fields[$key] = $key;
                        }

                        $this->columns = new JDatabaseQueryElement('()', $fields);
                    }

                    $fields   = $this->columns->getElements();
                    $elements = $this->set->getElements();

                    foreach ($elements as $nameValue) {
                        $setArray = explode(' = ', $nameValue, 2);

                        if ($setArray[0][0] === '`') {
                            // Unquote column name
                            $setArray[0] = substr($setArray[0], 1, -1);
                        }

                        $fields[$setArray[0]] = $setArray[1];
                    }

                    $select = new JDatabaseQuerySqlite($this->db);
                    $select->select(array_values($fields))
                        ->from($table);

                    $select->join  = $this->join;
                    $select->where = $this->where;

                    return 'INSERT OR REPLACE INTO ' . $tableName
                        . ' (' . implode(',', array_keys($fields)) . ')'
                        . (string) $select;
                }
        }

        return parent::__toString();
    }

    /**
     * Return the number of the current row.
     *
     * @param   string  $orderBy           An expression of ordering for window function.
     * @param   string  $orderColumnAlias  An alias for new ordering column.
     *
     * @return  JDatabaseQuery  Returns this object to allow chaining.
     *
     * @since   3.7.0
     * @throws  RuntimeException
     */
    public function selectRowNumber($orderBy, $orderColumnAlias)
    {
        $this->validateRowNumber($orderBy, $orderColumnAlias);

        return $this;
    }
}
