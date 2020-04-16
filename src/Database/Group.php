<?php

namespace FilterSearch\Database;

use Doctrine\ORM\QueryBuilder;

/**
 * Represents a SQL group by.
 *
 * @package FilterSearch\Database
 */
class Group
{

    private $columns;

    private function __construct($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Creates an group by.
     *
     * @param string ...$columns string The columns to group on
     * @return Group The Group object
     */
    public static function by(string ...$columns): Group
    {
        return new Group($columns);
    }

    /**
     * Calls the groupBy method on the provided
     * query builder.
     *
     * @param QueryBuilder $qb The query builder to do
     * the group by on.
     */
    public function extend(QueryBuilder $qb)
    {
        foreach($this->columns as $group) {
            $qb->groupBy($group);
        }
    }

}
