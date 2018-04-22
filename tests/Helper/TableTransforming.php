<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Helper;

use function array_combine;
use function array_shift;

trait TableTransforming
{
    /**
     * Transforms one tabular format into another.
     *
     * @param array $table Visual table.
     * @return array       Rows of maps.
     */
    private function table(array $table): array
    {
        $keys = array_shift($table);
        $result = [];
        foreach ($table as $row) {
            $result[] = array_combine($keys, $row);
        }
        return $result;
    }
}
