<?php

namespace FilterSearch\Database;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;

/**
 * Query parts that will be appended to
 * the query builder.
 *
 * @package FilterSearch\Database
 */
class QueryPart
{

    private $where;
    private $having;
    private $group;
    private $order;
    private $parameters;

    public function __construct()
    {
        $this->where = [];
        $this->having = [];
        $this->group = [];
        $this->order = [];
        $this->parameters = [];
    }

    public function expr(): Expr {
        return new Expr();
    }

    public function where($expr): self {
        $this->where[] = $expr;
        return $this;
    }

    public function having($expr): self {
        $this->having[] = $expr;
        return $this;
    }

    public function groupBy($expr): self {
        $this->group[] = $expr;
        return $this;
    }

    public function orderBy($expr): self {
        $this->order[] = $expr;
        return $this;
    }

    public function setParameter($name, $value): self {
        $this->parameters[$name] = $value;
        return $this;
    }

    public function merge(QueryBuilder $qb, $mode) {
        if($mode == "all") {
            foreach($this->where as $expr) {
                $qb->andWhere($expr);
            }

            foreach($this->having as $expr) {
                $qb->andHaving($expr);
            }
        } else if($mode == "any") {
            foreach($this->where as $expr) {
                $qb->orWhere($expr);
            }

            foreach($this->having as $expr) {
                $qb->orHaving($expr);
            }
        } else if($mode == "none") {
            foreach($this->where as $expr) {
                $qb->andWhere($qb->expr()->not($expr));
            }

            foreach($this->having as $expr) {
                $qb->andHaving($qb->expr()->not($expr));
            }
        } else {
            throw new RuntimeException("Unknown match mode: $mode");
        }

        foreach($this->group as $expr) {
            $qb->addGroupBy($expr);
        }

        foreach($this->order as $expr) {
            $qb->addOrderBy($expr);
        }

        foreach($this->parameters as $name => $value) {
            $qb->setParameter($name, $value);
        }
    }

}
