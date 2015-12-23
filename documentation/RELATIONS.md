# Metadata Relationship Building

If you passed in `true` as the 4th argument when building your client project object, some metadata 
objects will be built with relationships to other metadata objects.

### Example:

```php
$project = new REDCapProject(
    'https://redcap.mygreatinstitution.edu/api/',
    'mygreattoken',
    'my-cache-dir',
    true);

$metadata = $project->getMetadata();
```

The above, with a 4th argument of `true`, has the net effect of this:

1. `$project->getExportFieldNames();`
2. `$project->getInstruments();`
3. `$project->getMetadata();`

This will query for and parse metadata regarding [Export Field Names](EXPORTFIELDNAMES.md) and
[Instruments (Forms)](INSTRUMENT.md), then create associations between them and the
[Metadata](METADATA.md) response.

This allows you to do do the following:

```php
foreach($metadata as $metadataItem)
{
    /** @var \DCarbone\AmberHat\Metadata\MetadataItemInterface $metadataItem */
    
    echo 'Instrument Label: '.$metadataItem->getInstrumentItem()->getInstrumentLabel()."\n";
    // Note: Instrument Label is not something present within the typical Metadata response
    
    echo "Export Field Name(s):\n\n";

    // Export Field Name is used in FLAT-type record export responses, as well as
    // for importing records
    // The choice value is the ID of the choice.
    foreach($metadataItem->getExportFieldNameItems() as $exportFieldName)
    {
        /** @var \DCarbone\AmberHat\ExportFieldName\ExportFieldNameItemInterface $exportFieldName */
        echo 'Export Field Name: '.$exportFieldName->getExportFieldName()."\n";
        echo 'Choice Value: '.$exportFieldName->getChoiceValue()."\n";
    }
    
    echo sprintf("\n%s\n", str_repeat('-', 50));
}
```

## Efficiency

To prevent ALL metadata api calls being executed every time you execute any of the "get metadata"
methods, only certain metadata items create relationships.  Here is a complete list:

Method | Response | Populated Relationships
------ | -------- | -----------------------
getArms | [Arms](ARMS.md) | None
getEvents | [Events](EVENTS.md) | Events \*-1 Arm
getMetadata | [Metadata](METADATA.md) | Metadata 1-\* Export Field Names<br> Metadata \*-1 Instrument
getInstruments | [Instruments](INSTRUMENT.md) | None
getExportFieldNames | [Export Field Names](EXPORTFIELDNAMES.md) | None
getFormEventMapping | [Form Event Mapping](FORMEVENTS.md) | Form Event Mapping \*-1 Arms<br> Form Event Mapping \*-1 Events<br> Form Event Mapping \*-1 Instrument
getInformation | [Information](INFORMATION.md) | None
getUsers | [Users](USER.md) | None
