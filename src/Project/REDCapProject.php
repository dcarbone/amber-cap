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
use DCarbone\AmberHat\Metadata\MetadataCollection;
use DCarbone\AmberHat\Utilities\ParserUtility;

/**
 * Class REDCapProject
 * @package DCarbone\AmberHat\Project
 */
class REDCapProject
{
    /** @var AmberHatClient */
    private $_client;

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
    private $_formNames = null;

    /**
     * Constructor
     *
     * @param string $redcapApiEndpoint
     * @param string $token
     * @param string $tempDirectory
     * @param bool|false $saveTempFiles
     */
    public function __construct($redcapApiEndpoint, $token, $tempDirectory, $saveTempFiles = false)
    {
        $this->_client = new AmberHatClient($redcapApiEndpoint, $token, $tempDirectory, $saveTempFiles);
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
            ParserUtility::parseMetadataJsonResponse(
                $this->_client->exportArms(array(), 'json'),
                $this->_arms
            );
        }

        return $this->_arms;
    }

    /**
     * @param bool $includeRelationships
     * @return EventsCollection
     */
    public function getEvents($includeRelationships = false)
    {
        if (!isset($this->_events))
        {
            if ($includeRelationships)
                $this->_events = new EventsCollection($this->getArms());
            else
                $this->_events = new EventsCollection();

            ParserUtility::parseMetadataJsonResponse(
                $this->_client->exportEvents(array(), 'json'),
                $this->_events
            );
        }

        return $this->_events;
    }

    /**
     * @param bool $includeRelationships
     * @return \DCarbone\AmberHat\Metadata\MetadataCollection
     */
    public function getMetadata($includeRelationships = false)
    {
        if (!isset($this->_metadata))
        {
            if ($includeRelationships)
                $this->_metadata = new MetadataCollection()
            $this->_metadata = $this->_client->exportMetadata();
        }

        return $this->_metadata;
    }

    /**
     * @return \DCarbone\AmberHat\Instrument\InstrumentsCollection
     */
    public function getInstruments()
    {
        if (!isset($this->_instruments))
            $this->_instruments = $this->_client->exportInstruments();

        return $this->_instruments;
    }

    /**
     * @return \DCarbone\AmberHat\ExportFieldName\ExportFieldNamesCollection
     */
    public function getExportFieldNames()
    {
        if (!isset($this->_exportFieldNames))
            $this->_exportFieldNames = $this->_client->exportExportFieldNames();

        return $this->_exportFieldNames;
    }

    /**
     * @return \DCarbone\AmberHat\Information\ProjectInformation
     */
    public function getInformation()
    {
        if (!isset($this->_information))
            $this->_information = $this->_client->exportProjectInformation();

        return $this->_information;
    }

    /**
     * @return \DCarbone\AmberHat\FormEventMapping\FormEventMappingsCollection
     */
    public function getFormEventMapping()
    {
        if (!isset($this->_formEventMapping))
            $this->_formEventMapping = $this->_client->exportFormEventMappings();

        return $this->_formEventMapping;
    }

    /**
     * @return array
     */
    public function getFormNames()
    {
        if (!isset($this->_formNames))
        {
            $this->_formNames = array();
            /** @var \DCarbone\AmberHat\Instrument\InstrumentItemInterface $instrument */
            foreach($this->getInstruments() as $instrument)
            {
                $this->_formNames[] = $instrument->getInstrumentName();
            }
        }

        return $this->_formNames;
    }
}