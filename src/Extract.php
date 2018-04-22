<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

/**
 * Makes partially hydrated objects from an input array.
 *
 * @author Stratadox
 */
final class Extract implements MakesObjects
{
    private $objects;

    private function __construct(MakesObjects ...$objects)
    {
        $this->objects = $objects;
    }

    /**
     * Makes a new object extractor that composes more object extractors.
     *
     * @param MakesObjects ...$objects The other object extractors.
     *
     * @return Extract                 The composed extractor.
     */
    public static function these(MakesObjects ...$objects): self
    {
        return new self(...$objects);
    }

    /** @inheritdoc */
    public function from(array $input): array
    {
        $output = [];
        foreach ($this->objects as $objects) {
            $output += $objects->from($input);
        }
        return $output;
    }
}
