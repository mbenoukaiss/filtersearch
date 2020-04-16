<?php

namespace FilterSearch\Modules;

use FilterSearch\Database\Join;
use FilterSearch\Database\QueryPart;
use Symfony\Component\Form\AbstractType;

/**
 * Defines a search module that can be used in an
 * filter search type.
 * Search modules are small Symfony forms that help
 * the user narrow down his search on a specific
 * field of an entity.
 *
 * For example, a search module to find bookings
 * that have a price between X and Y would provide
 * two input fields, one for the X value and one
 * for the Y value.
 *
 * Since a module is a form, it also needs its view.
 * The view should be defined in a twig `form_theme`
 * file.
 * The name of the block is the name of the module
 * class in snake case suffixes with "_row".
 * The view of a class named PriceRangeModule will
 * thus be "price_range_module_row".
 *
 * @package FilterSearch\Modules
 */
abstract class AbstractModule extends AbstractType
{

    /**
     * Lists all the joins that are necessary for
     * the query to be generated using the
     * extendQuery method.
     *
     * The array should associate the alias that
     * will be used in the query to the field that
     * is being joined.
     *
     * For example, a getJoins method returning
     * `Join::inner("user.bookings", "booking")` will
     * result in a call to $qb->join("u.bookings", "booking")
     * before calling the extendQuery method.
     *
     * @return Join|array Either a single join or an array
     * of Joins
     */
    public static function getJoins() {
        return [];
    }
    public static function getGroups() {
        return [];
    }

    /**
     * The name of the modules which will be displayed
     * to the user.
     *
     * @return string Name of the module
     */
    public static abstract function getName(): string;

    /**
     * Extends the query builder with the data submitted
     * to the module.
     *
     * When using parameters with names through QueryBuilder's
     * setParameter method, it is recommended to use randomly
     * generated parameter names using the generateParameterName
     * method in order to avoid conflicting parameter names
     * in the query builder.
     *
     * @param QueryPart $qp The query part
     * @param array $data Submitted data
     */
    public abstract function extendQuery(QueryPart $qp, array $data): void;

    /**
     * Generates a random alphabetic string that can be
     * used as a parameter name in a query builder.
     *
     * @return string Random parameter name
     */
    protected function generateParameterName(): string {
        return "pn" . md5(random_bytes(8));
    }

}
