<?php

namespace FilterSearch;

use Doctrine\ORM\EntityManagerInterface;
use FilterSearch\Form\FilterSearchForm;
use FilterSearch\Form\FilterSearchType;
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

    public function create(string $entity, string $textModule, array $modules, ?string $alias = null): FilterSearchForm {
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

        $form = $this->factory->createNamed("a", FilterSearchType::class, null, [
            "text_module" => $textModule,
            "modules" => $modules,
            "query_builder" => $qb
        ]);

        return new FilterSearchForm($qb, $form, $modules);
    }

}
