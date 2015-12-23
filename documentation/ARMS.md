# ARM Data Export

## Method
```php
$arms = $project->getArms();
```

## Response

Response will be of type [ArmsCollection](../src/Arm/ArmsCollection.php), containing 0+
[ArmItemInterface](../src/Arm/ArmItemInterface.php) objects.

## Example Usage

```php
echo '<pre>';
foreach($project->getArms() as $armItem)
{
    echo sprintf("%s: %s\n", $armItem['arm_num'], $armItem['name']);
}
echo '</pre>';
```