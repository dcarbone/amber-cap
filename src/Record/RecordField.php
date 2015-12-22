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

use DCarbone\AmberHat\Metadata\MetadataItemInterface;

/**
 * Class RecordField
 * @package DCarbone\AmberHat\Record
 */
class RecordField implements RecordFieldInterface
{
    /** @var string */
    public $recordID;
    /** @var string */
    public $instrumentName;
    /** @var string|null */
    public $redcapEventName;

    /** @var string */
    public $fieldName;
    /** @var string */
    public $fieldValue;

    /** @var bool */
    public $firstFieldInRecord = false;
    /** @var bool */
    public $lastFieldInRecord = false;

    /** @var MetadataItemInterface|null */
    private $_metadataItem;

    /**
     * Constructor
     * @param $recordID
     * @param $instrumentName
     * @param string $fieldName
     * @param string $fieldValue
     * @param null $redcapEventName
     * @param MetadataItemInterface|null $metadataItem
     */
    public function __construct($recordID,
                                $instrumentName,
                                $fieldName,
                                $fieldValue,
                                $redcapEventName = null,
                                MetadataItemInterface $metadataItem = null)
    {
        $this->recordID = $recordID;
        $this->instrumentName = $instrumentName;
        $this->fieldName = $fieldName;
        $this->fieldValue = $fieldValue;
        $this->redcapEventName = $redcapEventName;
        $this->_metadataItem = $metadataItem;
    }

    /**
     * @return string
     */
    public function getRecordID()
    {
        return $this->recordID;
    }

    /**
     * @return string
     */
    public function getInstrumentName()
    {
        return $this->instrumentName;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return string
     */
    public function getFieldValue()
    {
        return $this->fieldValue;
    }

    /**
     * @return null|string
     */
    public function getRedcapEventName()
    {
        return $this->redcapEventName;
    }

    /**
     * @return MetadataItemInterface|null
     */
    public function getMetadataItem()
    {
        return $this->_metadataItem;
    }

    /**
     * @return bool
     */
    public function isFirstFieldInRecord()
    {
        return $this->firstFieldInRecord;
    }

    /**
     * @return bool
     */
    public function isLastFieldInRecord()
    {
        return $this->lastFieldInRecord;
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
            $this->recordID,
            $this->instrumentName,
            $this->fieldName,
            $this->fieldValue,
            $this->firstFieldInRecord,
            $this->lastFieldInRecord,
            $this->_metadataItem,
            $this->redcapEventName,
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
        if (count($data) === 8
            && is_string($data[0])
            && is_string($data[1])
            && is_string($data[2])
            && (null === $data[3] || is_scalar($data[3]))
            && is_bool($data[4])
            && is_bool($data[5])
            && (null === $data[6] || $data[6] instanceof MetadataItemInterface)
            && (null === $data[7] || is_string($data[7])))
        {
            $this->recordID = $data[0];
            $this->instrumentName = $data[1];
            $this->fieldName = $data[2];
            $this->fieldValue = $data[3];
            $this->firstFieldInRecord = $data[4];
            $this->lastFieldInRecord = $data[5];
            $this->_metadataItem = $data[6];
            $this->redcapEventName = $data[7];
        }
        else
        {
            throw new \DomainException(sprintf(
                '%s - Unstable serialized object representation seen.',
                get_class($this)
            ));
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'recordID' => $this->recordID,
            'instrumentName' => $this->instrumentName,
            'fieldName' => $this->fieldName,
            'fieldValue' => $this->fieldValue,
            'firstFieldInRecord' => $this->firstFieldInRecord,
            'lastFieldInRecord' => $this->lastFieldInRecord,
            'redcapEventName' => $this->redcapEventName,
            'metadataItem' => (isset($this->_metadataItem) ? $this->_metadataItem->jsonSerialize() : null)
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->fieldValue;
    }
}