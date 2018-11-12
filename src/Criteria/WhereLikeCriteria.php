<?php

namespace JoshThackeray\Repositories\Criteria;

use JoshThackeray\Repositories\Repository;

class WhereLikeCriteria extends BaseCriteria
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $value;

    /**
     * WhereLikeCriteria constructor.
     *
     * @param string $field
     * @param string $value
     */
    public function __construct($field, $value = '')
    {
        $this->field = $field;
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
        return $model->where($this->field, 'LIKE', "%{$this->value}%");
    }
}