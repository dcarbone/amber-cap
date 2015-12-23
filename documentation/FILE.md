# Record File Export

To export a file, you must FIRST have [exported the record](RECORDS.md) with the associated File
field containing the file you wish to export.

## Basics

Once you have retrieved the record field you wish to export the file for, execute
```php
$file = $project->downloadFile($field, __DIR__);
```

The first argument is required and must be an instance of
[RecordFieldInterface](../src/Record/RecordFieldInterface.php), representing the field you wish
to export a file for.

The second argument is optional, and specifies the directory you wish for the file to be downloaded to.
If you do not pass anything into this value, it will be stored in the location you specified as
your temp directory when constructing the client.

The response will EITHER be a string to the location of the file in the event of a parsing error,
OR an instance of [RecordFieldFileInterface](../src/Record/RecordFieldFileInterface.php).

### Example:

```php

$recordParser = $project->getRecords($formName, array(), array(), $metadata);

while ($field = $recordParser->read())
{
    if (($metadata = $field->getMetadataItem()) && $metadata->getFieldType() === 'file')
        {
        echo <<<STRING
Record ID: {$field->recordID}
Field Name: {$field->fieldName}
Field Value: {$field->fieldValue}

STRING;

        $file = $project->downloadFile($field, __DIR__);

        echo <<<STRING
Base File Name: {$file->basename}
File Path: {$file->filePath}
File Type: {$file->fileType}
File Size: {$file->fileSize}

STRING;
    }
}
```