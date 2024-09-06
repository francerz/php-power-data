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

### Method `groupBy($columns)`

The `groupBy` method allows you to group records from an indexed dataset based
on one or more column values. The grouping is performed incrementally, meaning
that the method applies filters progressively on the existing index to avoid
unnecesary memory and processing overhead. This ensures that the performance
remains efficient, even with large datasets.

#### Parameters:
- `$columns` (string|array):
  The name of a single column (string) or an array of column names (array) by
  which the data should be grouped. The method will return a nested grouping if
  multiple columns are provided.

#### Returns:
- **array**:
  Returns a nested associative array, where each key corresponds to the unique
  values in the specified columns. The innermost arrays contain the records that
  match teh specific grouping of column values.

#### How it works:
The methods does not create a new index fore each group. Instead, progressively
filters the data of each column's unique values, grouping the results step by
step. This approach reduces memory consumption and processing time by avoiding
redundant operations on the dataset.

#### Example Usage:
```php
$index = new Index($data, ['country', 'city']);

// Group by a single column
$groupedByCity = $index->groupBy('city');

// Group by multiple columns
$groupedByCountryAndCity = $index->groupBy(['country', 'city']);

print_r($groupedByCity);
```

## Aggregations

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

## Arrays

```php
Arrays::hasNumericKeys(array $array)
Arrays::hasStringKeys(array $array)
Arrays::findKeys(array $array, string $pattern)
Arrays::remove(array &$array, $value)
Arrays::filter($array, $callback = null, $flag = 0)
Arrays::intersect(array $array1, array $array2, ...$_)
```
