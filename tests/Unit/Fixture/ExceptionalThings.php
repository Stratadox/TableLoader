<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

use BadMethodCallException;

final class ExceptionalThings extends Things
{
    public function __construct(Thing ...$things)
    {
        parent::__construct(...$things);
        throw new BadMethodCallException('Original exception message here.');
    }
}
