<?php

namespace JoshThackeray\Repositories\Criteria;

use JoshThackeray\Repositories\Repository;

abstract class BaseCriteria
{
    /**
     * An abstract method for classes to implement which will apply the criteria query to the model.
     *
     * @param $model
     * @param Repository $repository
     * @return mixed
     */
    public abstract function apply($model, Repository $repository);
}