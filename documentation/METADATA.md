# Metadata Data Export

## Method
```php
$metadata = $project->getMetadata();
```

## Response

Response will be of type [MetadataCollection](../src/Metadata/MetadataCollection.php), containing
1+ [MetadataItemInterface](../src/Metadata/MetadataItemInterface.php) objects.

## Example Usage

```php
echo '<pre>';
/** @var \DCarbone\AmberHat\Metadata\MetadataItemInterface $metadataItem **/
foreach($project->getMetadata() as $metadataItem)
{
    echo <<<STRING
Field Name: {$metadataItem['field_name']}
Form Name: {$metadataItem['form_name']}
Section Header: {$metadataItem['section_header']}
Field Type: {$metadataItem['field_type']}
Select Choices or Calculations: {$metadataItem['select_choices_or_calculations']}
Field Note: {$metadataItem['field_note']}
Text Validation Type Or Show Slider Number: {$metadataItem['text_validation_type_or_show_slider_number']}
Text Validation Min: {$metadataItem['text_validation_min']}
Text Validation Max: {$metadataItem['text_validation_max']}
Identifier: {$metadataItem['identifier']}
Branching Logic: {$metadataItem['branching_logic']}
Required Field: {$metadataItem['required_field']}
Custom Alignment: {$metadataItem['custom_alignment']}
Question Number: {$metadataItem['question_number']}
Matrix Group Name: {$metadataItem['matrix_group_name']}
Matrix Ranking: {$metadataItem['matrix_ranking']}
Field Annotation: {$metadataItem['field_annotation']}
STRING;
    echo sprintf("\n%s\n\n", str_repeat('-', 50));
}
echo '</pre>';
```

## Dates

In REDCap, fields which contain date or datetime values come back as type "text" with a varying
value present in the "text_validation_type_or_show_slider_number" metadata property.

To help with consuming date fields, every Metadata item have the following methods:

```php
    /**
     * @return bool
     */
    public function isDateTimeField();

    /**
     * @param string $fieldValue
     * @return string
     */
    public function getDateTimeFormatString($fieldValue);
```

The first will attempt to determine if the field was stored as a date-time field.
*NOTE:* This relies on the value of the aforementioned "text_validation_type_or_show_slider_number".
It is entirely possible for a date-like value to be stored in a plain text field, and in those instances 
this method will return FALSE.

The 2nd will attempt to return a valid PHP [date](http://php.net/manual/en/function.date.php) format
string that can be used to create a [DateTime](http://php.net/manual/en/class.datetime.php) instance
or used with one of PHP's many [Date/Time](http://php.net/manual/en/ref.datetime.php) functions.

## Field Choices

For fields of type:

- dropdown
- radio
- yesno
- truefalse
- checkbox

All available choices are pre-defined and are available as part of the metadata response.

To see a list of available choices for a given metadata item, use the following method:

```php
    /**
     * @return array
     */
    public function getFieldChoiceArray();
```

This method will return an associative array with the key being the identifier of the choice in
REDCap and the value being the display value of the choice.

## Checkbox Field Choice Values

Once you have executed a [Record](RECORDS.md) export and have iterated through to a checkbox field,
you are only made aware of the identifier of the choice, not the value itself.  To that end, I
created the below method:

```php
    /**
     * @param string $exportFieldName
     * @return null|string
     */
    public function getChoiceValueByExportFieldName($exportFieldName);
```

By passing in the exported field name of the chosen checkbox item, this method will return the
actual VALUE associated with that choice.