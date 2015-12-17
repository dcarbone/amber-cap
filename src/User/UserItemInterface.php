<?php namespace DCarbone\AmberHat\User;

use DCarbone\AmberHat\ItemInterface;

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

/**
 * Interface UserItemInterface
 * @package DCarbone\AmberHat\User
 */
interface UserItemInterface extends ItemInterface
{
    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @return string
     */
    public function getExpiration();

    /**
     * @return string
     */
    public function getDataAccessGroup();

    /**
     * @return string
     */
    public function getDataAccessGroupID();

    /**
     * @return bool
     */
    public function canDesign();

    /**
     * @return bool
     */
    public function canModifyUserRights();

    /**
     * @return bool
     */
    public function canModifyDataAccessGroups();

    /**
     * @return bool
     */
    public function canExportData();

    /**
     * @return bool
     */
    public function canAccessReports();

    /**
     * @return bool
     */
    public function canAccessStatsAndCharts();

    /**
     * @return bool
     */
    public function canManageSurveyParticipants();

    /**
     * @return bool
     */
    public function canAccessCalendar();

    /**
     * @return bool
     */
    public function canUseDataImportTool();

    /**
     * @return bool
     */
    public function canUseDataComparisonTool();

    /**
     * @return bool
     */
    public function canAccessLogging();

    /**
     * @return bool
     */
    public function canAccessFileRepository();

    /**
     * @return bool
     */
    public function canCreateDataQuality();

    /**
     * @return bool
     */
    public function canExecuteDataQuality();

    /**
     * @return bool
     */
    public function canExportViaAPI();

    /**
     * @return bool
     */
    public function canImportViaAPI();

    /**
     * @return bool
     */
    public function canUseMobileApp();

    /**
     * @return bool
     */
    public function canDownloadDataInMobileApp();

    /**
     * @return bool
     */
    public function canCreateRecords();

    /**
     * @return bool
     */
    public function canRenameRecords();

    /**
     * @return bool
     */
    public function canDeleteRecords();

    /**
     * @return bool
     */
    public function canLockAllFormRecords();

    /**
     * @return bool
     */
    public function canLockRecords();

    /**
     * @return bool
     */
    public function canCustomizeLockRecords();

    /**
     * @return array
     */
    public function getFormsAccess();

    /**
     * @param string $form
     * @return bool
     */
    public function hasAccessToForm($form);
}