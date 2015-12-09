# Event Data Export

Once you have built your client, you may request Event data for a project that uses the
event concept using the following method:

```php
$eventsCollection = $client->getEvents();
```

You may optionally pass in an array of known Event Numbers if you only wish to receive data
about a few specific ones.

The response comes in the form of a [EventsCollection](../src/Event/EventsCollection.php) object, with
each object in the collection implementing [EventItemInterface](../src/Event/EventItemInterface.php).

Ex:
```php
echo '<pre>';
/** @var \DCarbone\AmberHat\Event\EventItemInterface $eventItem **/
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