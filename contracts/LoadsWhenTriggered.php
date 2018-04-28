<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use Stratadox\HydrationMapper\InvalidMapperConfiguration;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Instantiator\CannotInstantiateThis;

interface LoadsWhenTriggered
{
    public function as(string $class, array $properties = []): self;

    public function with(array $properties): self;

    public function havingOne(
        string $property,
        string $label = null
    ): self;

    public function havingMany(
        string $property,
        string $label,
        string $collectionClass = null
    ): self;

    /** @inheritdoc */
    public function identifying(string $label, string ...$columns): self;

    public function labeled(string $label): self;

    public function decisionTrigger(): string;

    /**
     * @return Hydrates
     * @throws InvalidMapperConfiguration
     * @throws CannotInstantiateThis
     */
    public function hydrator(): Hydrates;

    /** @throws CannotMakeMapping */
    public function wiring(): WiresObjects;
}