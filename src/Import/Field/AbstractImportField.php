<?php namespace DCarbone\AmberHat\Import\Field;

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

use DCarbone\AmberHat\Event\EventItemInterface;
use DCarbone\AmberHat\Metadata\MetadataItemInterface;

/**
 * Class AbstractImportField
 * @package DCarbone\AmberHat\Import\Field
 */
abstract class AbstractImportField implements ImportFieldInterface
{
    const EAV_JSON_EVENT_FORMAT = '{"record":"%s","field_name":"%s","value":"%s","redcap_event_name":"%s"}';
    const EAV_JSON_FORMAT = '{"record":"%s","field_name":"%s","value":"%s"}';

    const EAV_XML_EVENT_FORMAT = '<item><record>%s</record><field_name>%s</field_name><value><![CDATA[%s]]></value><redcap_event_name>%s</redcap_event_name></item>';
    const EAV_XML_FORMAT = '<item><record>%s</record><field_name>%s</field_name><value><![CDATA[%s]]></value></item>';

    const FLAT_JSON_FORMAT = '{"%s":"%s"}';

    const FLAT_XML_FORMAT = '<%s><![CDATA[%s]]></%s>';

    /** @var string */
    protected $instrumentName;

    /** @var string */
    protected $fieldName;

    /** @var mixed */
    protected $fieldValue = null;

    /** @var MetadataItemInterface */
    protected $metadataItem;

    /**
     * Constructor
     *
     * @param MetadataItemInterface $metadataItem
     */
    public function __construct(MetadataItemInterface $metadataItem)
    {
        $this->instrumentName = $metadataItem['form_name'];
        $this->fieldName = $metadataItem['field_name'];
        $this->metadataItem = $metadataItem;
    }

    /**
     * @return string
     */
    public function getInstrumentName()
    {
        return $this->instrumentName;
    }

    /**
     * @see getInstrumentName
     *
     * Alias for getInstrumentName
     *
     * @return string
     */
    public function getFormName()
    {
        return $this->getInstrumentName();
    }

    /**
     * @return string|array
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param mixed $fieldValue
     */
    public function setFieldValue($fieldValue)
    {
        $this->fieldValue = $fieldValue;
    }

    /**
     * @return mixed
     */
    public function getFieldValue()
    {
        return $this->fieldValue;
    }

    /**
     * @param string $recordID
     * @param EventItemInterface $event
     * @return string
     */
    public function createEAVJsonEntry($recordID, EventItemInterface $event = null)
    {
        if (isset($event))
        {
            return sprintf(
                self::EAV_JSON_EVENT_FORMAT,
                $recordID,
                $this->fieldName,
                $this->fieldValue,
                $event
            );
        }

        return sprintf(
            self::EAV_JSON_FORMAT,
            $recordID,
            $this->fieldName,
            $this->fieldValue
        );
    }

    /**
     * @return string
     */
    public function createFlatJsonEntry()
    {
        return sprintf(self::FLAT_JSON_FORMAT, $this->fieldName, $this->fieldValue);
    }

    /**
     * @param string $recordID
     * @param EventItemInterface $event
     * @return string
     */
    public function createEAVXMLEntry($recordID, EventItemInterface $event = null)
    {
        if (isset($event))
        {
            return sprintf(
                self::EAV_XML_EVENT_FORMAT,
                $recordID,
                $this->fieldName,
                $this->fieldValue,
                $event
            );
        }

        return sprintf(
            self::EAV_XML_FORMAT,
            $recordID,
            $this->fieldName,
            $this->fieldValue
        );
    }

    /**
     * @return string
     */
    public function createFlatXMLEntry()
    {
        return sprintf(
            self::FLAT_XML_FORMAT,
            $this->fieldName,
            $this->fieldValue,
            $this->fieldName
        );
    }
}