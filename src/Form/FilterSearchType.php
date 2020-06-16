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
            ->add("text", $options["main_module"])
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
            foreach($data["filters"] as $filter) {
                if(!$filter["type"])
                    continue;

                $module = $filter["type"];

                $moduleJoins = $module::getJoins();
                $moduleGroups = $module::getGroups();

                //if it's not an array, make it an array
                if(!is_array($moduleJoins)) {
                    $moduleJoins = [$moduleJoins];
                }

                if(!is_array($moduleGroups)) {
                    $moduleGroups = [$moduleGroups];
                }

                //adds the joins to the joins array using the alias as key
                foreach($moduleJoins as $join) {
                    $joins[$join->getAlias()] = $join;
                }

                //add the group by to the query builder
                foreach($moduleGroups as $group) {
                    $group->extend($qb);
                }
            }

            //take the list of joins from the text module
            foreach($options["main_module"]::getJoins() as $join) {
                $joins[$join->getAlias()] = $join;
            }

            //make the joins on the query builder
            foreach($joins as $class => $join) {
                $join->extend($qb);
            }

            //extend query with text module
            $part = new QueryPart();

            $moduleType = $form->get("text")->getConfig()->getType()->getInnerType();
            if($data["text"] && isset($data["text"]["search"])) {
                $moduleType->extendQuery($part, $data["text"]["search"]);
            }

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
        $resolver->setRequired(["main_module", "modules", "query_builder"]);
    }

}
