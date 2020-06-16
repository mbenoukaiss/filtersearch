<?php

namespace FilterSearch\Modules;

use FilterSearch\Database\Join;
use FilterSearch\Database\QueryPart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * The main search module which consists of a single
 * text field. There can only be one main search
 * module per form.
 *
 * @package FilterSearch\Modules
 */
abstract class MainModule extends AbstractType
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

    //TODO: documentation
    public static function getGroups() {
        return [];
    }

    /**
     * The placeholder to display on the text field.
     *
     * @return string|null Text field placeholder
     */
    public static function getPlaceholder() {
        return null;
    }

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
     * @param string $search Submitted text search
     */
    public abstract function extendQuery(QueryPart $qp, string $search): void;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("search", TextType::class, [
            "attr" => [
                "placeholder" => $this->getPlaceholder()
            ],
            "label" => false,
            "required" => false
        ]);
    }

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
