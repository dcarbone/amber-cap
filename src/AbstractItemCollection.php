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
 * Class AbstractItemCollection
 * @package DCarbone\AmberHat
 */
abstract class AbstractItemCollection implements \ArrayAccess, \Countable, \Iterator, \Serializable
{
    /** @var string */
    protected static $rootNodeName = 'records';
    /** @var string */
    protected static $itemNodeName = 'item';

    /** @var array */
    protected $items = array();

    /**
     * @param string $xml
     */
    public static function createFromXMLString($xml)
    {
        throw new \BadMethodCallException(sprintf(
            '%s::createFromXMLString - Class %s must override base definition of this method.',
            __CLASS__,
            get_called_class()
        ));
    }

    /**
     * @param string $file
     */
    public static function createFromXMLFile($file)
    {
        throw new \BadMethodCallException(sprintf(
            '%s::createFromXMLFile - Class %s must override base definition of this method.',
            __CLASS__,
            get_called_class()
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
        return current($this->items);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->items);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return key($this->items) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->items);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     *
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if (isset($this->items[$offset]))
            return $this->items[$offset];

        throw new \OutOfRangeException(sprintf(
            '%s::offsetGet - Offset "%s" does not exist.',
            get_class($this),
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
        if (is_object($value) && $value instanceof ItemInterface)
        {
            if (null === $offset)
                $this->items[] = $value;
            else
                $this->items[$offset] = $value;
        }
        else
        {
            throw new \DomainException(sprintf(
                '%s::offsetSet - Item Collection children must implement ItemInterface',
                get_class($this)
            ));
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
        throw new \BadMethodCallException(sprintf(
            'Not allowed to remove Items from "%s".',
            get_class($this)));
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(array(
            static::$rootNodeName,
            static::$itemNodeName,
            $this->items
        ));
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
        if (count($data) === 3 && is_string($data[0]) && is_string($data[1]) && is_array($data[2]))
        {
            static::$rootNodeName = $data[0];
            static::$itemNodeName = $data[1];
            $this->items = $data[2];
        }
        else
        {
            throw new \DomainException(sprintf(
                '%s::unserialize - Corrupt serialized representation seen.',
                get_class($this)
            ));
        }
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @param string $xml
     * @param string $itemClass
     * @param string $keyProperty
     * @return AbstractItemCollection
     * @internal
     */
    protected static function processXMLString($xml, $itemClass, $keyProperty = null)
    {
        /** @var \DCarbone\AmberHat\AbstractItem $itemClass */

        $sxe = new \SimpleXMLElement($xml, LIBXML_COMPACT | LIBXML_NOBLANKS);
        if ($sxe instanceof \SimpleXMLElement)
        {
            $collection = new static();
            foreach($sxe->xpath(static::$itemNodeName) as $itemElement)
            {
                $collection->addItem($itemClass::createFromSXE($itemElement), $keyProperty);
            }
            return $collection;
        }

        throw new \InvalidArgumentException('Unable to parse provided XML string.');
    }

    /**
     * @param string $file
     * @param string $itemClass
     * @param string $keyProperty
     * @return AbstractItemCollection
     * @internal
     */
    protected static function processXMLFile($file, $itemClass, $keyProperty = null)
    {
        $xmlReader = new \XMLReader();
        $xmlReader->open($file);

        $collection = new static();
        $fieldName = null;
        $item = null;
        while ($xmlReader->read())
        {
            switch($xmlReader->nodeType)
            {
                case \XMLReader::ELEMENT:
                    switch($xmlReader->name)
                    {
                        case static::$itemNodeName:
                            $item = new $itemClass();
                            continue 3;

                        case static::$rootNodeName:
                            continue 3;

                        default:
                            $fieldName = $xmlReader->name;
                            continue 3;
                    }

                // If the provided XML has been modified from the default
                // structure of having values contained within CDATA blocks.
                case \XMLReader::TEXT:
                    if (null !== $fieldName && '' !== ($value = trim($xmlReader->value)))
                        $item[$fieldName] = $value;
                    continue 2;

                case \XMLReader::CDATA:
                    $item[$fieldName] = trim($xmlReader->value);
                    $fieldName = null;
                    continue 2;

                case \XMLReader::END_ELEMENT:
                    switch($xmlReader->name)
                    {
                        case static::$itemNodeName:
                            $collection->addItem($item, $keyProperty);
                            $fieldName = null;
                            continue 3;
                    }
            }
        }

        return $collection;
    }

    /**
     * @param ItemInterface $item
     * @param string $keyProperty
     * @internal
     */
    protected function addItem(ItemInterface $item, $keyProperty)
    {
        if (null === $keyProperty)
            $this[] = $item;
        else
            $this[$item[$keyProperty]] = $item;
    }
}