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
    const MODE_READ_FIELD  = 0;
    const MODE_READ_RECORD = 1;

    const POS_START_OF_DOC   = 10;
    const POS_START_OF_FIELD = 20;
    const POS_IN_FIELD       = 30;
    const POS_END_OF_FIELD   = 40;
    const POS_END_OF_DOC     = 50;

    /**
     * Thank you to http://stackoverflow.com/a/1401716/1141357 for the below regex
     *
     * @var string
     */
    private static $_invalidByteRemovalRegex = '/((?:[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})|./Sx';

    /** @var resource */
    private $_fh;

    /** @var resource */
    private $_parser;

    /** @var MetadataCollection */
    private $_metadataCollection;

    /** @var string */
    private $_formName;

    /** @var int */
    private $_mode;

    /** @var string */
    private $_currentElementName = null;

    /** @var null|RecordField */
    private $_previousField = null;

    /** @var int */
    private $_position;

    /** @var string */
    private $_recordID = null;
    /** @var string */
    private $_redcapEventName = null;
    /** @var string */
    private $_fieldName = null;
    /** @var string */
    private $_fieldValue = null;

    /**
     * Constructor
     *
     * @param string $formName
     * @param MetadataCollection|null $metadataCollection
     */
    protected function __construct($formName, MetadataCollection $metadataCollection = null)
    {
        $this->_formName = $formName;
        $this->_metadataCollection = $metadataCollection;

        $this->_mode = self::MODE_READ_FIELD;
        $this->_position = self::POS_START_OF_DOC;
    }

    /**
     * If not done already, be sure to close the file & xml_parser handlers.
     */
    public function __destruct()
    {
        if (gettype($this->_fh) === 'resource')
            fclose($this->_fh);

        if (gettype($this->_parser) === 'resource')
            xml_parser_free($this->_parser);
    }

    /**
     * @param string $file
     * @param string $formName
     * @param MetadataCollection|null $metadataCollection
     * @return RecordParser
     */
    public static function createWithXMLFile($file, $formName, MetadataCollection $metadataCollection = null)
    {
        $fp = fopen($file, 'rb');
        if ($fp)
        {
            $recordParser = new RecordParser($formName, $metadataCollection);

            $parser = xml_parser_create();

            xml_set_object($parser, $recordParser);
            xml_set_element_handler($parser, 'startElement', 'endElement');
            xml_set_character_data_handler($parser, 'cdataNode');

            $recordParser->_fh = $fp;
            $recordParser->_parser = $parser;

            return $recordParser;
        }

        throw new \RuntimeException(sprintf(
            'Unable to open data file %s for parsing.',
            $file
        ));
    }

    /**
     * @param int $mode
     */
    public function setMode($mode)
    {
        if ($this->_position !== self::POS_START_OF_DOC)
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
        if ($this->_position === self::POS_END_OF_DOC)
            return null;

        if ($this->_mode === self::MODE_READ_RECORD)
            $record = new Record();

        /** @var RecordField $field */
        $field = null;

        while ($line = fgets($this->_fh))
        {
            xml_parse($this->_parser, preg_replace(self::$_invalidByteRemovalRegex, '$1', $line));

            switch($this->_position)
            {
                // This will be reached during multi-line field value parsing.
                case self::POS_IN_FIELD:
                    continue 2;

                // Typically speaking, items will be on a single line.
                // However, in the event of multi-line notes content or the like,
                // we do NOT want to create  an item per-line.
                case self::POS_END_OF_FIELD:
                    $field = new RecordField(
                        $this->_recordID,
                        $this->_formName,
                        $this->_fieldName,
                        $this->_fieldValue,
                        $this->_redcapEventName,
                        $this->_getMetadataItem($this->_fieldName)
                    );
                    $this->_recordID = null;
                    $this->_redcapEventName = null;
                    $this->_fieldName = null;
                    $this->_fieldValue = null;

                    if (null === $this->_previousField)
                    {
                        $field->firstFieldInRecord = true;
                        $this->_previousField = $field;
                        $field = null;
                        continue 2;
                    }

                    if ($this->_previousField->recordID === $field->recordID)
                    {
                        switch($this->_mode)
                        {
                            case self::MODE_READ_RECORD:
                                $record[] = $this->_previousField;
                                $this->_previousField = $field;
                                $field = null;
                                continue 3;

                            case self::MODE_READ_FIELD:
                                $returnField = $this->_previousField;
                                $this->_previousField = $field;
                                $field = null;
                                return $returnField;

                            default: return null;
                        }
                    }

                    if ($this->_previousField->recordID !== $field->recordID)
                    {
                        $this->_previousField->lastFieldInRecord = true;
                        $field->firstFieldInRecord = true;

                        switch($this->_mode)
                        {
                            case self::MODE_READ_RECORD:
                                $record->recordID = $this->_previousField->recordID;
                                $record->formName = $this->_formName;
                                $record[] = $this->_previousField;
                                $this->_previousField = $field;
                                return $record;

                            case self::MODE_READ_FIELD:
                                $returnField = $this->_previousField;
                                $this->_previousField = $field;
                                return $returnField;

                            default: return null;
                        }
                    }

                    break;

                case self::POS_END_OF_DOC:
                    if (null === $this->_previousField)
                        return null;

                    $this->_previousField->lastFieldInRecord = true;

                    switch($this->_mode)
                    {
                        case self::MODE_READ_RECORD:
                            $record[] = $this->_previousField;
                            return $record;

                        case self::MODE_READ_FIELD:
                            return $this->_previousField;

                        default:
                            return null;
                    }
            }
        }

        return null;
    }

    /**
     * @param resource $parser
     * @param string $tag
     * @param array $attributes
     */
    public function startElement($parser, $tag, $attributes)
    {
        switch($tag)
        {
            case 'ITEM':
                $this->_position = self::POS_START_OF_FIELD;
                break;

            case 'RECORD':
            case 'REDCAP_EVENT_NAME':
            case 'FIELD_NAME':
            case 'VALUE':
                $this->_position = self::POS_IN_FIELD;
                $this->_currentElementName = $tag;
                break;

            default:
                $this->_currentElementName = null;
        }
    }

    /**
     * @param resource $parser
     * @param string $cdata
     */
    public function cdataNode($parser, $cdata)
    {
        if ($this->_position === self::POS_IN_FIELD)
        {
            $cdata = trim($cdata);

            if ('' === $cdata)
                return;

            switch($this->_currentElementName)
            {
                case 'RECORD':
                    $this->_recordID = $cdata;
                    break;

                case 'REDCAP_EVENT_NAME':
                    $this->_redcapEventName = $cdata;
                    break;

                case 'FIELD_NAME':
                    $this->_fieldName = $cdata;
                    break;

                case 'VALUE':
                    if (null === $this->_fieldValue)
                        $this->_fieldValue = $cdata;
                    else
                        $this->_fieldValue = sprintf('%s%s', $this->_fieldValue, $cdata);
                    break;
            }
        }
    }

    /**
     * @param resource $parser
     * @param string $tag
     */
    public function endElement($parser, $tag)
    {
        switch($tag)
        {
            case 'ITEM':
                $this->_position = self::POS_END_OF_FIELD;
                break;

            case 'RECORDS':
                $this->_position = self::POS_END_OF_DOC;
                break;
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