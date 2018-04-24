<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\PreExistingEntities\Fixture;

class Box
{
    private $items;

    /**
     * @param Thing[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /** @return Thing[] */
    public function items(): array
    {
        return $this->items;
    }
}
