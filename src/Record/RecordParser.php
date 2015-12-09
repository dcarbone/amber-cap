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
    private $_formName;

    /** @var string */
    private $_saxExportFieldName = null;

    /** @var bool */
    private $_atEnd = false;

    /**
     * @param \XMLReader|null $xmlReader
     * @param string $formName
     * @param MetadataCollection|null $metadataCollection
     */
    protected function __construct(\XMLReader $xmlReader, $formName, MetadataCollection $metadataCollection = null)
    {
        $this->_xmlReader = $xmlReader;
        $this->_formName = $formName;
        $this->_metadataCollection = $metadataCollection;
    }

    /**
     * @param string $file
     * @param string $formName
     * @param MetadataCollection|null $metadataCollection
     * @return RecordParser
     */
    public static function createWithXMLFile($file, $formName, MetadataCollection $metadataCollection = null)
    {
        $xmlReader = new \XMLReader();
        $xmlReader->open($file);

        return new RecordParser(
            $xmlReader,
            $formName,
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
                            $this->_saxExportFieldName = null;
                            continue 3;

                        default:
                            $this->_saxExportFieldName = $this->_xmlReader->name;
                            if ($recordField instanceof RecordFieldInterface)
                            {
                                $recordField->firstFieldInItem = $firstField;
                                $recordField->lastFieldInItem = $lastField;
                                return $recordField;
                            }
                            continue 3;
                    }

                case \XMLReader::TEXT:
                    if (null !== $this->_saxExportFieldName)
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
                                $this->_saxExportFieldName,
                                $fieldValue,
                                $this->_getMetadataItem($this->_saxExportFieldName)
                            );
                    }
            }
        }
    }


    /**
     * @param string $exportFieldName
     * @return null|\DCarbone\AmberHat\Metadata\MetadataItemInterface
     */
    private function _getMetadataItem($exportFieldName)
    {
        if (!isset($this->_metadataCollection))
            return null;

        $key = sprintf('%s:%s', $this->_formName, $exportFieldName);

        if (isset($this->_metadataCollection[$key]))
            return $this->_metadataCollection[$key];

        $key = sprintf('%s:%s', $this->_formName, preg_replace('/(___[a-zA-Z0-9]+$)/', '', $exportFieldName));

        if (isset($this->_metadataCollection[$key]))
            return $this->_metadataCollection[$key];

        return null;
    }
}