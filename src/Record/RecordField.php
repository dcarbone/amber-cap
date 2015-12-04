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
    public $responseFieldName;
    /** @var string */
    public $fieldValue;

    /** @var bool */
    public $firstFieldInItem = false;
    /** @var bool */
    public $lastFieldInItem = false;

    /** @var MetadataItemInterface|null */
    private $_metadataItem;

    /**
     * Constructor
     * @param string $responseFieldName
     * @param string $fieldValue
     * @param MetadataItemInterface|null $metadataItem
     */
    public function __construct($responseFieldName,
                                $fieldValue,
                                MetadataItemInterface $metadataItem = null)
    {
        $this->responseFieldName = $responseFieldName;
        $this->fieldValue = $fieldValue;
        $this->_metadataItem = $metadataItem;
    }

    /**
     * @return MetadataItemInterface|null
     */
    public function getMetadataItem()
    {
        return $this->_metadataItem;
    }

    /**
     * Name of field in Record
     *
     * @return string
     */
    public function getResponseFieldName()
    {
        return $this->responseFieldName;
    }

    /**
     * @return string
     */
    public function getFieldValue()
    {
        return $this->fieldValue;
    }

    /**
     * @return boolean
     */
    public function isFirstFieldInItem()
    {
        return $this->firstFieldInItem;
    }

    /**
     * @return boolean
     */
    public function isLastFieldInItem()
    {
        return $this->lastFieldInItem;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->fieldValue;
    }
}