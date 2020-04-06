<?php

namespace FilterSearch\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class is just a type that makes
 * the connection between the search type
 * and the module.
 *
 * Takes a required "modules" which is an
 * array of the available modules.
 * The array should associate a string to
 * a module class :
 * ```php
 * $modules = [
 *     "country" => CountryModule::class,
 *     "price" => PriceRangeModule::class
 * ];
 *```
 *
 * @package App\Form\Search
 */
class FilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //initialize the module list for the modules selector
        $modules = [];
        foreach($options["modules"] as $module) {
            $modules[$module::getName()] = $module;
        }

        $builder
            //selector for the module
            ->add("type", ChoiceType::class, [
                "choices" => $modules,
                "placeholder" => "Select filter type"
            ]);

        //before form submission, update the modules
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($modules) {
            $form = $event->getForm();
            $type = $event->getData()["type"];

            //load the module if the user chose one in the "type" selector
            if($type) {
                $form->add("module", $type);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(["modules", "query_builder"]);
    }

}
