<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use function sprintf as withMessage;
use Throwable;
use UnexpectedValueException;

final class CannotProduceObjects
    extends UnexpectedValueException
    implements CannotMakeTableMapping
{
    public static function encountered(Throwable $exception, string $label): self
    {
        return new CannotProduceObjects(withMessage(
            'Cannot produce the `%s` objects: %s',
            $label,
            $exception->getMessage()
        ), 0, $exception);
    }
}
