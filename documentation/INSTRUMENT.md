# Instrument Export

## Method
```php
$instruments = $client->getInstruments();
```

## Response Classes

Response will be of type [InstrumentsCollection](../src/Instrument/InstrumentsCollection.php),
containing 1+ [InstrumentItemInterface](../src/Instrument/InstrumentItemInterface.php) objects.

## Example Usage

```php
foreach($client->getInstruments() as $instrument)
{
    echo <<<STRING
Instrument Name: {$instrument->getInstrumentName()}
Label: {$instrument->getInstrumentLabel()}
STRING;

    echo sprintf("\n%s\n", str_repeat('-', 50));
}
</pre>
```