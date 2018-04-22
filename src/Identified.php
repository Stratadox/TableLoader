<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

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

    private function __construct(string ...$identifyingColumns)
    {
        $this->identifyingColumns = $identifyingColumns;
    }

    /**
     * Creates an object that identifies entities based on these columns.
     *
     * @param string ...$identifyingColumns The columns to use for identification.
     *
     * @return Identified                  The identifying object.
     */
    public static function by(string ...$identifyingColumns): self
    {
        return new self(...$identifyingColumns);
    }

    /** @inheritdoc */
    public function for(array $row): string
    {
        $id = [];
        foreach ($this->identifyingColumns as $column) {
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
