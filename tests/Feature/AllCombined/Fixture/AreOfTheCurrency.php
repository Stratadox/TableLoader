<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

use Stratadox\Specification\Contract\Specifies;
use Stratadox\Specification\Specification;

final class AreOfTheCurrency extends Specification
{
    private $currency;

    public function __construct(string $currency)
    {
        $this->currency = $currency;
    }

    public static function withCode(string $currencyCode): Specifies
    {
        return new self($currencyCode);
    }

    public function isSatisfiedBy($object): bool
    {
        return $object instanceof Monetary
            && $object->currency() === $this->currency;
    }
}
