<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

/**
 * Locates the source object for the relationship.
 *
 * @author Stratadox
 */
final class From implements KnowsWhereToLook
{
    private $who;
    private $identity;

    private function __construct(string $who, IdentifiesEntities $identity)
    {
        $this->who = $who;
        $this->identity = $identity;
    }

    /**
     * Makes a new source locator.
     *
     * @param string             $label    The label of the source objects.
     * @param IdentifiesEntities $identity The mechanism to identify the source
     *                                     entity of the row.
     *
     * @return From
     */
    public static function the(
        string $label,
        IdentifiesEntities $identity
    ): self {
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
        return $this->identity->for($relationship);
    }
}
