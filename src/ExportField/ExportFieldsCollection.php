<?php namespace DCarbone\AmberHat\ExportField;

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

use DCarbone\AmberHat\AbstractItemCollection;
use DCarbone\AmberHat\ItemInterface;
use DCarbone\AmberHat\Metadata\MetadataItemInterface;

/**
 * Class ExportFieldsCollection
 * @package DCarbone\AmberHat\ExportFields
 */
class ExportFieldsCollection extends AbstractItemCollection
{
    /** @var string */
    protected static $rootNodeName = 'fields';
    /** @var string */
    protected static $itemNodeName = 'field';

    /** @var array */
    private $_originalToExportNameMap = array();

    /**
     * @param string $originalFieldName
     * @return array|null
     */
    public function getExportFieldNamesForField($originalFieldName)
    {
        if (isset($this->_originalToExportNameMap[$originalFieldName]))
            return $this->_originalToExportNameMap[$originalFieldName];

        return null;
    }

    /**
     * @param MetadataItemInterface $metadataItem
     * @return array|null
     */
    public function getExportFieldsForMetadataItem(MetadataItemInterface $metadataItem)
    {
        return $this->getExportFieldNamesForField($metadataItem->getFieldName());
    }

    /**
     * @param string $xml
     * @return ExportFieldsCollection
     */
    public static function createFromXMLString($xml)
    {
        return self::processXMLString(
            $xml,
            '\\DCarbone\\AmberHat\\ExportField\\ExportFieldItem',
            'export_field_name');
    }

    /**
     * @param string $file
     * @return ExportFieldsCollection
     */
    public static function createFromXMLFile($file)
    {
        return self::processXMLFile(
            $file,
            '\\DCarbone\\AmberHat\\ExportField\\ExportFieldItem',
            'export_field_name');
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
            $this->items,
            $this->_originalToExportNameMap
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
        if (count($data) === 4 && is_string($data[0]) && is_string($data[1]) && is_array($data[2]) && is_array($data[3]))
        {
            static::$rootNodeName = $data[0];
            static::$itemNodeName = $data[1];
            $this->items = $data[2];
            $this->_originalToExportNameMap = $data[3];
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
     * @param AbstractItemCollection $collection
     * @param ItemInterface $item
     * @param string $keyProperty
     */
    protected static function addItemToCollection(
        AbstractItemCollection $collection,
        ItemInterface $item,
        $keyProperty)
    {
        if ($collection instanceof self)
        {
            $collection[$item['export_field_name']] = $item;

            if ($item['choice_value'] === '')
            {
                $collection->_originalToExportNameMap[$item['original_field_name']] = $item['export_field_name'];
            }
            else
            {
                if (!isset($collection->_originalToExportNameMap[$item['original_field_name']]))
                    $collection->_originalToExportNameMap[$item['original_field_name']] = array();

                $collection->_originalToExportNameMap[$item['original_field_name']][$item['choice_value']] = $item['export_field_name'];
            }
        }
        else
        {
            throw new \BadMethodCallException(
                'Cannot utilize overloaded static method "addItemToCollection" on on class "ExportFieldsCollection" with different collection class.'
            );
        }
    }
}