<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use function array_merge;
use function is_null;
use Stratadox\Hydration\Mapper\Mapper;
use Stratadox\Hydrator\ArrayHydrator;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\Hydrator\VariadicConstructor;

final class InCase
{
    private $trigger;
    private $class;
    private $identityColumnsFor = [];
    private $properties = [];
    private $relation = [];
    private $label;

    private function __construct(string $trigger)
    {
        $this->trigger = $trigger;
    }

    public static function of(string $trigger): self
    {
        return new self($trigger);
    }

    public function as(
        string $class,
        array $properties = []
    ): self {
        $new = clone $this;
        $new->class = $class;
        $new->properties = $properties;
        return $new;
    }

    public function with(array $properties): self
    {
        $new = clone $this;
        $new->properties = array_merge($this->properties, $properties);
        return $new;
    }

    public function havingOne(string $property, string $label = null): self
    {
        $new = clone $this;
        $new->relation[$label ?: $property] = HasOne::in($property);
        return $new;
    }

    public function havingMany(
        string $property,
        string $label,
        string $collectionClass = null
    ): self {
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
    ): self {
        $new = clone $this;
        $new->identityColumnsFor[$label] = $columns;
        return $new;
    }

    public function labeled(string $label): self
    {
        $new = clone $this;
        $new->label = $label;
        return $new;
    }

    public function decisionTrigger(): string
    {
        return $this->trigger;
    }

    public function hydrator(): Hydrates
    {
        if (empty($this->properties)) {
            return SimpleHydrator::forThe($this->class);
        }
        $mapper = Mapper::forThe($this->class);
        foreach ($this->properties as $name => $instruction) {
            $mapper = $mapper->property($name, $instruction);
        }
        return $mapper->finish();
    }

    /** @throws CannotMakeMapping */
    public function wiring(): WiresObjects
    {
        $ownLabel = $this->label;
        if (is_null($ownLabel)) {
            throw CannotMakeMapping::missingTheLabelFor($this->trigger);
        }
        $ownId = $this->identityColumnsFor[$ownLabel];
        $wires = [];
        foreach ($this->relation as $otherLabel => $connectThem) {
            $this->mustKnowTheIdentityColumnsFor($otherLabel);
            $otherId = $this->identityColumnsFor[$otherLabel];
            $wires[] = Wire::it(
                From::onlyThe($this->class, $ownLabel, Identified::by(...$ownId)),
                To::the($otherLabel, Identified::by(...$otherId)),
                $connectThem
            );
        }
        if (count($wires) === 1) {
            return $wires[0];
        }
        return Wired::together(...$wires);
    }

    /** @throws CannotMakeMapping */
    private function mustKnowTheIdentityColumnsFor(string $label): void
    {
        if (!isset($this->identityColumnsFor[$label])) {
            throw CannotMakeMapping::missingTheIdentityColumns($label, $this->label);
        }
    }
}
