<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use Stratadox\Hydrator\Hydrates;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\TableLoader\ContainsResultingObjects as Objects;
use Throwable;

/**
 * Converts a table into objects.
 *
 * @author Stratadox
 */
final class SimpleTable implements LoadsTables
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
     * @return LoadsTables                 The simple table converter.
     */
    public static function converter(
        string $label,
        Hydrates $hydrator,
        IdentifiesEntities $identity
    ): LoadsTables {
        return new self($label, $hydrator, $identity);
    }

    /** @inheritdoc */
    public function from(array $input, Map $map = null): Objects
    {
        $result = Result::fromArray([], $map ?: IdentityMap::startEmpty());
        foreach ($input as $row) {
            try {
                $result = $this->loadInto($result, $row);
            } catch (Throwable $exception) {
                throw UnmappableRow::encountered($exception, $this->label, $row);
            }
        }
        return $result;
    }

    /** @throws Throwable */
    private function loadInto(Objects $result, array $row): Objects
    {
        $class = $this->make->classFor($row);
        $id = $this->identity->forIdentityMap($row);
        if ($result->has($class, $id)) {
            return $result->include($this->label, $id, $result->get($class, $id));
        }
        return $result->add(
            $this->label,
            $this->identity->forLoading($row),
            $id,
            $this->make->fromArray($row)
        );
    }
}
