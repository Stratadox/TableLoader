<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use InvalidArgumentException;
use function json_encode as encodeAsJson;
use function sprintf as withMessage;

/**
 * Notifies the client code that a required identifier is missing.
 *
 * @author Stratadox
 */
final class MissingIdentifyingColumn
    extends InvalidArgumentException
    implements CannotIdentifyEntity
{
    /**
     * Produces an exception for when the input data is missing a required
     * identifier column.
     *
     * @param array  $inputData     The data that lacks an identifier.
     * @param string $identifier    The identifier that is missing.
     * @return CannotIdentifyEntity The exception to throw.
     */
    public static function inThe(
        array $inputData,
        string $identifier
    ): CannotIdentifyEntity {
        return new MissingIdentifyingColumn(withMessage(
            'Missing the identifying column `%s` in the input data: %s',
            $identifier,
            encodeAsJson($inputData)
        ));
    }
}
