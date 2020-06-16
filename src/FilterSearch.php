<?php

namespace FilterSearch;

use Doctrine\ORM\EntityManagerInterface;
use FilterSearch\Form\FilterSearchForm;
use FilterSearch\Form\FilterSearchType;
use FilterSearch\Modules\AbstractModule;
use FilterSearch\Modules\MainModule;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;

class FilterSearch
{

    private $manager;

    private $factory;

    public function __construct(EntityManagerInterface $manager, FormFactoryInterface $ff)
    {
        $this->manager = $manager;
        $this->factory = $ff;
    }

    public function create(string $entity, array $modules, ?string $alias = null): FilterSearchForm {
        if(!class_exists($entity))
            throw new RuntimeException("Expected entity class as the first argument");

        if(!class_exists($entity))
            throw new RuntimeException("Expected entity class as the first argument");

        //find the alias if it was not sent
        if(!$alias) {
            $alias = strtolower((new ReflectionClass($entity))->getShortName());
        }

        $qb = $this->manager->createQueryBuilder()
            ->select($alias)
            ->from($entity, $alias);

        $main = null;
        $other = [];

        foreach($modules as $module) {
            if(is_subclass_of($module, MainModule::class)) {
                if($main != null) {
                    throw new RuntimeException("Expected a single MainModule");
                }

                $main = $module;
            } else if(is_subclass_of($module, AbstractModule::class)) {
                $other[] = $module;
            } else {
                throw new RuntimeException("Expected either classes extending MainModule or AbstractModule for modules list");
            }
        }

        $form = $this->factory->createNamed("a", FilterSearchType::class, null, [
            "main_module" => $main,
            "modules" => $other,
            "query_builder" => $qb
        ]);

        return new FilterSearchForm($qb, $form, $modules);
    }

}
