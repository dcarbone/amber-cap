<?php namespace DCarbone\AmberHat\Metadata;

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

/**
 * Class MetadataCollection
 * @package PHPRedcap\MetadataCollection
 */
class MetadataCollection extends AbstractItemCollection
{
    /**
     * @param string $xml
     * @return MetadataCollection
     */
    public static function createFromXMLString($xml)
    {
        return self::processXMLString($xml, '\\DCarbone\\AmberHat\\Metadata\\MetadataItem');
    }

    /**
     * @param string $file
     * @return MetadataCollection
     */
    public static function createFromXMLFile($file)
    {
        return self::processXMLFile($file, '\\DCarbone\\AmberHat\\Metadata\\MetadataItem');
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
            $key = sprintf('%s:%s', $item['form_name'], $item['field_name']);
            $collection[$key] = $item;
        }
        else
        {
            throw new \BadMethodCallException(
                'Cannot utilize overloaded static method "addItemToCollection" on on class "MetadataCollection" with different collection class.'
            );
        }
    }
}