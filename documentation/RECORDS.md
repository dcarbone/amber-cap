# Record Data Export

Record Data exporting is a bit more complicated than using the various metadata api's.

## A little explanation
In practice, a single project can easily contain 20,000+ records.  Combine this with there could be
dozens of fields on any given form, and the sheer size of the response can be surprisingly large.

To that end, this lib was developed against a survey containing ~26,000 records at the time of this
documentation to be as fast and lightweight as possible.  To THAT end, the main underlying technology
used is [XMLReader](http://php.net/manual/en/class.xmlreader.php).

## Methods and Return Objects

The method you will use to export Record data for project is below:

```php
    /**
     * @param string $formName
     * @param array $fields
     * @param array $events
     * @param MetadataCollection|null $metadataCollection
     * @return RecordParser
     */
    public function getRecords($formName,
                               array $fields = array(),
                               array $events = array(),
                               MetadataCollection $metadataCollection = null)
```

The only required parameter is ` $formName `, and its value must directly equal to a form
in your REDCap project.

*NOTE:* At the moment, it is only possible to retrieve a single form's records at a time. Multi-form record
retrieval is being looked at for future releases.

The response object is a [RecordParser](../src/Record/RecordParser.php). Once this object
is received, you are able to retrieve data using the `read` method.

## RecordParser Read Modes

The parser supports 2 different operating modes, definable with the method `setMode`
on [RecordParser](../src/Record/RecordParser.php):

1. (Default) Field return mode
2. Record return mode

These are represented as class constants within the [RecordParser](../src/Record/RecordParser.php) class:

1. `MODE_READ_FIELD`
2. `MODE_READ_RECORD`

In Field return mode, the parser will return an object PER FIELD in the response, irrespective of parent
record.  The object will be an instance of [RecordFieldInterface](../src/Record/RecordFieldInterface.php).

In Record return mode, the parser will only return an object once it has looped through and found all fields
that belong to a single Record ID.  The object will be an instance of
[RecordInterface](../src/Record/RecordInterface.php), and it will contain child objects of type
[RecordFieldInterface](../src/Record/RecordFieldInterface.php).

## Sample Workflow

```php
use DCarbone\AmberHat\AmberHatClient;

// Initialize new client
$client = new AmberHatClient(
    'https://redcap.mygreatinstitution.edu/api/',
    'mygreattoken',
    'my-cache-dir'); 

// Get Metadata
$metadata = $client->getMetadata();

// Get a unique array of all form names
$formNames = array();
foreach($metadata as $metadataItem)
{
    /** @var \DCarbone\AmberHat\Metadata\MetadataItemInterface $metadataItem **/
    $formNames[] = $metadataItem['form_name'];
}
$formNames = array_unique($formNames);

// Loop through each form and process records.
echo '<pre>';
foreach($formNames as $formName)
{
    echo sprintf("Form Name: %s\n\nFields:\n", $formName);
    
    // At this point, the client will go out and attempt to fetch form data
    // for the form you specified.  The response will be cached in the Temp directory
    // you specified during client creation.  It will then be looped through using
    // \XMLReader.
    $recordParser = $client->getRecords($formName, array(), array(), $metadata);
    
    // Default is to read and return each field
    while ($field = $recordParser->read())
    {
        echo <<<STRING
Record ID: {$field->recordID}
Form Name: {$field->formName}
REDCap Event Name: {$field->redcapEventName}
Field Name: {$field->fieldName}
Field Value: {$field->fieldValue}
STRING;
        echo "\nFirst Field In Record: ";
        echo $field->firstFieldInRecord ? 'TRUE' : 'FALSE';
        echo "\nLast Field In Record: ";
        echo $field->lastFieldInRecord ? 'TRUE' : 'FALSE';
        echo "\nHas Metadata Item: ";
        echo $field->getMetadataItem() !== null ? 'TRUE' : 'FALSE';
        echo sprintf("\n%s\n", str_repeat('-', 50));
    }
    echo sprintf("\n\n%s\n\n", str_repeat('-', 50));
}
echo '</pre>';
```

*NOTE:* I would not recommend using the above code verbatim, particularly in very large forms
and / or projects with large record counts.  It is merely for demonstration purposes.

## Metadata

In addition to the raw values of the record field, and if you created and passed in a
[MetadataCollection](../src/Metadata/MetadataCollection.php), each
[RecordField](../src/Record/RecordFieldInterface.php) will have a reference to that fields appropriate
[MetadataItem](../src/Metadata/MetadataItemInterface.php).  This can be returned using the method:

```php
    /**
     * @return \DCarbone\AmberHat\Metadata\MetadataItemInterface|null
     */
    public function getMetadataItem();
```

*NOTE:* If you do not pass in a [MetadataCollection](../src/Metadata/MetadataCollection.php),
this method will return null
