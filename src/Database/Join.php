<?php

namespace FilterSearch\Database;

use Doctrine\ORM\QueryBuilder;

/**
 * Represents a SQL join.
 * Provides two methods to create joins :
 * - Join::inner for inner joins
 * - Join::left for left joins
 * @package App\Form\Search
 */
class Join
{

    private $method;
    private $join;
    private $alias;
    private $condition;

    private function __construct($type, $join, $alias, $condition)
    {
        $this->method = $type;
        $this->join = $join;
        $this->alias = $alias;
        $this->condition = $condition;
    }

    /**
     * Creates an inner join.
     *
     * @param $join string The joined field or class
     * @param string $alias Alias of the joined table
     * @param string $condition Join condition
     * @return Join The Join object
     */
    public static function inner(string $join, string $alias, string $condition = null): Join
    {
        return new Join("innerJoin", $join, $alias, $condition);
    }

    /**
     * Creates an left join.
     *
     * @param $join string The joined field or class
     * @param string $alias Alias of the joined table
     * @param string $condition Join condition
     * @return Join The Join object
     */
    public static function left(string $join, string $alias, string $condition = null): Join
    {
        return new Join("leftJoin", $join, $alias, $condition);
    }

    /**
     * Getter for the join alias.
     *
     * @return string The join alias
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Calls the appropriate join method on the provided
     * query builder with the join, alias and condition
     * this join was created with.
     *
     * @param QueryBuilder $qb The query builder to do
     * the join on.
     */
    public function extend(QueryBuilder $qb)
    {
        $qb->{$this->method}($this->join, $this->alias, "WITH", $this->condition);
    }

}
