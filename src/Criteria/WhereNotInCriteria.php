<?php

namespace JoshThackeray\Repositories\Criteria;

use JoshThackeray\Repositories\Repository;

class WhereNotInCriteria extends BaseCriteria
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $values;

    /**
     * WhereNotInCriteria constructor.
     *
     * @param string $field
     * @param array $values
     */
    public function __construct($field, $values = [])
    {
        $this->field = $field;
        $this->values = $values;
    }

    /**
     * Applies the criteria.
     *
     * @param $model
     * @param Repository $repository
     * @return mixed
     */
    public  function apply($model, Repository $repository)
    {
        return $model->whereNotIn($this->field, $this->values);
    }
}