<?php namespace DCarbone\AmberHat;

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

/**
 * Class AbstractItem
 * @package DCarbone\AmberHat
 */
abstract class AbstractItem implements ItemInterface
{
    /** @var array */
    protected $properties = array();

    /**
     * @param \SimpleXMLElement $sxe
     * @return ItemInterface
     */
    public static function createFromSXE(\SimpleXMLElement $sxe)
    {
        $item = new static;
        foreach($sxe->children() as $element)
        {
            /** @var \SimpleXMLElement $element */
            $item[$element->getName()] = (string)$element;
        }
        return $item;
    }

    /**
     * @param array $data
     * @return ItemInterface
     */
    public static function createFromArray(array $data)
    {
        $item = new static;
        foreach($data as $k=>$v)
        {
            $item[$k] = $v;
        }
        return $item;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->properties;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->properties);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized The string representation of the object.
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        if (is_array($data) && count($data) === count($this->properties))
        {
            if (count(array_diff(array_keys($this->properties), array_keys($data))) === 0)
            {
                $this->properties = $data;
                return;
            }
        }

        throw new \DomainException(sprintf(
            '%s::unserialize - Corrupt serialized representation seen.',
            get_class($this)
        ));
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->properties);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->properties);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->properties);
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
        return key($this->properties) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->properties);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->properties[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     * @throws \Exception
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if (isset($this->properties[$offset]) || array_key_exists($offset, $this->properties))
            return $this->properties[$offset];

        throw $this->createUnknownPropertyException($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * @return void
     * @throws \Exception
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (array_key_exists($offset, $this->properties))
            $this->properties[$offset] = $value;
        else
            throw $this->createUnknownPropertyException($offset);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset The offset to unset.
     * @return void
     * @throws \BadMethodCallException
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        throw $this->createUnsetAttemptException();
    }

    /**
     * @param mixed $property
     * @return \Exception
     */
    protected function createUnknownPropertyException($property)
    {
        return new \OutOfBoundsException(sprintf(
            '"%s" does not match known property on %s',
            $property,
            get_class($this)
        ));
    }

    /**
     * @return \BadMethodCallException
     */
    protected function createUnsetAttemptException()
    {
        return new \BadMethodCallException(sprintf(
            'Not allowed to unset %s properties',
            get_class($this)
        ));
    }
}