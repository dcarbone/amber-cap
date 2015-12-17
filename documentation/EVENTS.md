# Event Data Export

## Method
```php
$events = $client->getEvents();
```

You may optionally pass in an array of known Event Numbers if you only wish to receive data
about a few specific ones.

## Response

Response will be of type [EventsCollection](../src/Event/EventsCollection.php), containing
0+ [EventItemInterface](../src/Event/EventItemInterface.php) objects.

## Example Usage

```php
echo '<pre>';
foreach($client->getEvents() as $eventItem)
{
    echo <<<STRING
Event Name: {$eventItem['event_name']}
Arm Num: {$eventItem['arm_num']}
Day Offset: {$eventItem['day_offset']}
Offset Min: {$eventItem['offset_min']}
Offset Max: {$eventItem['offset_max']}
Unique Event Name: {$eventItem['unique_event_name']}
STRING;
    echo sprintf("\n%s\n\n", str_repeat('-', 50));
}
echo '</pre>';
```