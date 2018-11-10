<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Builder;

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
use Stratadox\TableLoader\Loader\From;
use Stratadox\TableLoader\Loader\HasMany;
use Stratadox\TableLoader\Loader\HasOne;
use Stratadox\TableLoader\Loader\Identified;
use Stratadox\TableLoader\Loader\MakesConnections;
use Stratadox\TableLoader\Loader\MakesObjects;
use Stratadox\TableLoader\Loader\Objects;
use Stratadox\TableLoader\Loader\Prefixed;
use Stratadox\TableLoader\Loader\To;
use Stratadox\TableLoader\Loader\Wire;
use Stratadox\TableLoader\Loader\Wired;
use Stratadox\TableLoader\Loader\WiresObjects;

final class Decide implements DefinesMultipleClassMapping
{
    private $label;
    private $ownId;
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

    public static function which(string $label): DefinesMultipleClassMapping
    {
        return new self($label);
    }

    /** @inheritdoc */
    public function basedOn(
        string $key,
        LoadsWhenTriggered ...$choices
    ): DefinesMultipleClassMapping {
        $new = clone $this;
        $new->decisionKey = $key;
        $new->choices = $choices;
        return $new;
    }

    /** @inheritdoc */
    public function by(string ...$columns): DefinesMultipleClassMapping
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
    public function with(array $properties): DefinesMultipleClassMapping
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
        // @todo try/catch
        return Objects::producedByThis(
            $this->hydrator(),
            Prefixed::with($this->label),
            Identified::by(...$this->ownId)->andForLoading($this->decisionKey)
        );
    }

    /** @inheritdoc */
    public function wiring(): WiresObjects
    {
        $wires = $this->addChoiceWiring(
            $this->prepareChoices(
                $this->choices,
                $this->identityColumnsFor
            ),
            $this->ownWiring(
                $this->label,
                $this->identityColumnsFor,
                $this->relation
            )
        );
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

    /**
     * @param string             $ownLabel
     * @param string[][]         $identityFor
     * @param MakesConnections[] $relations
     * @return WiresObjects[]
     */
    private function ownWiring(string $ownLabel, array $identityFor, array $relations): array
    {
        $ownId = $identityFor[$ownLabel];
        $wires = [];
        foreach ($relations as $otherLabel => $connectThem) {
            //@todo $this->mustKnowTheIdentityColumnsFor($otherLabel);
            $otherId = $identityFor[$otherLabel];
            $wires[] = Wire::it(
                From::the($ownLabel, Identified::by(...$ownId)),
                To::the($otherLabel, Identified::by(...$otherId)),
                $connectThem
            );
        }
        return $wires;
    }

    /**
     * @param LoadsWhenTriggered[] $originalChoices
     * @param string[][]           $identityColumns
     * @return LoadsWhenTriggered[]
     */
    private function prepareChoices(array $originalChoices, array $identityColumns): array
    {
        $choices = [];
        foreach ($originalChoices as $choice) {
            foreach ($identityColumns as $label => $columns) {
                $choice = $choice->identifying($label, ...$columns);
            }
            $choices[] = $choice->labeled($this->label);
        }
        return $choices;
    }

    /**
     * @param LoadsWhenTriggered[] $choices
     * @param WiresObjects[]       $wires
     * @return WiresObjects[]
     * @throws CannotMakeTableMapping
     */
    private function addChoiceWiring(array $choices, array $wires): array
    {
        foreach ($choices as $choice) {
            $wiring = $choice->wiring();
            if ($wiring instanceof Countable && !count($wiring)) {
                continue;
            }
            $wires[] = $wiring;
        }
        return $wires;
    }
}
