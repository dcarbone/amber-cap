<?php namespace DCarbone\AmberHat\ExportFields;

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

use DCarbone\AmberHat\AbstractAmberHatCollection;
use DCarbone\AmberHat\AmberHatItemInterface;
use DCarbone\AmberHat\Metadata\MetadataItemInterface;

/**
 * Class ExportFieldsCollection
 * @package DCarbone\AmberHat\ExportFields
 */
class ExportFieldsCollection extends AbstractAmberHatCollection
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
            '\\DCarbone\\AmberHat\\ExportFields\\ExportFieldItem',
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
            '\\DCarbone\\AmberHat\\ExportFields\\ExportFieldItem',
            'export_field_name');
    }

    /**
     * @param AbstractAmberHatCollection $collection
     * @param AmberHatItemInterface $item
     * @param string $keyProperty
     */
    protected static function addItemToCollection(
        AbstractAmberHatCollection $collection,
        AmberHatItemInterface $item,
        $keyProperty)
    {
        if ($collection instanceof self)
        {
            $collection[$item['export_field_name']] = $item;

            if (!isset($collection->_originalToExportNameMap[$item['original_field_name']]))
                $collection->_originalToExportNameMap[$item['original_field_name']] = array();

            $collection->_originalToExportNameMap[$item['original_field_name']][] = array(
                'choice_value' => $item['choice_value'],
                'export_field_name' => $item['export_field_name']
            );
        }
        else
        {
            throw new \BadMethodCallException(
                'Cannot utilize overloaded static method "addItemToCollection" on on class "ExportFieldsCollection" with different collection class.'
            );
        }
    }
}