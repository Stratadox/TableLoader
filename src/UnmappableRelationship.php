<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use RuntimeException;
use function sprintf as withMessage;
use Throwable;

/**
 * Notifies the client code that the relationship could not be mapped.
 *
 * @author Stratadox
 */
final class UnmappableRelationship
    extends RuntimeException
    implements CannotLoadTable
{
    /**
     * Produces an exception for when the relationship could not be mapped.
     *
     * @param Throwable $exception The exception that was encountered.
     * @param string    $property  The property of the relationship.
     * @param string    $owner     The label of the owning side.
     * @param string    $id        The id of the owning side.
     * @return CannotLoadTable     The exception to throw.
     */
    public static function encountered(
        Throwable $exception,
        string $property,
        string $owner,
        string $id
    ): CannotLoadTable {
        return new UnmappableRelationship(withMessage(
            'Could not map the `%s` in the `%s` `%s`: %s',
            $property,
            $owner,
            $id,
            $exception->getMessage()
        ), 0, $exception);
    }
}
