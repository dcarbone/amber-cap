<?php namespace DCarbone\AmberHat\User;

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
 * Class UserItem
 * @package DCarbone\AmberHat\User
 */
class UserItem extends AbstractItem implements UserItemInterface
{
    /** @var array */
    protected $properties = array(
        'username' => null,
        'email' => null,
        'firstname' => null,
        'lastname' => null,
        'expiration' => null,
        'data_access_group' => null,
        'data_access_group_id' => null,
        'design' => null,
        'user_rights' => null,
        'data_access_groups' => null,
        'data_export' => null,
        'reports' => null,
        'stats_and_charts' => null,
        'manage_survey_participants' => null,
        'calendar' => null,
        'data_import_tool' => null,
        'data_comparison_tool' => null,
        'logging' => null,
        'file_repository' => null,
        'data_quality_create' => null,
        'data_quality_execute' => null,
        'api_export' => null,
        'api_import' => null,
        'mobile_app' => null,
        'mobile_app_download_data' => null,
        'record_create' => null,
        'record_rename' => null,
        'record_delete' => null,
        'lock_records_all_forms' => null,
        'lock_records' => null,
        'lock_records_customization' => null,
        'forms' => array()
    );

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->properties['username'];
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->properties['email'];
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->properties['firstname'];
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->properties['lastname'];
    }

    /**
     * @return string
     */
    public function getExpiration()
    {
        return $this->properties['expiration'];
    }

    /**
     * @return string
     */
    public function getDataAccessGroup()
    {
        return $this->properties['data_access_group'];
    }

    /**
     * @return string
     */
    public function getDataAccessGroupID()
    {
        return $this->properties['data_access_group_id'];
    }

    /**
     * @return bool
     */
    public function canDesign()
    {
        return $this->properties['design'] == 1;
    }

    /**
     * @return bool
     */
    public function canModifyUserRights()
    {
        return $this->properties['user_rights'] == 1;
    }

    /**
     * @return bool
     */
    public function canModifyDataAccessGroups()
    {
        return $this->properties['data_access_groups'] == 1;
    }

    /**
     * @return bool
     */
    public function canExportData()
    {
        return $this->properties['data_export'] == 1;
    }

    /**
     * @return bool
     */
    public function canAccessReports()
    {
        return $this->properties['reports'] == 1;
    }

    /**
     * @return bool
     */
    public function canAccessStatsAndCharts()
    {
        return $this->properties['stats_and_charts'] == 1;
    }

    /**
     * @return bool
     */
    public function canManageSurveyParticipants()
    {
        return $this->properties['manage_survey_participants'] == 1;
    }

    /**
     * @return bool
     */
    public function canAccessCalendar()
    {
        return $this->properties['calendar'] == 1;
    }

    /**
     * @return bool
     */
    public function canUseDataImportTool()
    {
        return $this->properties['data_import_tool'] == 1;
    }

    /**
     * @return bool
     */
    public function canUseDataComparisonTool()
    {
        return $this->properties['data_comparison_tool'] == 1;
    }

    /**
     * @return bool
     */
    public function canAccessLogging()
    {
        return $this->properties['logging'] == 1;
    }

    /**
     * @return bool
     */
    public function canAccessFileRepository()
    {
        return $this->properties['file_repository'] == 1;
    }

    /**
     * @return bool
     */
    public function canCreateDataQuality()
    {
        return $this->properties['data_quality_create'] == 1;
    }

    /**
     * @return bool
     */
    public function canExecuteDataQuality()
    {
        return $this->properties['data_quality_execute'] == 1;
    }

    /**
     * @return bool
     */
    public function canExportViaAPI()
    {
        return $this->properties['api_export'] == 1;
    }

    /**
     * @return bool
     */
    public function canImportViaAPI()
    {
        return $this->properties['api_import'] == 1;
    }

    /**
     * @return bool
     */
    public function canUseMobileApp()
    {
        return $this->properties['mobile_app'] == 1;
    }

    /**
     * @return bool
     */
    public function canDownloadDataInMobileApp()
    {
        return $this->properties['mobile_app_download_data'] == 1;
    }

    /**
     * @return bool
     */
    public function canCreateRecords()
    {
        return $this->properties['record_create'] == 1;
    }

    /**
     * @return bool
     */
    public function canRenameRecords()
    {
        return $this->properties['record_rename'] == 1;
    }

    /**
     * @return bool
     */
    public function canDeleteRecords()
    {
        return $this->properties['record_delete'] == 1;
    }

    /**
     * @return bool
     */
    public function canLockAllFormRecords()
    {
        return $this->properties['lock_records_all_forms'] == 1;
    }

    /**
     * @return bool
     */
    public function canLockRecords()
    {
        return $this->properties['lock_records'] == 1;
    }

    /**
     * @return bool
     */
    public function canCustomizeLockRecords()
    {
        return $this->properties['lock_records_customization'] == 1;
    }

    /**
     * @return array
     */
    public function getFormsAccess()
    {
        return $this->properties['forms'];
    }

    /**
     * @param string $form
     * @return bool
     */
    public function hasAccessToForm($form)
    {
        if (isset($this->properties['forms'][$form]))
            return $this->properties['forms'][$form] == 1;

        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->properties['username'];
    }
}