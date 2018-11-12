<?php

namespace JoshThackeray\Repositories\Criteria;

interface CriteriaInterface
{
    /**
     * @return mixed
     */
    public function resetScope();

    /**
     * @param bool $status
     * @return mixed
     */
    public function skipCriteria($status = true);

    /**
     * @return mixed
     */
    public function getCriteria();

    /**
     * @param BaseCriteria $criteria
     * @return mixed
     */
    public function getByCriteria(BaseCriteria $criteria);

    /**
     * @param BaseCriteria $criteria
     * @return mixed
     */
    public function pushCriteria(BaseCriteria $criteria);

    /**
     * @return mixed
     */
    public function applyCriteria();
}