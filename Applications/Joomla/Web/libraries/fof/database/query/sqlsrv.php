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
class FOFDatabaseQuerySqlsrv extends FOFDatabaseQuery implements FOFDatabaseQueryLimitable
{
    /**
     * The character(s) used to quote SQL statement names such as table names or field names,
     * etc.  The child classes should define this as necessary.  If a single character string the
     * same character is used for both sides of the quoted name, else the first character will be
     * used for the opening quote and the second for the closing quote.
     *
     * @var    string
     * @since  11.1
     */
    protected $name_quotes = '`';

    /**
     * The null or zero representation of a timestamp for the database driver.  This should be
     * defined in child classes to hold the appropriate value for the engine.
     *
     * @var    string
     * @since  11.1
     */
    protected $null_date = '1900-01-01 00:00:00';

    /**
     * @var    integer  The affected row limit for the current SQL statement.
     * @since  3.2
     */
    protected $limit = 0;

    /**
     * @var    integer  The affected row offset to apply for the current SQL statement.
     * @since  3.2
     */
    protected $offset = 0;

    /**
     * Magic function to convert the query to a string.
     *
     * @return  string	The completed query.
     *
     * @since   11.1
     */
    public function __toString()
    {
        $query = '';

        switch ($this->type) {
            case 'select':
                $query .= (string) $this->select;
                $query .= (string) $this->from;

                if ($this->join) {
                    // Special case for joins
                    foreach ($this->join as $join) {
                        $query .= (string) $join;
                    }
                }

                if ($this->where) {
                    $query .= (string) $this->where;
                }

                if ($this->group) {
                    $query .= (string) $this->group;
                }

                if ($this->order) {
                    $query .= (string) $this->order;
                }

                if ($this->having) {
                    $query .= (string) $this->having;
                }

                if ($this instanceof FOFDatabaseQueryLimitable && ($this->limit > 0 || $this->offset > 0)) {
                    $query = $this->processLimit($query, $this->limit, $this->offset);
                }

                break;

            case 'insert':
                $query .= (string) $this->insert;

                // Set method
                if ($this->set) {
                    $query .= (string) $this->set;
                }
                // Columns-Values method
                elseif ($this->values) {
                    if ($this->columns) {
                        $query .= (string) $this->columns;
                    }

                    $elements = $this->insert->getElements();
                    $tableName = array_shift($elements);

                    $query .= 'VALUES ';
                    $query .= (string) $this->values;

                    if ($this->autoIncrementField) {
                        $query = 'SET IDENTITY_INSERT ' . $tableName . ' ON;' . $query . 'SET IDENTITY_INSERT ' . $tableName . ' OFF;';
                    }

                    if ($this->where) {
                        $query .= (string) $this->where;
                    }
                }

                break;

            case 'delete':
                $query .= (string) $this->delete;
                $query .= (string) $this->from;

                if ($this->join) {
                    // Special case for joins
                    foreach ($this->join as $join) {
                        $query .= (string) $join;
                    }
                }

                if ($this->where) {
                    $query .= (string) $this->where;
                }

                if ($this->order) {
                    $query .= (string) $this->order;
                }

                break;

            case 'update':
                $query .= (string) $this->update;

                if ($this->join) {
                    // Special case for joins
                    foreach ($this->join as $join) {
                        $query .= (string) $join;
                    }
                }

                $query .= (string) $this->set;

                if ($this->where) {
                    $query .= (string) $this->where;
                }

                if ($this->order) {
                    $query .= (string) $this->order;
                }

                break;

            default:
                $query = parent::__toString();
                break;
        }

        return $query;
    }

    /**
     * Casts a value to a char.
     *
     * Ensure that the value is properly quoted before passing to the method.
     *
     * @param   string  $value  The value to cast as a char.
     *
     * @return  string  Returns the cast value.
     *
     * @since   11.1
     */
    public function castAsChar($value)
    {
        return 'CAST(' . $value . ' as NVARCHAR(10))';
    }

    /**
     * Gets the function to determine the length of a character string.
     *
     * @param   string  $field      A value.
     * @param   string  $operator   Comparison operator between charLength integer value and $condition
     * @param   string  $condition  Integer value to compare charLength with.
     *
     * @return  string  The required char length call.
     *
     * @since   11.1
     */
    public function charLength($field, $operator = null, $condition = null)
    {
        return 'DATALENGTH(' . $field . ')' . (isset($operator) && isset($condition) ? ' ' . $operator . ' ' . $condition : '');
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
            return '(' . implode('+' . $this->quote($separator) . '+', $values) . ')';
        } else {
            return '(' . implode('+', $values) . ')';
        }
    }

    /**
     * Gets the current date and time.
     *
     * @return  string
     *
     * @since   11.1
     */
    public function currentTimestamp()
    {
        return 'GETDATE()';
    }

    /**
     * Get the length of a string in bytes.
     *
     * @param   string  $value  The string to measure.
     *
     * @return  integer
     *
     * @since   11.1
     */
    public function length($value)
    {
        return 'LEN(' . $value . ')';
    }

    /**
     * Add to the current date and time.
     * Usage:
     * $query->select($query->dateAdd());
     * Prefixing the interval with a - (negative sign) will cause subtraction to be used.
     *
     * @param   datetime  $date      The date to add to; type may be time or datetime.
     * @param   string    $interval  The string representation of the appropriate number of units
     * @param   string    $datePart  The part of the date to perform the addition on
     *
     * @return  string  The string with the appropriate sql for addition of dates
     *
     * @since   13.1
     * @note    Not all drivers support all units.
     * @link    http://msdn.microsoft.com/en-us/library/ms186819.aspx for more information
     */
    public function dateAdd($date, $interval, $datePart)
    {
        return "DATEADD('" . $datePart . "', '" . $interval . "', '" . $date . "'" . ')';
    }

    /**
     * Method to modify a query already in string format with the needed
     * additions to make the query limited to a particular number of
     * results, or start at a particular offset.
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
        if ($limit == 0 && $offset == 0) {
            return $query;
        }

        $start = $offset + 1;
        $end   = $offset + $limit;

        $orderBy = stristr($query, 'ORDER BY');

        if (is_null($orderBy) || empty($orderBy)) {
            $orderBy = 'ORDER BY (select 0)';
        }

        $query = str_ireplace($orderBy, '', $query);

        $rowNumberText = ', ROW_NUMBER() OVER (' . $orderBy . ') AS RowNumber FROM ';

        $query = preg_replace('/\sFROM\s/i', $rowNumberText, $query, 1);
        $query = 'SELECT * FROM (' . $query . ') A WHERE A.RowNumber BETWEEN ' . $start . ' AND ' . $end;

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
     * Return correct rand() function for MSSQL.
     *
     * Ensure that the rand() function is MSSQL compatible.
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
        return ' NEWID() ';
    }
}
