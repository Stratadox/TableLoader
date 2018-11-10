<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Loader;

use function is_null;

/**
 * Locates the source object for the relationship.
 *
 * @author Stratadox
 */
final class From implements KnowsWhereToLookFrom
{
    private $who;
    private $identity;
    private $class;

    private function __construct(string $who, IdentifiesEntities $identity, ?string $class)
    {
        $this->who = $who;
        $this->identity = $identity;
        $this->class = $class;
    }

    /**
     * Makes a new source locator.
     *
     * @param string             $label    The label of the source objects.
     * @param IdentifiesEntities $identity The mechanism to identify the source
     *                                     entity of the row.
     * @return KnowsWhereToLookFrom        The source locator.
     */
    public static function the(
        string $label,
        IdentifiesEntities $identity
    ): KnowsWhereToLookFrom {
        return new self($label, $identity, null);
    }

    /**
     * Makes a new source locator for a concrete class.
     *
     * @param string             $class    The name of the concrete class.
     * @param string             $label    The label of the source objects.
     * @param IdentifiesEntities $identity The mechanism to identify the source
     *                                     entity of the row.
     * @return KnowsWhereToLookFrom        The source locator.
     */
    public static function onlyThe(
        string $class,
        string $label,
        IdentifiesEntities $identity
    ): KnowsWhereToLookFrom {
        return new self($label, $identity, $class);
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
    public function hereToo(object $shouldWeConnectIt): bool
    {
        return is_null($this->class) || $shouldWeConnectIt instanceof $this->class;
    }
}
