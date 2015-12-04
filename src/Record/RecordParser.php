<?php namespace DCarbone\AmberHat\Record;

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

use DCarbone\AmberHat\Metadata\MetadataCollection;

/**
 * Class RecordParser
 * @package DCarbone\AmberHat\Record
 */
class RecordParser
{
    /** @var \XMLReader */
    private $_xmlReader;

    /** @var MetadataCollection */
    private $_metadataCollection;

    /** @var string */
    private $_saxResponseFieldName = null;

    /** @var bool */
    private $_atEnd = false;

    /**
     * @param \XMLReader|null $xmlReader
     * @param MetadataCollection|null $metadataCollection
     */
    protected function __construct(\XMLReader $xmlReader, MetadataCollection $metadataCollection = null)
    {
        $this->_xmlReader = $xmlReader;
        $this->_metadataCollection = $metadataCollection;
    }

    /**
     * @param string $file
     * @param MetadataCollection|null $metadataCollection
     * @return RecordParser
     */
    public static function recordParserFromXMLFile($file, MetadataCollection $metadataCollection = null)
    {
        $xmlReader = new \XMLReader();
        $xmlReader->open($file);

        return new RecordParser(
            $xmlReader,
            $metadataCollection
        );
    }

    /**
     * @return null|RecordFieldInterface
     */
    public function fetchField()
    {
        /** @var \DCarbone\AmberHat\Record\RecordFieldInterface $recordField */

        if ($this->_atEnd)
            return null;

        $recordField = null;
        $firstField = false;
        $lastField = false;
        $fieldValue = null;
        while($this->_xmlReader->read())
        {
            switch($this->_xmlReader->nodeType)
            {
                case \XMLReader::ELEMENT:
                    switch($this->_xmlReader->name)
                    {
                        case 'records':
                            continue 3;

                        case 'item':
                            $firstField = true;
                            $this->_saxResponseFieldName = null;
                            continue 3;

                        default:
                            $this->_saxResponseFieldName = $this->_xmlReader->name;
                            if ($recordField instanceof RecordFieldInterface)
                            {
                                $recordField->firstFieldInItem = $firstField;
                                $recordField->lastFieldInItem = $lastField;
                                return $recordField;
                            }
                            continue 3;
                    }

                case \XMLReader::TEXT:
                    if (null !== $this->_saxResponseFieldName)
                        $fieldValue = trim($this->_xmlReader->value);
                    continue 2;

                case \XMLReader::CDATA:
                    $fieldValue = trim($this->_xmlReader->value);
                    continue 2;

                case \XMLReader::END_ELEMENT:
                    switch($this->_xmlReader->name)
                    {
                        case 'item':
                            $recordField->lastFieldInItem = true;
                            return $recordField;

                        case 'records':
                            $this->_atEnd = true;
                            return null;

                        default:
                            $recordField = new RecordField(
                                $this->_saxResponseFieldName,
                                $fieldValue,
                                $this->_getMetadataItem($this->_saxResponseFieldName)
                            );
                    }
            }
        }
    }


    /**
     * @param string $responseFieldName
     * @return null|\DCarbone\AmberHat\Metadata\MetadataItemInterface
     */
    private function _getMetadataItem($responseFieldName)
    {
        if (!isset($this->_metadataCollection))
            return null;

        if (isset($this->_metadataCollection[$responseFieldName]))
            return $this->_metadataCollection[$responseFieldName];

        $responseFieldName = preg_replace('/(___[a-zA-Z0-9]+$)/', '', $responseFieldName);

        if (isset($this->_metadataCollection[$responseFieldName]))
            return $this->_metadataCollection[$responseFieldName];

        return null;
    }
}