<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use InvalidArgumentException;
use function json_encode as encodeAsJson;
use function sprintf as withMessage;
use Throwable;

/**
 * Notifies the client code that the row could not be mapped.
 *
 * @author Stratadox
 */
final class UnmappableRow
    extends InvalidArgumentException
    implements CannotLoadTable
{
    /**
     * Produces an exception for when the row could not be mapped.
     *
     * @param Throwable $exception The exception that was encountered.
     * @param string    $label     The label for the failed entity type.
     * @param array     $input     The input data that could not be mapped.
     * @return CannotLoadTable     The exception to throw.
     */
    public static function encountered(
        Throwable $exception,
        string $label,
        array $input
    ): CannotLoadTable {
        return new UnmappableRow(withMessage(
            'Could not map the `%s` from `%s`: %s',
            $label,
            encodeAsJson($input),
            $exception->getMessage()
        ), 0, $exception);
    }
}
