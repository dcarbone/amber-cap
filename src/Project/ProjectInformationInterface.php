<?php namespace DCarbone\AmberHat\Project;

use DCarbone\AmberHat\AmberHatItemInterface;

/**
 * Interface ProjectInformationInterface
 * @package DCarbone\AmberHat\Project
 */
interface ProjectInformationInterface extends AmberHatItemInterface
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