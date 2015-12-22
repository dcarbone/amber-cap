<?php namespace DCarbone\AmberHat\ExportFieldName;

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
use DCarbone\AmberHat\Metadata\MetadataItemInterface;

/**
 * Class ExportFieldNamesCollection
 * @package DCarbone\AmberHat\ExportFieldName
 */
class ExportFieldNamesCollection extends AbstractItemCollection
{
    /** @var array */
    private $_originalToExportNameMap = array();

    /**
     * @param string $originalFieldName
     * @return string[]|null
     */
    public function getExportFieldNamesForField($originalFieldName)
    {
        if (isset($this->_originalToExportNameMap[$originalFieldName]))
            return $this->_originalToExportNameMap[$originalFieldName];

        return null;
    }

    /**
     * @param MetadataItemInterface $metadataItem
     * @return ExportFieldNameItemInterface[]|null
     */
    public function getExportFieldsForMetadataItem(MetadataItemInterface $metadataItem)
    {
        $names = $this->getExportFieldNamesForField($metadataItem['field_name']);

        if ($names)
        {
            $exportFields = array();
            if (is_array($names))
            {
                foreach($names as $exportFieldName)
                {
                    $exportFields[$exportFieldName] = $this[$exportFieldName];
                }
            }
            else
            {
                $exportFields[$names] = $this[$names];
            }
            return $exportFields;
        }

        return null;
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
        if (count($data) === 2 && is_array($data[0]) && is_array($data[1]))
        {
            $this->items = $data[0];
            $this->_originalToExportNameMap = $data[1];
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
     * @param array $itemData
     */
    public function buildAndAppendItem(array $itemData)
    {
        $item = ExportFieldNameItem::createFromArray($itemData);

        if (null === $item['choice_value'] || '' === $item['choice_value'])
        {
            $this->_originalToExportNameMap[$item['original_field_name']] = $item['export_field_name'];
        }
        else
        {
            if (!isset($this->_originalToExportNameMap[$item['original_field_name']]))
                $this->_originalToExportNameMap[$item['original_field_name']] = array();

            $this->_originalToExportNameMap[$item['original_field_name']][$item['choice_value']] = $item['export_field_name'];
        }

        $this[$item['export_field_name']] = $item;
    }
}