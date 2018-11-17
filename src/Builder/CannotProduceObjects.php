<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Builder;

use function sprintf as withMessage;
use Throwable;
use UnexpectedValueException as UnexpectedValue;

/**
 * Notifies the client code that producing an object loader was unsuccessful.
 *
 * @author Stratadox
 */
final class CannotProduceObjects extends UnexpectedValue implements CannotMakeTableMapping
{
    /**
     * Produces an exception for when when an exception was encountered while
     * trying to assemble the infrastructure that loads the objects.
     *
     * @param Throwable $exception
     * @param string    $label
     * @return CannotMakeTableMapping
     */
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
