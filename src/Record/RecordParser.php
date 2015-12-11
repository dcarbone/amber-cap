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
    const MODE_READ_FIELD = 0;
    const MODE_READ_RECORD = 1;

    /** @var \XMLReader */
    private $_xmlReader;

    /** @var MetadataCollection */
    private $_metadataCollection;

    /** @var string */
    private $_formName;

    /** @var bool */
    private $_atFirstField = true;

    /** @var string */
    private $_currentRecordID = null;

    /** @var bool */
    private $_atEnd = false;

    /** @var int */
    private $_mode;

    /** @var bool */
    private $_begunReading = false;

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

        $this->_mode = self::MODE_READ_FIELD;
    }

    /**
     * @param int $mode
     */
    public function setMode($mode)
    {
        if ($this->_begunReading)
        {
            throw new \BadMethodCallException(sprintf(
                '%s::setMode - Cannot set mode once reading has begun.',
                get_class($this)
            ));
        }

        switch($mode)
        {
            case self::MODE_READ_FIELD:
            case self::MODE_READ_RECORD:
                $this->_mode = $mode;
                break;

            default:
                throw new \InvalidArgumentException(sprintf(
                    '%s::setMode - Specified mode does not exist.',
                    get_class($this)
                ));
        }
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->_mode;
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
     * This method returns an object based upon the specified MODE or NULL if at end of file.
     *
     * Return Classes:
     *
     * if MODE_READ_FIELD
     *  returns \DCarbone\AmberHat\Record\RecordFieldInterface
     *
     * if MODE_READ_RECORD
     *  returns \DCarbone\AmberHat\Record\Record
     *
     * @return mixed
     */
    public function read()
    {
        $this->_begunReading = true;

        if ($this->_atEnd)
            return null;

        switch($this->_mode)
        {
            case self::MODE_READ_FIELD:
                return $this->_readField();

            case self::MODE_READ_RECORD:
                return $this->_readRecord();

            default:
                throw new \DomainException(sprintf(
                    '%s::fetch - Mode value in insane state.',
                    get_class($this)
                ));
        }
    }

    /**
     * @return RecordFieldInterface|null
     */
    private function _readField()
    {
        /** @var \DCarbone\AmberHat\Record\RecordFieldInterface $recordField */

        $recordField = null;

        $elementName = null;

        $recordID = null;
        $eventName = null;
        $fieldValue = null;
        $fieldName = null;
        while($this->_xmlReader->read())
        {
            switch($this->_xmlReader->nodeType)
            {
                case \XMLReader::ELEMENT:
                    switch($this->_xmlReader->name)
                    {
                        case 'records':
                            continue 3;

                        default:
                            $elementName = $this->_xmlReader->name;
                            continue 3;
                    }

                case \XMLReader::TEXT:
                    $_value = trim($this->_xmlReader->value);

                    /**
                     * TODO: Keep an eye on this, could cause issues.
                     */
                    if ($_value === '')
                        continue 2;

                    switch($elementName)
                    {
                        case 'record':
                            $recordID = $_value;
                            if (null === $this->_currentRecordID)
                            {
                                $this->_currentRecordID = $recordID;
                                continue 3;
                            }

                            if ($recordID === $this->_currentRecordID)
                            {
                                $recordField = new RecordField(
                                    $this->_currentRecordID,
                                    $this->_formName,
                                    $fieldName,
                                    $fieldValue,
                                    $eventName,
                                    $this->_getMetadataItem($fieldName)
                                );

                                $recordField->firstFieldInRecord = $this->_atFirstField;
                                $this->_atFirstField = false;

                                return $recordField;
                            }

                            if ($recordID !== $this->_currentRecordID)
                            {
                                $recordField = new RecordField(
                                    $this->_currentRecordID,
                                    $this->_formName,
                                    $fieldName,
                                    $fieldValue,
                                    $eventName,
                                    $this->_getMetadataItem($fieldName)
                                );

                                $recordField->lastFieldInRecord = true;

                                $this->_atFirstField = true;
                                $this->_currentRecordID = $recordID;

                                return $recordField;
                            }
                            continue 3;

                        case 'redcap_event_name':
                            $eventName = $_value;
                            continue 3;

                        case 'field_name':
                            $fieldName = $_value;
                            continue 3;

                        case 'value':
                            $fieldValue = $_value;
                            continue 3;
                    }

                    continue 2;

                case \XMLReader::CDATA:
                    $fieldValue = trim($this->_xmlReader->value);
                    continue 2;

                case \XMLReader::END_ELEMENT:
                    if ('records' === $this->_xmlReader->name)
                    {
                        $this->_atEnd = true;
                        return null;
                    }
            }
        }
    }

    /**
     * @return RecordInterface|null
     */
    private function _readRecord()
    {
        /** @var \DCarbone\AmberHat\Record\RecordInterface $record */
        $record = new Record();

        while(true)
        {
            $field = $this->_readField();

            if (null === $field)
            {
                if (count($record) > 0)
                    return $record;

                return null;
            }

            $record[] = $field;

            switch(true)
            {
                case $field->firstFieldInRecord:
                    $record->recordID = $field->recordID;
                    $record->formName = $field->formName;
                    continue 2;

                case $field->lastFieldInRecord:
                    return $record;
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