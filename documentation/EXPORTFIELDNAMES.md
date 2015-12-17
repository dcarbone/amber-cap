# Export Field Names Data Export

## Method
```php
$exportFieldNames = $client->getExportFieldNames();
```

## Response

Response will be of type
[ExportFieldNamesCollection](../src/ExportFieldName/ExportFieldNamesCollection.php), containing
1+ [ExportFieldNameItemInterface](../src/ExportFieldName/ExportFieldNameItemInterface.php) objects.

## Example Usage

```php
echo '<pre>';
foreach($client->getExportFieldNames() as $exportNameItem)
{
    echo <<<STRING
Original Field Name: {$exportNameItem['original_field_name']}
Choice Value: {$exportNameItem['choice_value']}
Export Field Name: {$exportNameItem['export_field_name']}
STRING;
    echo sprintf("\n%s\n\n", str_repeat('-', 50));
}
echo '</pre>';
```
