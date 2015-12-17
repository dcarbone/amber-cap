# User Export

## Method
```php
$users = $client->getUsers();
```

## Response Classes

Response will be of type [UsersCollection](../src/User/UsersCollection.php) containing
1+ [UserItemInterface](../src/User/UserItemInterface.php) objects.

## Example Usage

```php
echo '<pre>';

foreach($client->getUsers() as $user)
{
    echo <<<STRING
Username: {$user->getUsername()}
Email: {$user->getEmail()}
First Name: {$user->getFirstName()}
Last Name: {$user->getLastName()}
Expiration: {$user->getExpiration()}
Data Access Group: {$user->getDataAccessGroup()}
Data Access Group ID: {$user->getDataAccessGroupID()}

STRING;
    echo "Can Design: ";
    var_dump($user->canDesign());
    echo "Can Modify User Rights: ";
    var_dump($user->canModifyUserRights());
    echo "Can Modify Data Access Groups: ";
    var_dump($user->canModifyDataAccessGroups());
    echo "Data Export Permission Level: ";
    var_dump($user->getDataExportPermissionLevel());
    echo "Can Access Reports: ";
    var_dump($user->canAccessReports());
    echo "Can Access Stats and Charts: ";
    var_dump($user->canAccessStatsAndCharts());
    echo "Can Manage Survey Participants: ";
    var_dump($user->canManageSurveyParticipants());
    echo "Can Access Calendar: ";
    var_dump($user->canAccessCalendar());
    echo "Can Use Data Import Tool: ";
    var_dump($user->canUseDataImportTool());
    echo "Can Use Data Comparison Tool: ";
    var_dump($user->canUseDataComparisonTool());
    echo "Can Access Logging: ";
    var_dump($user->canAccessLogging());
    echo "Can Access File Repository: ";
    var_dump($user->canAccessFileRepository());
    echo "Can Create Data Quality: ";
    var_dump($user->canCreateDataQuality());
    echo "Can Execute Data Quality: ";
    var_dump($user->canExecuteDataQuality());
    echo "Can Export Via API: ";
    var_dump($user->canExportViaAPI());
    echo "Can Import Via API: ";
    var_dump($user->canImportViaAPI());
    echo "Can Use Mobile App: ";
    var_dump($user->canUseMobileApp());
    echo "Can Download Data In Mobile App: ";
    var_dump($user->canDownloadDataInMobileApp());
    echo "Can Create Records: ";
    var_dump($user->canCreateRecords());
    echo "Can Rename Records: ";
    var_dump($user->canRenameRecords());
    echo "Can Delete Records: ";
    var_dump($user->canDeleteRecords());
    echo "Can Lock All Form Records: ";
    var_dump($user->canLockAllFormRecords());
    echo "Can Lock Records Custom: ";
    var_dump($user->canCustomizeLockRecords());
    echo "Form Access: ";
    echo var_export($user->getFormsAccess(), true);

    echo sprintf("\n%s\n", str_repeat('-', 50));
}

echo '</pre>';
```
