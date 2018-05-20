<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason;

use Stratadox\Specification\Contract\Specifies;
use Stratadox\Specification\Specification;

final class AreFeatures extends Specification
{
    public static function ofTheProduct(): Specifies
    {
        return new self;
    }

    public function isSatisfiedBy($object): bool
    {
        return $object instanceof Feature;
    }
}
