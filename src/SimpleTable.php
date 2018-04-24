<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use Stratadox\Hydrator\CouldNotHydrate;
use Stratadox\Hydrator\Hydrates;

/**
 * Converts a table into objects.
 *
 * @author Stratadox
 */
final class SimpleTable implements LoadsTable
{
    private $label;
    private $make;
    private $identity;

    private function __construct(
        string $label,
        Hydrates $hydrator,
        IdentifiesEntities $identity
    ) {
        $this->label = $label;
        $this->make = $hydrator;
        $this->identity = $identity;
    }

    /**
     * Makes a new simple table converter.
     *
     * @param string             $label    The label to apply.
     * @param Hydrates           $hydrator The hydrator that produces the objects.
     * @param IdentifiesEntities $identity The row identification mechanism.
     *
     * @return SimpleTable                 The simple table converter.
     */
    public static function converter(
        string $label,
        Hydrates $hydrator,
        IdentifiesEntities $identity
    ): self {
        return new self($label, $hydrator, $identity);
    }

    /** @inheritdoc */
    public function from(array $input): array
    {
        $objects = [];
        foreach ($input as $row) {
            $tag = $this->identity->forLoading($row);
            try {
                $objects[$tag] = $this->make->fromArray($row);
            } catch (CouldNotHydrate $exception) {
                throw UnmappableRow::encountered($exception, $this->label, $row);
            }
        }
        return [$this->label => $objects];
    }
}
