<?php

namespace FilterSearch\Modules;

use FilterSearch\Database\QueryPart;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * A module that allows searching for values
 * between two other values or greater/lesser
 * than a another value.
 *
 * @package FilterSearch\Modules
 */
abstract class NumberModule extends AbstractModule
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("comparison", ChoiceType::class, [
                "choices" => [
                    "between" => "in",
                    "equal to" => "eq",
                    "greater than" => "gte",
                    "lesser than" => "lte",
                ],
                "empty_data" => "between"
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $form = $event->getForm();
            $comparison = isset($event->getData()["comparison"]) ? $event->getData()["comparison"] : "in";

            if($comparison == "in" || $comparison == "gte") {
                $form->add("min", NumberType::class, [
                    "attr" => [
                        "placeholder" => "minimum value"
                    ]
                ]);
            }

            if($comparison == "in" || $comparison == "lte") {
                $form->add("max", NumberType::class, [
                    "attr" => [
                        "placeholder" => "maximum value"
                    ]
                ]);
            }

            if($comparison == "eq") {
                $form->add("eq", NumberType::class, [
                    "attr" => [
                        "placeholder" => "value"
                    ]
                ]);
            }
        });
    }

    public function extendQuery(QueryPart $qp, array $data): void
    {
        if(isset($data["eq"])) {
            $this->extendQueryWithEqual($qp, $data["eq"]);
        }

        if(isset($data["min"])) {
            $this->extendQueryWithMin($qp, $data["min"]);
        }

        if(isset($data["max"])) {
            $this->extendQueryWithMax($qp, $data["max"]);
        }
    }

    /**
     * Extends the query part when the min value
     * is given.
     *
     * @param QueryPart $qp The query part
     * @param int $min The minimum value
     */
    protected abstract function extendQueryWithMin(QueryPart $qp, $min): void;

    /**
     * Extends the query part when the max value
     * is given.
     *
     * @param QueryPart $qp The query part
     * @param int $max The maximum value
     */
    protected abstract function extendQueryWithMax(QueryPart $qp, $max): void;

    /**
     * Extends the query part when the value
     * should be equal to the given value.
     *
     * @param QueryPart $qp The query part
     * @param int $value The value to compare with
     */
    protected abstract function extendQueryWithEqual(QueryPart $qp, $value): void;

    public function getBlockPrefix()
    {
        return "number_module";
    }

}
