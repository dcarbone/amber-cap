<?php namespace DCarbone\AmberHat\Project;

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

use DCarbone\AmberHat\AmberHatClient;
use DCarbone\AmberHat\Arm\ArmsCollection;
use DCarbone\AmberHat\Event\EventsCollection;
use DCarbone\AmberHat\ExportFieldName\ExportFieldNamesCollection;
use DCarbone\AmberHat\FormEventMapping\FormEventMappingsCollection;
use DCarbone\AmberHat\Information\ProjectInformation;
use DCarbone\AmberHat\Instrument\InstrumentItemInterface;
use DCarbone\AmberHat\Instrument\InstrumentsCollection;
use DCarbone\AmberHat\Metadata\MetadataCollection;
use DCarbone\AmberHat\Record\RecordParser;
use DCarbone\AmberHat\Utilities\ParserUtility;

/**
 * Class REDCapProject
 * @package DCarbone\AmberHat\Project
 */
class REDCapProject
{
    /** @var AmberHatClient */
    private $_client;

    private $_buildRelationships;

    /** @var \DCarbone\AmberHat\Arm\ArmsCollection */
    private $_arms = null;
    /** @var \DCarbone\AmberHat\Event\EventsCollection */
    private $_events = null;
    /** @var \DCarbone\AmberHat\Metadata\MetadataCollection */
    private $_metadata = null;
    /** @var \DCarbone\AmberHat\Instrument\InstrumentsCollection */
    private $_instruments = null;
    /** @var \DCarbone\AmberHat\ExportFieldName\ExportFieldNamesCollection */
    private $_exportFieldNames = null;
    /** @var \DCarbone\AmberHat\Information\ProjectInformation */
    private $_information = null;
    /** @var \DCarbone\AmberHat\FormEventMapping\FormEventMappingsCollection */
    private $_formEventMapping = null;

    /** @var array */
    private $_instrumentNames = null;

    /**
     * Constructor
     *
     * @param string $redcapApiEndpoint
     * @param string $token
     * @param string $tempDirectory
     * @param bool $buildRelationships
     * @param bool|false $saveTempFiles
     */
    public function __construct($redcapApiEndpoint,
                                $token,
                                $tempDirectory,
                                $buildRelationships = false,
                                $saveTempFiles = false)
    {
        $this->_client = new AmberHatClient($redcapApiEndpoint, $token, $tempDirectory, $saveTempFiles);
        $this->_buildRelationships = $buildRelationships;
    }

    /**
     * @return AmberHatClient
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * @return \DCarbone\AmberHat\Arm\ArmsCollection
     */
    public function getArms()
    {
        if (!isset($this->_arms))
        {
            $this->_arms = new ArmsCollection();
            ParserUtility::populateMetadataCollection(
                $this->_client->exportArms(array(), 'json'),
                $this->_arms
            );
        }

        return $this->_arms;
    }

    /**
     * @return EventsCollection
     */
    public function getEvents()
    {
        if (!isset($this->_events))
        {
            if ($this->_buildRelationships)
                $this->_events = new EventsCollection($this->getArms());
            else
                $this->_events = new EventsCollection();

            ParserUtility::populateMetadataCollection(
                $this->_client->exportEvents(array(), 'json'),
                $this->_events
            );
        }

        return $this->_events;
    }

    /**
     * @return \DCarbone\AmberHat\Metadata\MetadataCollection
     */
    public function getMetadata()
    {
        if (!isset($this->_metadata))
        {
            if ($this->_buildRelationships)
                $this->_metadata = new MetadataCollection($this->getExportFieldNames(), $this->getInstruments());
            else
                $this->_metadata = new MetadataCollection();

            ParserUtility::populateMetadataCollection(
                $this->_client->exportMetadata(array(), array(), 'json'),
                $this->_metadata
            );
        }

        return $this->_metadata;
    }

    /**
     * @return \DCarbone\AmberHat\Instrument\InstrumentsCollection
     */
    public function getInstruments()
    {
        if (!isset($this->_instruments))
        {
            $this->_instruments = new InstrumentsCollection();

            ParserUtility::populateMetadataCollection(
                $this->_client->exportInstruments('json'),
                $this->_instruments
            );
        }

        return $this->_instruments;
    }

    /**
     * @return \DCarbone\AmberHat\ExportFieldName\ExportFieldNamesCollection
     */
    public function getExportFieldNames()
    {
        if (!isset($this->_exportFieldNames))
        {
            $this->_exportFieldNames = new ExportFieldNamesCollection();

            ParserUtility::populateMetadataCollection(
                $this->_client->exportExportFieldNames('json'),
                $this->_exportFieldNames
            );
        }

        return $this->_exportFieldNames;
    }

    /**
     * @return FormEventMappingsCollection
     */
    public function getFormEventMapping()
    {
        if (!isset($this->_formEventMapping))
        {
            if ($this->_buildRelationships)
            {
                $this->_formEventMapping= new FormEventMappingsCollection(
                    $this->getArms(),
                    $this->getEvents(),
                    $this->getInstruments()
                );
            }
            else
            {
                $this->_formEventMapping = new FormEventMappingsCollection();
            }

            ParserUtility::populateMetadataCollection(
                $this->_client->exportFormEventMappings('json'),
                $this->_formEventMapping
            );
        }

        return $this->_formEventMapping;
    }

    /**
     * @return ProjectInformation
     */
    public function getInformation()
    {
        if (!isset($this->_information))
        {
            $this->_information = ProjectInformation::createFromArray(
                ParserUtility::parseJsonResponse(
                    $this->_client->exportProjectInformation('json')
                )
            );
        }

        return $this->_information;
    }

    /**
     * @return array
     */
    public function getInstrumentNames()
    {
        if (!isset($this->_instrumentNames))
        {
            $this->_instrumentNames = array();
            /** @var \DCarbone\AmberHat\Instrument\InstrumentItemInterface $instrument */
            foreach($this->getInstruments() as $instrument)
            {
                $this->_instrumentNames[] = $instrument->getInstrumentName();
            }
        }

        return $this->_instrumentNames;
    }

    /**
     * @see getInstrumentNames()
     *
     * @return array
     */
    public function getFormNames()
    {
        return $this->getInstrumentNames();
    }

    /**
     * @param InstrumentItemInterface|string $instrument
     * @return RecordParser
     */
    public function getInstrumentRecordParser($instrument)
    {
        if ($instrument instanceof InstrumentItemInterface)
            $instrument = $instrument->getInstrumentName();

        return RecordParser::createWithXMLFile(
            $this->_client->exportFormRecords($instrument),
            $instrument,
            $this->getMetadata()
        );
    }
}