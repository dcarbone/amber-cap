# Export Field Names Data Export

Once you have built your client, you may request Export Field Names data for a project as such:

```php
$exportFieldNames = $client->getExportFieldNames();
```

The response comes in the form of a
[ExportFieldNamesCollection](../src/ExportFieldName/ExportFieldNamesCollection.php) object, with
each object in the collection implementing
[ExportFieldNameItemInterface](../src/ExportFieldName/ExportFieldNameItemInterface.php).

Ex:
```php
/** @var \DCarbone\AmberHat\ExportFieldName\ExportFieldNameItemInterface $armItem **/
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