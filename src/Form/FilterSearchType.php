<?php

namespace FilterSearch\Form;

use FilterSearch\Database\QueryPart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A filter search type.
 *
 * This type takes two options:
 *  - "modules": The list of modules available
 * for this form.
 *  - "query_builder": The query builder to modify
 * when this form is modified.
 *
 * The template for this form is in the
 * `views/filter_search.html.twig` file.
 *
 * @package FilterSearch\Form
 */
class FilterSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod("GET")
            ->add("match", ChoiceType::class, [
                "choices" => [
                    "all" => "all",
                    "any" => "any",
                    "none" => "none"
                ]
            ])
            ->add("text", $options["text_module"])
            ->add("filters", CollectionType::class, [
                "entry_type" => FilterType::class,
                "allow_add" => true,
                "prototype_name" => "__filter_name__",
                "entry_options" => [
                    "modules" => $options["modules"],
                    "query_builder" => $options["query_builder"]
                ]
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($options) {
            $form = $event->getForm();
            $data = $event->getData();

            $qb = $options["query_builder"];
            $joins = [];

            //retrieve the list of joins to make
            foreach($options["modules"] as $module) {
                $moduleJoins = $module::getJoins();

                //if it's not an array, make it an array
                if(!is_array($moduleJoins)) {
                    $moduleJoins = [$moduleJoins];
                }

                //adds the joins to the joins array using the alias as key
                foreach($moduleJoins as $join) {
                    $joins[$join->getAlias()] = $join;
                }
            }

            //make the joins on the query builder
            foreach($joins as $class => $join) {
                $join->extend($qb);
            }

            //extend query with text module
            $part = new QueryPart();

            $moduleType = $form->get("text")->getConfig()->getType()->getInnerType();
            $moduleType->extendQuery($part, $data["text"]);

            $part->merge($qb, $data["match"]);

            //now retrieve all the child modules to add them to the query builder
            foreach($form->get("filters")->all() as $filter) {
                if($filter->getData()["type"]) {
                    $part = new QueryPart();

                    $moduleType = $filter->get("module")->getConfig()->getType()->getInnerType();
                    $moduleType->extendQuery($part, $filter->getData()["module"]);

                    $part->merge($options["query_builder"], $data["match"]);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("csrf_protection", false);
        $resolver->setRequired(["text_module", "modules", "query_builder"]);
    }

}
