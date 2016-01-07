<?php namespace DCarbone\AmberHat\Import;

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
use DCarbone\AmberHat\FormEventMapping\FormEventMappingsCollection;
use DCarbone\AmberHat\Import\Field\MultiChoiceImportField;
use DCarbone\AmberHat\Import\Field\MultiSelectImportField;
use DCarbone\AmberHat\Import\Field\SimpleImportField;
use DCarbone\AmberHat\Information\ProjectInformationInterface;
use DCarbone\AmberHat\Metadata\MetadataCollection;

/**
 * Class ImportRecordFactory
 * @package DCarbone\AmberHat\Import
 */
class ImportRecordFactory
{
    /** @var ProjectInformationInterface */
    private $_projectInformation;
    /** @var MetadataCollection */
    private $_metadataCollection;
    /** @var FormEventMappingsCollection */
    private $_formEventMappingsCollection;

    /**
     * Constructor
     *
     * @param ProjectInformationInterface $projectInformation
     * @param MetadataCollection $metadataCollection
     * @param FormEventMappingsCollection $formEventMappingsCollection
     */
    public function __construct(ProjectInformationInterface $projectInformation,
                                MetadataCollection $metadataCollection,
                                FormEventMappingsCollection $formEventMappingsCollection)
    {
        $this->_projectInformation = $projectInformation;
        $this->_metadataCollection = $metadataCollection;
        $this->_formEventMappingsCollection = $formEventMappingsCollection;
    }

    /**
     * @param string $recordID
     * @param EventItemInterface|null $event
     * @return array|ImportRecord
     */
    public function createImportRecord($recordID, EventItemInterface $event = null)
    {
        $importRecord = new ImportRecord($recordID, $event);

        /** @var \DCarbone\AmberHat\Metadata\MetadataItemInterface $metadataItem */
        foreach($this->_metadataCollection as $metadataItem)
        {
            switch($metadataItem['field_type'])
            {
                case 'checkbox':
                    $field = new MultiSelectImportField($metadataItem);
                    break;

                case 'yesno':
                case 'truefalse':
                case 'dropdown':
                case 'radio':
                    $field = new MultiChoiceImportField($metadataItem);
                    break;

                default:
                    $field = new SimpleImportField($metadataItem);
                    break;
            }

            $importRecord[] = $field;
        }

        return $importRecord;
    }
}