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
 * Class MultiSelectImportField
 * @package DCarbone\AmberHat\Import\Field
 */
class MultiSelectImportField extends MultiChoiceImportField
{
    /** @var array */
    private $_exportNames = array();

    /** @var array */
    private $_exportChoices = array();

    /**
     * Constructor
     *
     * @param MetadataItemInterface $metadataItem
     */
    public function __construct(MetadataItemInterface $metadataItem)
    {
        parent::__construct($metadataItem);

        foreach($metadataItem->getExportFieldNameItems() as $exportName)
        {
            $choiceValue = $exportName->getChoiceValue();
            $this->_exportNames[$choiceValue] = $exportName->getExportFieldName();
            $this->_exportChoices[$choiceValue] = '0';
        }
    }

    /**
     * @param string $choice
     */
    public function selectChoice($choice)
    {
        if (isset($this->choices[$choice]))
        {
            $this->_exportChoices[$choice] = '1';
        }
        else if (($idx = array_search($choice, $this->choices)) > -1)
        {
            $this->_exportChoices[$idx] = '1';
        }
        else
        {
            throw new \OutOfRangeException(sprintf(
                '%s::selectChoice - Choice "%s" does not match ID or Label of available choices on field "%s".  Available choices: ["%s"] or ["%s"].',
                get_class($this),
                $choice,
                $this->fieldName,
                implode('", "', array_keys($this->choices)),
                implode('", "', array_values($this->choices))
            ));
        }
    }

    /**
     * @see selectChoice
     *
     * Alias for selectChoice in MultiSelect fields
     *
     * @param mixed $fieldValue
     */
    public function setFieldValue($fieldValue)
    {
        $this->selectChoice($fieldValue);
    }

    /**
     * @param string $recordID
     * @param EventItemInterface $event
     * @return string
     */
    public function createEAVJsonEntry($recordID, EventItemInterface $event = null)
    {
        $fieldEntry = '';

        if (isset($event))
        {
            $format = sprintf("%%s%s,\n", static::EAV_JSON_EVENT_FORMAT);
            foreach($this->_exportNames as $choice=>$exportName)
            {
                $fieldEntry = sprintf(
                    $format,
                    $fieldEntry,
                    $recordID,
                    $exportName,
                    $this->_exportChoices[$choice],
                    $event
                );
            }
        }
        else
        {
            $format = sprintf("%%s%s,\n", static::EAV_JSON_FORMAT);
            foreach($this->_exportNames as $choice=>$exportName)
            {
                $fieldEntry = sprintf(
                    $format,
                    $fieldEntry,
                    $recordID,
                    $exportName,
                    $this->_exportChoices[$choice]
                );
            }
        }

        return rtrim($fieldEntry, "\n,");
    }

    /**
     * @return string
     */
    public function createFlatJsonEntry()
    {
        $fieldEntry = '';

        $format = sprintf("%%s%s,\n", static::FLAT_JSON_FORMAT);

        foreach($this->_exportNames as $choice=>$exportName)
        {
            $fieldEntry = sprintf(
                $format,
                $fieldEntry,
                $exportName,
                $this->_exportChoices[$choice]
            );
        }

        return rtrim($fieldEntry, "\n,");
    }

    /**
     * @param string $recordID
     * @param EventItemInterface $event
     * @return string
     */
    public function createEAVXMLEntry($recordID, EventItemInterface $event = null)
    {
        $fieldEntry = '';

        if (isset($event))
        {
            $format = sprintf("%%s%s\n", static::EAV_XML_EVENT_FORMAT);
            foreach($this->_exportNames as $choice=>$exportName)
            {
                $fieldEntry = sprintf(
                    $format,
                    $fieldEntry,
                    $recordID,
                    $exportName,
                    $this->_exportChoices[$choice],
                    $event
                );
            }
        }
        else
        {
            $format = sprintf("%%s%s\n", static::EAV_XML_FORMAT);
            foreach($this->_exportNames as $choice=>$exportName)
            {
                $fieldEntry = sprintf(
                    $format,
                    $fieldEntry,
                    $recordID,
                    $exportName,
                    $this->_exportChoices[$choice]
                );
            }
        }

        return rtrim($fieldEntry);
    }

    /**
     * @return string
     */
    public function createFlatXMLEntry()
    {
        $fieldEntry = '';

        $format = sprintf("%%s%s\n", static::FLAT_XML_FORMAT);

        foreach($this->_exportNames as $choice=>$exportName)
        {
            $fieldEntry = sprintf(
                $format,
                $fieldEntry,
                $exportName,
                $this->_exportChoices[$choice],
                $exportName
            );
        }

        return rtrim($fieldEntry);
    }
}