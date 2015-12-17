# Project Information Data Export

## Method
```php
$projectInfo = $client->getProjectInformation();
```

Response will be of type 
[ProjectInformationInterface](../src/Information/ProjectInformationInterface.php).

## Example Usage

```php
echo '<pre>';
echo <<<STRING
Project ID: {$projectInfo['project_id']}
Project Title: {$projectInfo['project_title']}
Creation Time: {$projectInfo['creation_time']}
Production Time: {$projectInfo['production_time']}
In Production: {$projectInfo['in_production']}
Project Language: {$projectInfo['project_language']}
Purpose: {$projectInfo['purpose']}
Purpose Other: {$projectInfo['purpose_other']}
Custom Record Label: {$projectInfo['custom_record_label']}
Is Longitudinal: {$projectInfo['is_longitudinal']}
Surveys Enabled: {$projectInfo['surveys_enabled']}
Scheduling Enabled: {$projectInfo['scheduling_enabled']}
Record Auto-Numbering Enabled: {$projectInfo['record_autonumbering_enabled']}
Randomization Enabled: {$projectInfo['randomization_enabled']}
DDP Enabled: {$projectInfo['ddp_enabled']}
Project IRB Number: {$projectInfo['project_irb_number']}
Project Grant Number: {$projectInfo['project_grant_number']}
Project PI First Name: {$projectInfo['project_pi_firstname']}
Project PI Last Name: {$projectInfo['project_pi_lastname']}
STRING;
echo '<pre>';
```

## Dates

For the fields `creation_time` and `production_time`, you may optionally pass in a boolean value to
their getters to return a [DateTime](http://php.net/manual/en/class.datetime.php) object instead
of a string.