<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Loader;

use function array_merge;
use function implode;
use function str_replace as replace;

/**
 * Identifies the entities in the table rows.
 *
 * @author Stratadox
 */
final class Identified implements IdentifiesEntities
{
    private const PROBLEMS = ['\\', ':'];
    private const SOLUTIONS = ['\\\\', '\\:'];

    private $identifyingColumns;
    private $columnsForLoading;

    private function __construct(array $identifyingColumns, array $forLoading)
    {
        $this->identifyingColumns = $identifyingColumns;
        $this->columnsForLoading = array_merge(
            $forLoading,
            $this->identifyingColumns
        );
    }

    /**
     * Creates an object that identifies entities based on these columns.
     *
     * @param string ...$identifyingColumns The columns to use for identification.
     *
     * @return IdentifiesEntities           The identifying object.
     */
    public static function by(string ...$identifyingColumns): IdentifiesEntities
    {
        return new self($identifyingColumns, []);
    }

    /** @inheritdoc */
    public function andForLoading(string ...$columns): IdentifiesEntities
    {
        return new self($this->identifyingColumns, $columns);
    }

    /** @inheritdoc */
    public function forIdentityMap(array $row): string
    {
        return $this->identifierFor($row, $this->identifyingColumns);
    }

    /** @inheritdoc */
    public function forLoading(array $row): string
    {
        return $this->identifierFor($row, $this->columnsForLoading);
    }

    /** @inheritdoc */
    public function isNullFor(array $row): bool
    {
        foreach ($this->columnsForLoading as $column) {
            if (null !== $row[$column]) {
                return false;
            }
        }
        return true;
    }

    /** @throws CannotIdentifyEntity */
    private function identifierFor(array $row, array $identifyingColumns): string
    {
        $id = [];
        foreach ($identifyingColumns as $column) {
            $this->mustHave($row, $column);
            $id[] = replace(self::PROBLEMS, self::SOLUTIONS, $row[$column]);
        }
        return implode(':', $id);
    }

    /** @throws CannotIdentifyEntity */
    private function mustHave(array $row, string $column): void
    {
        if (!isset($row[$column])) {
            throw MissingIdentifyingColumn::inThe($row, $column);
        }
    }
}
