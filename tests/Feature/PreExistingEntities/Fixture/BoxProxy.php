<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\PreExistingEntities\Fixture;

use BadMethodCallException;

final class BoxProxy extends Box
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function items(): array
    {
        throw new BadMethodCallException(
            'Proxy behaviour is out of scope for the example.'
        );
    }
}
