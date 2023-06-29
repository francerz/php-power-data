PHP Power Data
=======================================

Install library with composer

```
composer require francerz/php-power-data
```

### Aggregations

`Aggregations::concat(array $values, $separator = '')`
`Aggregations::count(array $values, bool $ignoreNulls = false)`
`Aggregations::findPercentile(array $values, float $value, int $flags = self::PERCENTILE_FLAGS_MIDDLE)`
`Aggregations::first(array $values)`
`Aggregations::frequencies(array $values)`
`Aggregations::last(array $values)`
`Aggregations::max(array $values)`
`Aggregations::mean(array $values, bool $ignoreNulls = false)`
`Aggregations::median(array $values)`
`Aggregations::min(array $values)`
`Aggregations::mode(array $values, bool $strict = false)`
`Aggregations::percentile(array $values, float $percentile, int $flags = self::PERCENTILE_FLAGS_MIDDLE)`
`Aggregations::product(array $values, bool $ignoreEmpty = false)`
`Aggregations::sum(array $values)`

### Arrays

`Arrays::hasNumericKeys(array $array)`
`Arrays::hasStringKeys(array $array)`
`Arrays::findKeys(array $array, string $pattern)`
`Arrays::remove(array &$array, $value)`
`Arrays::filter($array, $callback = null, $flag = 0)`
`Arrays::intersect(array $array1, array $array2, ...$_)`
