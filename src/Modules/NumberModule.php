<?php

namespace FilterSearch\Modules;

use FilterSearch\Database\QueryPart;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

abstract class NumberModule extends AbstractModule
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("comparison", ChoiceType::class, [
                "choices" => [
                    "between" => "between",
                    "greater than" => "greater",
                    "lesser than" => "lesser"
                ],
                "empty_data" => "between"
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $form = $event->getForm();
            $comparison = isset($event->getData()["comparison"]) ? $event->getData()["comparison"] : "between";

            if($comparison == "between" || $comparison == "greater") {
                $form->add("min", NumberType::class, [
                    "attr" => [
                        "placeholder" => "minimum value"
                    ]
                ]);
            }

            if($comparison == "between" || $comparison == "lesser") {
                $form->add("max", NumberType::class, [
                    "attr" => [
                        "placeholder" => "maximum value"
                    ]
                ]);
            }
        });
    }

    public function extendQuery(QueryPart $qp, array $data): void
    {
        if(isset($data["min"])) {
            $this->extendQueryWithMin($qp, $data["min"]);
        }

        if(isset($data["max"])) {
            $this->extendQueryWithMax($qp, $data["max"]);
        }
    }

    protected abstract function extendQueryWithMin(QueryPart $qp, $min);

    protected abstract function extendQueryWithMax(QueryPart $qp, $max);

    public function getBlockPrefix()
    {
        return "number_module";
    }

}
