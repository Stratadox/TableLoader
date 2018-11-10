<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Builder;

use function sprintf as withMessage;
use Throwable;
use UnexpectedValueException as UnexpectedValue;

/**
 * Notifies the client code that producing the objects was unsuccessful.
 *
 * @author Stratadox
 */
final class CannotProduceObjects extends UnexpectedValue implements CannotMakeTableMapping
{
    public static function encountered(
        Throwable $exception,
        string $label
    ): CannotMakeTableMapping {
        return new CannotProduceObjects(withMessage(
            'Cannot produce the `%s` objects: %s',
            $label,
            $exception->getMessage()
        ), 0, $exception);
    }
}
