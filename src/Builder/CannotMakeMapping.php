<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Builder;

use function sprintf as withMessage;
use UnexpectedValueException as UnexpectedValue;

/**
 * Notifies the client code that the table mapping could not be produced.
 *
 * @author Stratadox
 */
final class CannotMakeMapping extends UnexpectedValue implements CannotMakeTableMapping
{
    public static function missingTheIdentityColumns(
        string $theirLabel,
        string $ourLabel
    ): CannotMakeTableMapping {
        return new CannotMakeMapping(withMessage(
            'Cannot make a mapping for the `%s` objects: ' .
            'Cannot resolve the identifying columns for the `%s` relation.',
            $ourLabel,
            $theirLabel
        ));
    }

    public static function missingTheLabelFor(
        string $choiceTrigger
    ): CannotMakeTableMapping {
        return new CannotMakeMapping(withMessage(
            'Cannot make a mapping for the `%s` objects: ' .
            'The label for the source relation was not provided.',
            $choiceTrigger
        ));
    }
}
