<?php

namespace FilterSearch\Form;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Wraps a filter search from type.
 *
 * @package FilterSearch\Form
 */
class FilterSearchForm
{

    private $qb;

    private $form;

    private $modules;

    private $request;

    public function __construct(QueryBuilder $qb, FormInterface $form, array $modules)
    {
        $this->qb = $qb;
        $this->form = $form;
        $this->modules = $modules;
    }

    /**
     * Returns whether the submitted request is a
     * real form submission or an AJAX call to
     * update the form structure.
     *
     * @return bool
     */
    public function isStructureUpdate(): bool {
        if(!$this->request)
            throw new RuntimeException("Call to FilterSearch::isStructureUpdate requires a call to FilterSearch::handleRequest before");

        return $this->request->query->get("__form_update", false);
    }

    /**
     * Getter for the query builder.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder {
        return $this->qb;
    }

    /**
     * Builds the query.
     *
     * @return Query
     */
    public function getQuery(): Query {
        return $this->qb->getQuery();
    }

    /**
     * Builds the query and returns the result.
     *
     * @return mixed
     */
    public function getResult() {
        return $this->qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function isSubmitted(): bool {
        if(!$this->request)
            throw new RuntimeException("Call to FilterSearch::isSubmitted requires a call to FilterSearch::handleRequest before");

        return $this->form->isSubmitted();
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool {
        if(!$this->request)
            throw new RuntimeException("Call to FilterSearch::isValid requires a call to FilterSearch::handleRequest before");

        return $this->form->isValid();
    }

    /**
     * @inheritDoc
     */
    public function handleRequest($request = null) {
        $this->request = $request;
        $this->form->handleRequest($request);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function createView(FormView $parent = null) {
        return $this->form->createView($parent);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->form->offsetExists($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->form->offsetGet($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->form->offsetSet($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->form->offsetUnset($offset);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->form->count();
    }

    /**
     * @inheritDoc
     */
    public function setParent(FormInterface $parent = null)
    {
        $this->form->setParent($parent);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return $this->form->getParent();
    }

    /**
     * @inheritDoc
     */
    public function add($child, string $type = null, array $options = [])
    {
        $this->form->add($child, $type, $options);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name)
    {
        return $this->form->get($name);
    }

    /**
     * @inheritDoc
     */
    public function has(string $name)
    {
        return $this->form->has($name);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $name)
    {
        $this->form->remove($name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $this->form->all();
    }

    /**
     * @inheritDoc
     */
    public function getErrors(bool $deep = false, bool $flatten = true)
    {
        return $this->form->getErrors($deep, $flatten);
    }

    /**
     * @inheritDoc
     */
    public function setData($modelData)
    {
        $this->form->setData($modelData);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->form->getData();
    }

    /**
     * @inheritDoc
     */
    public function getNormData()
    {
        return $this->form->getNormData();
    }

    /**
     * @inheritDoc
     */
    public function getViewData()
    {
        return $this->form->getViewData();
    }

    /**
     * @inheritDoc
     */
    public function getExtraData()
    {
        return $this->form->getExtraData();
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return $this->form->getConfig();
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->form->getName();
    }

    /**
     * @inheritDoc
     */
    public function getPropertyPath()
    {
        return $this->form->getPropertyPath();
    }

    /**
     * @inheritDoc
     */
    public function addError(FormError $error)
    {
        $this->form->addError($error);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRequired()
    {
        return $this->form->isRequired();
    }

    /**
     * @inheritDoc
     */
    public function isDisabled()
    {
        return $this->form->isDisabled();
    }

    /**
     * @inheritDoc
     */
    public function isEmpty()
    {
        return $this->form->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function isSynchronized()
    {
        return $this->form->isSynchronized();
    }

    /**
     * @inheritDoc
     */
    public function getTransformationFailure()
    {
        return $this->form->getTransformationFailure();
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $this->form->initialize();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function submit($submittedData, bool $clearMissing = true)
    {
        $this->form->submit($submittedData, $clearMissing);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoot()
    {
        return $this->form->getRoot();
    }

    /**
     * @inheritDoc
     */
    public function isRoot()
    {
        return $this->form->isRoot();
    }

}
