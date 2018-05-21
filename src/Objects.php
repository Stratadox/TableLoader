<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use function is_null;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\Hydrates;
use Stratadox\IdentityMap\AlreadyThere;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Throwable;

/**
 * Makes partially hydrated objects from an input array.
 *
 * @author Stratadox
 */
final class Objects implements MakesObjects
{
    private $hydrate;
    private $relevantData;
    private $identifier;

    private function __construct(
        Hydrates $theObjects,
        FiltersTheArray $toTheRelevantDataOnly,
        IdentifiesEntities $forIndexation
    ) {
        $this->hydrate = $theObjects;
        $this->relevantData = $toTheRelevantDataOnly;
        $this->identifier = $forIndexation;
    }

    /**
     * Makes a new object extractor that produces partially hydrated objects.
     *
     * @param Hydrates           $theObjects            Hydrator for the objects.
     * @param FiltersTheArray    $toTheRelevantDataOnly The filter for the input
     *                                                  array.
     * @param IdentifiesEntities $forIndexation         The row identification
     *                                                  mechanism.
     * @return MakesObjects                             The object extractor.
     */
    public static function producedByThis(
        Hydrates $theObjects,
        FiltersTheArray $toTheRelevantDataOnly,
        IdentifiesEntities $forIndexation
    ): MakesObjects {
        return new self($theObjects, $toTheRelevantDataOnly, $forIndexation);
    }

    /** @inheritdoc */
    public function from(array $input, Map $map): ContainsResultingObjects {
        $data = $this->relevantData->only($input);
        $label = $this->relevantData->label();
        $objects = [];
        foreach ($data as $row) {
            if ($this->identifier->isNullFor($row)) {
                continue;
            }
            $hash = $this->identifier->forLoading($row);
            if (isset($objects[$hash])) {
                continue;
            }
            try {
                $class = $this->hydrate->classFor($row);
                $id = $this->identifier->forIdentityMap($row);
                if ($map->has($class, $id)) {
                    $object = $map->get($class, $id);
                } else {
                    $object = $this->hydrate->fromArray($row);
                    $map = $map->add($id, $object);
                }
                $objects[$hash] = $object;
            } catch (Throwable $exception) {
                throw UnmappableRow::encountered($exception, $label, $row);
            }
        }
        return Result::fromArray([$label => $objects], $map);
    }
}
