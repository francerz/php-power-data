PHP Power Data
=======================================

## Installation

This package can be installed with composer using following command.

```
composer require francerz/php-power-data
```

## Using `Index` class

```php
use Francerz\PowerData\Index;

$index = new Index($data, ['column1', 'column2']);

$col1Values = $index->getColumnValues('column1');
$col2Values = $index->getColumnValues('column2');

foreach ($col1Values as $c1) {
    foreach ($col2Values as $c2) {
        // Retrieves all items that matches $c1 and $c2.
        $items = $index[['column1' => $c1, 'column2' => $c2]];

        $numItems = $index->xCount(['column1' => $c1, 'column2' => $c2]);
        $sumCol3 = $index->sum('column3', ['column1' => $c1, 'column2' => $c2]);
        $first = $index->first(['column1' => $c1, 'column2' => $c2]);
        $last = $index->last(['column1' => $c1, 'column2' => $c2]);
    }
    // Retrieves all items that matches $c1
    $items = $index[['column1' => $c1]];
}
```

### Aggregations

```php
Aggregations::concat(array $values, $separator = '')
Aggregations::count(array $values, bool $ignoreNulls = false)
Aggregations::findPercentile(array $values, float $value, int $flags = self::PERCENTILE_FLAGS_MIDDLE)
Aggregations::first(array $values)
Aggregations::frequencies(array $values)
Aggregations::last(array $values)
Aggregations::max(array $values)
Aggregations::mean(array $values, bool $ignoreNulls = false)
Aggregations::median(array $values)
Aggregations::min(array $values)
Aggregations::mode(array $values, bool $strict = false)
Aggregations::percentile(array $values, float $percentile, int $flags = self::PERCENTILE_FLAGS_MIDDLE)
Aggregations::product(array $values, bool $ignoreEmpty = false)
Aggregations::sum(array $values)
```

### Arrays

```php
Arrays::hasNumericKeys(array $array)
Arrays::hasStringKeys(array $array)
Arrays::findKeys(array $array, string $pattern)
Arrays::remove(array &$array, $value)
Arrays::filter($array, $callback = null, $flag = 0)
Arrays::intersect(array $array1, array $array2, ...$_)
```
