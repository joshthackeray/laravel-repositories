<?php

namespace JoshThackeray\Repositories\Criteria;

use JoshThackeray\Repositories\Repository;

class WhereCriteria extends BaseCriteria
{
    /**
     * @var mixed
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $value;

    /**
     * WhereCriteria constructor.
     *
     * @param mixed $field
     * @param string $operator
     * @param string $value
     */
    public function __construct($field, $operator = '=', $value = '')
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
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
        return $model->where($this->field, $this->operator, $this->value);
    }
}