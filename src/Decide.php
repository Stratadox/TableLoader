<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use function array_unshift;
use function assert;
use function count;
use Countable;
use Stratadox\HydrationMapper\InvalidMapperConfiguration;
use Stratadox\Hydrator\ArrayHydrator;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\OneOfTheseHydrators;
use Stratadox\Hydrator\VariadicConstructor;
use Stratadox\Instantiator\CannotInstantiateThis;

final class Decide implements DefinesJoinedClassMapping
{
    private $label;
    private $ownId = [];
    private $identityColumnsFor;
    private $decisionKey;
    /** @var LoadsWhenTriggered[] */
    private $choices = [];
    /** @var MakesConnections[] */
    private $relation = [];

    private function __construct(string $label)
    {
        $this->label = $label;
        $this->ownId = ['id'];
        $this->identityColumnsFor[$label] = [$label . '_type', $label . '_id'];
    }

    public static function which(string $label): self
    {
        return new self($label);
    }

    /** @inheritdoc */
    public function basedOn(
        string $key,
        LoadsWhenTriggered ...$choices
    ): DefinesJoinedClassMapping {
        $new = clone $this;
        $new->decisionKey = $key;
        $new->choices = $choices;
        return $new;
    }

    /** @inheritdoc */
    public function by(string ...$columns): DefinesJoinedClassMapping
    {
        $label = $this->label;
        $new = clone $this;
        $new->ownId = $columns;
        array_unshift($columns, $this->decisionKey);
        $new->identityColumnsFor[$label] = $this->mapThe($columns, $label . '_');
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
    public function with(array $properties): DefinesJoinedClassMapping
    {
        $new = clone $this;
        foreach ($this->choices as $i => $choice) {
            $new->choices[$i] = $choice->with($properties);
        }
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

    /** @inheritdoc */
    public function label(): string
    {
        return $this->label;
    }

    /** @inheritdoc */
    public function identityColumns(): array
    {
        return $this->identityColumnsFor[$this->label];
    }

    /** @inheritdoc */
    public function objects(): MakesObjects
    {
        assert(isset($this->decisionKey));
        return Objects::producedByThis(
            $this->hydrator(),
            Prefixed::with($this->label),
            Identified::by(...$this->ownId)->andForLoading($this->decisionKey)
        );
    }

    /** @inheritdoc */
    public function wiring(): WiresObjects
    {
        // @todo extract methods
        $ownLabel = $this->label;
        $ownId = $this->identityColumnsFor[$ownLabel];
        $wires = [];
        foreach ($this->relation as $otherLabel => $connectThem) {
            //@todo $this->mustKnowTheIdentityColumnsFor($otherLabel);
            $otherId = $this->identityColumnsFor[$otherLabel];
            $wires[] = Wire::it(
                From::the($ownLabel, Identified::by(...$ownId)),
                To::the($otherLabel, Identified::by(...$otherId)),
                $connectThem
            );
        }
        /** @var LoadsWhenTriggered[] $choices */
        $choices = [];
        foreach ($this->choices as $choice) {
            foreach ($this->identityColumnsFor as $label => $columns) {
                $choice = $choice->identifying($label, ...$columns);
            }
            $choices[] = $choice->labeled($this->label);

        }
        foreach ($choices as $choice) {
            $wiring = $choice->wiring();
            if ($wiring instanceof Countable && count($wiring) === 0) {
                continue;
            }
            $wires[] = $wiring;
        }
        if (count($wires) === 1) {
            return $wires[0];
        }
        return Wired::together(...$wires);
    }

    /**
     * @return Hydrates
     * @throws InvalidMapperConfiguration
     * @throws CannotInstantiateThis
     */
    private function hydrator(): Hydrates
    {
        $choices = [];
        foreach ($this->choices as $choice) {
            $choices[$choice->decisionTrigger()] = $choice->hydrator();
        }
        return OneOfTheseHydrators::decideBasedOnThe(
            $this->decisionKey,
            $choices
        );
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
}
