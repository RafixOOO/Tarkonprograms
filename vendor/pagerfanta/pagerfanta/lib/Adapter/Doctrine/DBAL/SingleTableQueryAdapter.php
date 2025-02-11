<?php declare(strict_types=1);

namespace Pagerfanta\Doctrine\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Exception\InvalidArgumentException;

/**
 * Extended Doctrine DBAL adapter which assists in building the count query modifier for a SELECT query on a single table.
 *
 * @template T
 *
 * @extends QueryAdapter<T>
 */
class SingleTableQueryAdapter extends QueryAdapter
{
    /**
     * @param string $countField Primary key for the table in query, used in the count expression. Must include table alias.
     *
     * @throws InvalidArgumentException if the count field does not have a table alias
     */
    public function __construct(QueryBuilder $queryBuilder, string $countField)
    {
        parent::__construct($queryBuilder, $this->createCountQueryModifier($countField));
    }

    private function createCountQueryModifier(string $countField): \Closure
    {
        $select = $this->createSelectForCountField($countField);

        return static function (QueryBuilder $queryBuilder) use ($select): QueryBuilder {
            $queryBuilder->select($select);

            /** @phpstan-ignore-next-line function.alreadyNarrowedType */
            if (method_exists($queryBuilder, 'resetOrderBy')) {
                $queryBuilder->resetOrderBy();
            } else {
                $queryBuilder->resetQueryPart('orderBy');
            }

            $queryBuilder->setMaxResults(1);

            return $queryBuilder;
        };
    }

    /**
     * @throws InvalidArgumentException if the count field does not have a table alias
     */
    private function createSelectForCountField(string $countField): string
    {
        if ($this->countFieldHasNoAlias($countField)) {
            throw new InvalidArgumentException('The $countField must contain a table alias in the string.');
        }

        return \sprintf('COUNT(DISTINCT %s) AS total_results', $countField);
    }

    private function countFieldHasNoAlias(string $countField): bool
    {
        return !str_contains($countField, '.');
    }
}
