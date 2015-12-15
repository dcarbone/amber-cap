<?php namespace DCarbone\AmberHat\Information;

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

use DCarbone\AmberHat\ItemInterface;

/**
 * Interface ProjectInformationInterface
 * @package DCarbone\AmberHat\Information
 */
interface ProjectInformationInterface extends ItemInterface
{
    /**
     * @return int
     */
    public function getProjectID();

    /**
     * @return string
     */
    public function getProjectTitle();

    /**
     * @param bool $asDateTime
     * @return \DateTime|string
     */
    public function getCreationTime($asDateTime = false);

    /**
     * @param bool $asDateTime
     * @return \DateTime|string
     */
    public function getProductionTime($asDateTime = false);

    /**
     * @return bool
     */
    public function isInProduction();

    /**
     * @return string
     */
    public function getProjectLanguage();

    /**
     * @return string
     */
    public function getPurpose();

    /**
     * @return string
     */
    public function getPurposeOther();

    /**
     * @return string
     */
    public function getProjectNotes();

    /**
     * @return string
     */
    public function getCustomRecordLabel();

    /**
     * @return bool
     */
    public function isLongitudinal();

    /**
     * @return bool
     */
    public function areSurveysEnabled();

    /**
     * @return bool
     */
    public function isSchedulingEnabled();

    /**
     * @return bool
     */
    public function isRecordAutoNumberingEnabled();

    /**
     * @return bool
     */
    public function isRandomizationEnabled();

    /**
     * @return bool
     */
    public function isDDPEnabled();

    /**
     * @return string
     */
    public function getProjectIRBNumber();

    /**
     * @return string
     */
    public function getProjectGrantNumber();

    /**
     * @return string
     */
    public function getProjectPIFirstName();

    /**
     * @return string
     */
    public function getProjectPILastName();
}