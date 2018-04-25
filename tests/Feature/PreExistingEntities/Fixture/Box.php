<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\PreExistingEntities\Fixture;

use function count;
use Countable;

class Box implements Countable
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

    /** @inheritdoc */
    public function count()
    {
        return count($this->items);
    }
}
