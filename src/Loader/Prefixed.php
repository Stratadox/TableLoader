<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Loader;

use function array_filter as filtered;
use function array_map as andMapped;
use function strlen as lengthOf;
use function strpos as positionOf;
use function substr as aPortionOf;

/**
 * Limits the data set to the relevant subset.
 *
 * @author Stratadox
 */
final class Prefixed implements FiltersTheArray
{
    private $label;
    private $prefix;
    private $startingAt;

    private function __construct(string $label, string $separator)
    {
        $this->label = $label;
        $this->prefix = $label . $separator;
        $this->startingAt = lengthOf($this->prefix);
    }

    /**
     * Creates a new array limiter that limits input to fields with this prefix.
     *
     * @param string $label     The label to use in the prefix.
     * @param string $separator The separator between the label and the field.
     * @return FiltersTheArray  The prefix limiter.
     */
    public static function with(
        string $label,
        string $separator = '_'
    ): FiltersTheArray {
        return new self($label, $separator);
    }

    /** @inheritdoc */
    public function only(array $input): array
    {
        return filtered(andMapped([$this, 'filterOutIrrelevantData'], $input));
    }

    /** @inheritdoc */
    public function label(): string
    {
        return $this->label;
    }

    private function filterOutIrrelevantData(array $row): array
    {
        $result = [];
        foreach ($row as $column => $value) {
            if ($this->isPrefixed($column)) {
                $result[$this->withoutPrefix($column)] = $value;
            }
        }
        return $result;
    }

    private function isPrefixed(string $field): bool
    {
        return positionOf($field, $this->prefix) === 0;
    }

    private function withoutPrefix(string $field): string
    {
        return aPortionOf($field, $this->startingAt);
    }
}
