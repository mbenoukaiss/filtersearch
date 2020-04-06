<?php

namespace FilterSearch\Form;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Wraps a filter search from type.
 *
 * @package App\Form\Search
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
     * Handles the request.
     *
     * @param Request $request
     */
    public function handleRequest(Request $request) {
        $this->request = $request;
        $this->form->handleRequest($request);
    }

    /**
     * Returns whether the form is submitted.
     *
     * In order to refresh the form's fields (when
     * selecting a different module for example)
     * the form is submitted through an AJAX call.
     * Calling this function when the request
     * is an AJAX call will return false even if
     * Symfony's base FormInterface::isSubmitted
     * would return true.
     *
     * @return bool
     */
    public function isSubmitted(): bool {
        if(!$this->request)
            throw new RuntimeException("Call to AdvancedSearch::isSubmitted requires a call to AdvancedSearch::handleRequest before");

        //check that the request was a real form submission and not just
        //a submission to update the form dynamically
        return !$this->request->query->get("__form_update", false) && $this->form->isSubmitted();
    }

    /**
     * Returns whether the form is valid.
     *
     * @return bool
     */
    public function isValid(): bool {
        if(!$this->request)
            throw new RuntimeException("Call to AdvancedSearch::isValid requires a call to AdvancedSearch::handleRequest before");

        return $this->form->isValid();
    }

    /**
     * Creates the form view.
     *
     * @return FormView
     */
    public function createView(): FormView {
        return $this->form->createView();
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

}
