<?php namespace DCarbone\AmberHat\Import;

/*
    AmberHat: A REDCap Client library written in PHP
    Copyright (C) 2015  Daniel Paul Carbone (daniel.p.carbone@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

use DCarbone\AmberHat\Event\EventItemInterface;
use DCarbone\AmberHat\Import\Field\ImportFieldInterface;

/**
 * Class ImportRecord
 * @package DCarbone\AmberHat\Import
 */
class ImportRecord implements ImportRecordInterface
{
    const EAV_JSON_FORMAT = '';

    /** @var null|string */
    private $_recordID = null;

    /** @var null|EventItemInterface */
    private $_event = null;

    /** @var array */
    private $_fields = array();

    /** @var array */
    private $_instrumentFieldMap = array();

    /**
     * Constructor
     *
     * @param string $recordID
     * @param EventItemInterface|null $event
     */
    public function __construct($recordID, EventItemInterface $event = null)
    {
        $this->_recordID = $recordID;
        $this->_event = $event;
    }

    /**
     * @return string
     */
    public function getRecordID()
    {
        return $this->_recordID;
    }

    /**
     * @return EventItemInterface|null
     */
    public function getEvent()
    {
        return $this->_event;
    }

    /**
     * @return string[]
     */
    public function getInstrumentNames()
    {
        return array_keys($this->_instrumentFieldMap);
    }

    /**
     * @param string $instrumentName
     * @return string[]
     */
    public function getInstrumentFieldNames($instrumentName)
    {
        if (isset($this->_instrumentFieldMap[$instrumentName]))
            return $this->_instrumentFieldMap[$instrumentName];

        throw new \OutOfRangeException(sprintf(
            '%s::getInstrumentFieldNames - There is no instrument named "%s".',
            get_class($this),
            $instrumentName
        ));
    }

    /**
     * @param string $instrumentName
     * @return \DCarbone\AmberHat\Import\Field\ImportFieldInterface[]
     */
    public function getInstrumentFields($instrumentName)
    {
        $fieldNames = $this->getInstrumentFieldNames($instrumentName);

        $fields = array();
        foreach($fieldNames as $fieldName)
        {
            $fields[$fieldName] = $this[sprintf('%s:%s', $instrumentName, $fieldName)];
        }
        return $fields;
    }

    /**
     * @param string $instrumentName
     * @param string $fieldName
     * @return \DCarbone\AmberHat\Import\Field\ImportFieldInterface
     */
    public function getInstrumentField($instrumentName, $fieldName)
    {
        $key = sprintf('%s:%s', $instrumentName, $fieldName);
        if (isset($this[$key]))
            return $this[$key];

        if (!isset($this->_instrumentFieldMap[$instrumentName]))
        {
            throw new \OutOfRangeException(sprintf(
                '%s::getInstrumentField - There is no instrument named "%s".',
                get_class($this),
                $instrumentName
            ));
        }

        throw new \OutOfRangeException(sprintf(
            '%s::getInstrumentField - Instrument "%s" has no field named "%s".  Available fields: ["%s"].',
            get_class($this),
            $instrumentName,
            $fieldName,
            implode('", "', $this->_instrumentFieldMap[$instrumentName])
        ));
    }

    /**
     * @return string
     */
    public function createEAVJsonEntry()
    {
        $recordEntry = '[';

        /** @var ImportFieldInterface $field */
        foreach($this as $field)
        {
            $recordEntry = sprintf("%s%s,\n", $recordEntry, $field->createEAVJsonEntry($this->_recordID, $this->_event));
        }

        return sprintf('%s]', rtrim($recordEntry, "\n,"));
    }

    /**
     * @return string
     */
    public function createFlatJsonEntry()
    {
        $recordEntry = '[';

        /** @var ImportFieldInterface $field */
        foreach($this as $field)
        {
            $recordEntry = sprintf("%s%s,\n", $recordEntry, $field->createFlatJsonEntry());
        }

        return sprintf('%s]', rtrim($recordEntry, "\n,"));
    }

    /**
     * @return string
     */
    public function createEAVXMLEntry()
    {
        $recordEntry = '';

        /** @var ImportFieldInterface $field */
        foreach($this as $field)
        {
            $recordEntry = sprintf("%s%s\n", $recordEntry, $field->createEAVXMLEntry($this->_recordID, $this->_event));
        }

        return rtrim($recordEntry, "\n");
    }

    /**
     * @return string
     */
    public function createFlatXMLEntry()
    {
        $recordEntry = '<item>';

        /** @var ImportFieldInterface $field */
        foreach($this as $field)
        {
            $recordEntry = sprintf('%s%s', $recordEntry, $field->createFlatXMLEntry());
        }

        return sprintf('%s</item>', $recordEntry);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->_fields);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->_fields);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->_fields);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return key($this->_fields) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->_fields);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset  An offset to check for.
     * @return boolean true on success or false on failure.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->_fields[$offset]) || array_key_exists($offset, $this->_fields);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset  The offset to retrieve.
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if (isset($this->_fields[$offset]) || array_key_exists($offset, $this->_fields))
            return $this->_fields[$offset];

        throw new \OutOfRangeException(sprintf(
            'Offset %s does not exist on this object',
            $offset
        ));
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_string($offset) && false !== ($p = strpos($offset, ':')) && $value instanceof ImportFieldInterface)
        {
            $this->_fields[$offset] = $value;

            $instrument = substr($offset, 0, $p);
            $field = substr($offset, $p);

            if (!isset($this->_instrumentFieldMap[$instrument]))
                $this->_instrumentFieldMap[$instrument] = array();

            $this->_instrumentFieldMap[$instrument][] = $field;
        }
        else
        {
            throw new \OutOfBoundsException('Child values must be of type ImportFieldInterface with "instrument:field_name" keys.');
        }
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset The offset to unset.
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Not allowed to unset objects on ImportRecord objects.');
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->_fields);
    }
}