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
    /**
     * Produces an exception for when a relationship cannot be wired together
     * because the identity columns for the related label are unknown to the
     * builder.
     *
     * This can happen when the object mapping is not informed about which
     * columns for a related object. By default, the builder for the Joined (1)
     * table loader will automatically set the `identifying`(2) columns. When
     * not using the builder, the object mapping definitions(3) need to be given
     * this information manually.
     *
     * (1) @see Joined
     * (2) @see DefinesObjectMapping::identifying
     * (3) @see DefinesObjectMapping
     *
     * @param string $theirLabel
     * @param string $ourLabel
     * @return CannotMakeTableMapping
     */
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

    /**
     * Produces an exception for when a concrete class decision cannot be
     * produced due to a missing label.
     *
     * This can happen when the trigger(1) is not informed about the label it's
     * attached to. By default, the label is assigned automatically(2).
     *
     * (1) @see InCase
     * (2) @see Decide::prepareChoices
     *
     * @param string $choiceTrigger
     * @return CannotMakeTableMapping
     */
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
