<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

/**
 * Locates the target object for the relationship.
 *
 * @author Stratadox
 */
final class To implements KnowsWhereToLookTo
{
    private $who;
    private $identity;

    private function __construct(string $who, IdentifiesEntities $identity)
    {
        $this->who = $who;
        $this->identity = $identity;
    }

    /**
     * Makes a new target locator.
     *
     * @param string             $label    The label of the source objects.
     * @param IdentifiesEntities $identity The mechanism to identify the target
     *                                     entity of the row.
     * @return KnowsWhereToLook
     */
    public static function the(
        string $label,
        IdentifiesEntities $identity
    ): KnowsWhereToLook {
        return new self($label, $identity);
    }

    /** @inheritdoc */
    public function label(): string
    {
        return $this->who;
    }

    /** @inheritdoc */
    public function this(array $relationship): string
    {
        return $this->identity->forLoading($relationship);
    }

    /** @inheritdoc */
    public function ignoreThe(array $row): bool
    {
        return $this->identity->isNullFor($row);
    }
}
