# Form Event Mapping Export

## Method
```php
$formEvents = $client->getFormEventMappings();
```

## Response

Response will be of type
[FormEventMappingsCollection](../src/FormEventMapping/FormEventMappingsCollection.php), containing
0+ [FormEventMappingItemInterface](../src/FormEventMapping/FormEventMappingItemInterface.php) objects.

## Example Usage

```php
foreach($client->getFormEventMappings() as $formEventMapping)
{
    echo <<<STRING
Arm Num: {$formEventMapping->getArmNum()}
Unique Event Name: {$formEventMapping->getUniqueEventName()}
Form: {$formEventMapping->getForm()}
STRING;

    echo sprintf("\n%s\n", str_repeat('-', 50));
}
```