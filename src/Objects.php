<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use Stratadox\Hydrator\CouldNotHydrate;
use Stratadox\Hydrator\Hydrates;

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
     * @return Objects                                  The object extractor.
     */
    public static function producedByThis(
        Hydrates $theObjects,
        FiltersTheArray $toTheRelevantDataOnly,
        IdentifiesEntities $forIndexation
    ): self {
        return new self($theObjects, $toTheRelevantDataOnly, $forIndexation);
    }

    /** @inheritdoc */
    public function from(array $input): array
    {
        $data = $this->relevantData->only($input);
        $label = $this->relevantData->label();
        $objects = [];
        foreach ($data as $row) {
            $id = $this->identifier->for($row);
            if (!isset($objects[$id])) {
                try {
                    $objects[$id] = $this->hydrate->fromArray($row);
                } catch (CouldNotHydrate $exception) {
                    throw UnmappableRow::encountered($exception, $label, $row);
                }
            }
        }
        return [$label => $objects];
    }
}
