<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use function count;
use Stratadox\Hydration\Mapper\Mapper;
use Stratadox\HydrationMapper\InvalidMapperConfiguration;
use Stratadox\Hydrator\ArrayHydrator;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\Hydrator\VariadicConstructor;
use Stratadox\Instantiator\CannotInstantiateThis;

final class Load implements DefinesSingleClassMapping
{
    private $label;
    private $ownId = [];
    private $identityColumnsFor = [];
    private $class;
    private $properties = [];
    private $relation = [];

    private function __construct(string $label)
    {
        $this->label = $label;
        $this->ownId = ['id'];
        $this->identityColumnsFor[$label] = [$label . '_id'];
    }

    public static function each(string $label): DefinesSingleClassMapping
    {
        return new self($label);
    }

    /** @inheritdoc */
    public function by(string ...$columns): DefinesSingleClassMapping
    {
        $label = $this->label;
        $new = clone $this;
        $new->ownId = $columns;
        $new->identityColumnsFor[$label] = $this->mapThe($columns, $label . '_');
        return $new;
    }

    /** @inheritdoc */
    public function as(
        string $class,
        array $properties = []
    ): DefinesSingleClassMapping {
        $new = clone $this;
        $new->class = $class;
        $new->properties = $properties;
        return $new;
    }

    /** @inheritdoc */
    public function havingOne(
        string $property,
        string $label
    ): DefinesObjectMapping {
        $new = clone $this;
        $new->relation[$label] = HasOne::in($property);
        return $new;
    }

    /** @inheritdoc */
    public function havingMany(
        string $property,
        string $label,
        string $collectionClass = null
    ): DefinesObjectMapping {
        $hydrator = $collectionClass ?
            VariadicConstructor::forThe($collectionClass) :
            ArrayHydrator::create();
        $new = clone $this;
        $new->relation[$label] = HasMany::in($property, $hydrator);
        return $new;
    }

    /** @inheritdoc */
    public function identifying(
        string $label,
        string ...$columns
    ): DefinesObjectMapping {
        $new = clone $this;
        $new->identityColumnsFor[$label] = $columns;
        return $new;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function identityColumns(): array
    {
        return $this->identityColumnsFor[$this->label];
    }

    /** @inheritdoc */
    public function objects(): MakesObjects
    {
        try {
            $hydrator = $this->hydratorFor($this->class, $this->properties);
        } catch (CannotInstantiateThis|InvalidMapperConfiguration $exception) {
            throw CannotProduceObjects::encountered($exception, $this->label);
        }
        return Objects::producedByThis(
            $hydrator,
            Prefixed::with($this->label),
            Identified::by(...$this->ownId)
        );
    }

    /** @inheritdoc */
    public function wiring(): WiresObjects
    {
        $ownLabel = $this->label;
        $ownId = $this->identityColumnsFor[$ownLabel];
        $wires = [];
        foreach ($this->relation as $otherLabel => $connectThem) {
            $this->mustKnowTheIdentityColumnsFor($otherLabel);
            $otherId = $this->identityColumnsFor[$otherLabel];
            $wires[] = Wire::it(
                From::the($ownLabel, Identified::by(...$ownId)),
                To::the($otherLabel, Identified::by(...$otherId)),
                $connectThem
            );
        }
        if (count($wires) === 1) {
            return $wires[0];
        }
        return Wired::together(...$wires);
    }

    /**
     * Prepends the identifying columns with a label.
     *
     * @param array $columns
     * @return array
     */
    private function mapThe(array $columns, string $prefix): array
    {
        return array_map(function(string $column) use ($prefix): string {
            return $prefix . $column;
        }, $columns);
    }

    /**
     * Produces a hydrator to prepare the objects.
     *
     * @param string $class
     * @param array  $properties
     *
     * @return Hydrates
     * @throws CannotInstantiateThis
     * @throws InvalidMapperConfiguration
     */
    private function hydratorFor(string $class, array $properties): Hydrates
    {
        if (empty($properties)) {
            return SimpleHydrator::forThe($class);
        }
        $mapper = Mapper::forThe($class);
        foreach ($properties as $name => $instruction) {
            $mapper = $mapper->property($name, $instruction);
        }
        return $mapper->finish();
    }

    /**
     * Checks that the columns that identify the label are known.
     *
     * @param string $label           The label we need to identify.
     * @throws CannotMakeTableMapping When the label cannot be identified.
     */
    private function mustKnowTheIdentityColumnsFor(string $label): void
    {
        if (!isset($this->identityColumnsFor[$label])) {
            throw CannotMakeMapping::missingTheIdentityColumns($label, $this->label);
        }
    }
}
