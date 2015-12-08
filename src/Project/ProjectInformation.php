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

use DCarbone\AmberHat\AbstractItem;

/**
 * Class ProjectInformation
 * @package DCarbone\AmberHat\Project
 */
class ProjectInformation extends AbstractItem implements ProjectInformationInterface
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /** @var array */
    protected $properties = array(
        'project_id' => null,
        'project_title' => null,
        'creation_time' => null,
        'production_time' => null,
        'in_production' => null,
        'project_language' => null,
        'purpose' => null,
        'purpose_other' => null,
        'project_notes' => null,
        'custom_record_label' => null,
        'is_longitudinal' => null,
        'surveys_enabled' => null,
        'scheduling_enabled' => null,
        'record_autonumbering_enabled' => null,
        'randomization_enabled' => null,
        'ddp_enabled' => null,
        'project_irb_number' => null,
        'project_grant_number' => null,
        'project_pi_firstname' => null,
        'project_pi_lastname' => null,
    );

    /** @var null|\DateTime */
    private $_creationTimeDateTime = null;
    /** @var null|\DateTime */
    private $_productionTimeDateTime = null;

    /**
     * @param $xml
     * @return \DCarbone\AmberHat\Project\ProjectInformationInterface
     */
    public static function createWithXMLString($xml)
    {
        $sxe = new \SimpleXMLElement($xml, LIBXML_COMPACT | LIBXML_NOBLANKS);

        if ($sxe instanceof \SimpleXMLElement)
            return parent::createFromSXE($sxe);

        throw new \InvalidArgumentException('Unable to parse provided XML string.');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->properties['project_title'];
    }

    /**
     * @return int
     */
    public function getProjectID()
    {
        return $this->properties['project_id'];
    }

    /**
     * @return string
     */
    public function getProjectTitle()
    {
        return $this->properties['project_title'];
    }

    /**
     * @param bool $asDateTime
     * @return \DateTime|string
     */
    public function getCreationTime($asDateTime = false)
    {
        if ($asDateTime && isset($this->properties['creation_time']))
        {
            if (!isset($this->_creationTimeDateTime))
                $this->_creationTimeDateTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $this->properties['creation_time']);

            return $this->_creationTimeDateTime;
        }

        return $this->properties['creation_time'];
    }

    /**
     * @param bool $asDateTime
     * @return \DateTime|string
     */
    public function getProductionTime($asDateTime = false)
    {
        if ($asDateTime && isset($this->properties['production_time']))
        {
            if (!isset($this->_productionTimeDateTime))
                $this->_productionTimeDateTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $this->properties['production_time']);

            return $this->_productionTimeDateTime;
        }

        return $this->properties['production_time'];
    }

    /**
     * @return bool
     */
    public function isInProduction()
    {
        return (bool)$this->properties['in_production'];
    }

    /**
     * @return string
     */
    public function getProjectLanguage()
    {
        return $this->properties['project_language'];
    }

    /**
     * @return string
     */
    public function getPurpose()
    {
        return $this->properties['purpose'];
    }

    /**
     * @return string
     */
    public function getPurposeOther()
    {
        return $this->properties['purpose_other'];
    }

    /**
     * @return string
     */
    public function getProjectNotes()
    {
        return $this->properties['project_notes'];
    }

    /**
     * @return string
     */
    public function getCustomRecordLabel()
    {
        return $this->properties['custom_record_label'];
    }

    /**
     * @return bool
     */
    public function isLongitudinal()
    {
        return (bool)$this->properties['is_longitudinal'];
    }

    /**
     * @return bool
     */
    public function areSurveysEnabled()
    {
        return (bool)$this->properties['surveys_enabled'];
    }

    /**
     * @return bool
     */
    public function isSchedulingEnabled()
    {
        return (bool)$this->properties['scheduling_enabled'];
    }

    /**
     * @return bool
     */
    public function isRecordAutoNumberingEnabled()
    {
        return (bool)$this->properties['record_autonumbering_enabled'];
    }

    /**
     * @return bool
     */
    public function isRandomizationEnabled()
    {
        return (bool)$this->properties['randomization_enabled'];
    }

    /**
     * @return bool
     */
    public function isDDPEnabled()
    {
        return (bool)$this->properties['ddp_enabled'];
    }

    /**
     * @return string
     */
    public function getProjectIRBNumber()
    {
        return $this->properties['project_irb_number'];
    }

    /**
     * @return string
     */
    public function getProjectGrantNumber()
    {
        return $this->properties['project_grant_number'];
    }

    /**
     * @return string
     */
    public function getProjectPIFirstName()
    {
        return $this->properties['project_pi_firstname'];
    }

    /**
     * @return string
     */
    public function getProjectPILastName()
    {
        return $this->properties['project_pi_lastname'];
    }
}