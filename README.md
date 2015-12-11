# amber-hat
A REDCap Client library written in PHP

## Installation

This lib requires the use of [Composer](https://getcomposer.org/).

```json
"require": {
    "dcarbone/amber-hat": "0.2.*"
}
```

## Client Creation

To get started, you will need 3 things:

1. Full URL to your REDCap installation's API endpoint (typically something like "https://redcap.mygreatinstitution.edu/api/")
2. A REDCap API token
3. A Temp directory on the server capable of handling potentially large files

Once you have those, you can immediately start exporting data for the project the token gives you access to:

```php
use DCarbone\AmberHat\AmberHatClient;

$client = new AmberHatClient(
    'https://redcap.mygreatinstitution.edu/api/',
    'mygreattoken',
    'my-cache-dir');  
```

## Available Data Export methods:

This lib does not yet fully support all possible export options from REDCap, but here
is the list so far:

1. [Arms](documentation/ARMS.md)
2. [Events](documentation/EVENTS.md)
3. [Metadata](documentation/METADATA.md)
4. [ExportFieldNames](documentation/EXPORTFIELDNAMES.md)
5. [ProjectInformation](documentation/PROJECTINFORMATION.md)
6. [Records](documentation/RECORDS.md)

### Basic Export Data Object Structure

With the exception of [Records](documentation/RECORDS.md) and
[ProjectInformation](documentation/PROJECTINFORMATION.md), all export methods return a collection
class which extends [AbstractItemCollection](src/AbstractItemCollection.php).  This collection class
implements the following interfaces:

- [ArrayAccess](http://php.net/manual/en/class.arrayaccess.php)
- [Countable](http://php.net/manual/en/class.countable.php)
- [Iterator](http://php.net/manual/en/class.iterator.php)
- [Serializable](http://php.net/manual/en/class.serializable.php)

The objects present in the collection are all classes which implement the 
[ItemInterface](src/ItemInterface.php) interface.  The interface requires concrete classes
to implement the following:

- [ArrayAccess](http://php.net/manual/en/class.arrayaccess.php)
- [Iterator](http://php.net/manual/en/class.iterator.php)
- [Serializable](http://php.net/manual/en/class.serializable.php)
- [JsonSerializable](http://php.net/manual/en/class.jsonserializable.php)

*NOTE*: For PHP 5.3 users, I have created a [JsonSerializableCompatible](src/JsonSerializableCompatible.php)
interface, however you must execute [json_encode](http://php.net/manual/en/function.json-encode.php)
on the result of the implemented method call, rather than on the object itself.

Ex:
```php
$json_string = json_encode($itemObject->jsonSerialize());
```

## Available Data Import methods:

At the moment, no import methods are available.  However, this feature is being worked on
and will hopefully come to fruition soon.
