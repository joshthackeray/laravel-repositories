<?php

namespace JoshThackeray\Repositories;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JoshThackeray\Repositories\Criteria\BaseCriteria;
use JoshThackeray\Repositories\Criteria\CriteriaInterface;
use JoshThackeray\Repositories\Exceptions\RepositoriesException;

abstract class Repository implements RepositoryInterface, CriteriaInterface
{
    /**
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    private $skipCriteria = false;

    /**
     * @var \Illuminate\Container\Container
     */
    private $container;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Repository constructor.
     *
     * @param Container $container
     * @param Collection $collection
     * @throws RepositoriesException
     */
    public function __construct(Container $container, Collection $collection)
    {
        $this->container = $container;
        $this->criteria = $collection;
        $this->resetScope();
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    abstract function model();

    /**
     * @return Model
     * @throws RepositoriesException
     */
    private function makeModel() {
        $model = $this->container->make($this->model());

        if (!$model instanceof Model)
            throw new RepositoriesException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");

        return $this->model = $model;
    }

    /**
     * Resets the criteria scope for working on fresh instances.
     *
     * @return $this
     */
    public function resetScope() {
        $this->skipCriteria(false);
        return $this;
    }

    /**
     * Skips any criteria in the collection.
     *
     * @param bool $status
     * @return $this|mixed
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;
        return $this;
    }

    /**
     * Returns all criteria collection.
     *
     * @return Collection|mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Directly applies the criteria to the repo instance for one line implementations.
     *
     * @param BaseCriteria $criteria
     * @return $this|mixed
     */
    public function getByCriteria(BaseCriteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);
        return $this;
    }

    /**
     * Pushes the given Criteria object to the criteria collection.
     *
     * @param BaseCriteria $criteria
     * @return $this|mixed
     */
    public function pushCriteria(BaseCriteria $criteria)
    {
        $this->criteria->push($criteria);
        return $this;
    }

    /**
     * Applies the criteria Collection to the current repo instance.
     *
     * @return $this|mixed
     */
    public function applyCriteria()
    {
        if($this->skipCriteria)
            return $this;

        foreach($this->getCriteria() as $criteria) {
            if($criteria instanceof BaseCriteria)
                $this->model = $criteria->apply($this->model, $this);
        }

        return $this;
    }

    /**
     * Returns all rows for the current Model.
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->get($columns);
    }

    /**
     * Returns all rows with the current criteria as paginated results.
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate($perPage = 15, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Returns a single row for the current Model by ID.
     *
     * @param $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, $columns = array('*'))
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Returns a single row for the current Model by the given criteria.
     * If none is found, it will create one with the specified data.
     *
     * @param array $where
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrCreate(array $where, array $data)
    {
        return $this->model->firstOrCreate($where, $data);
    }

    /**
     * Returns a single row for the given criteria.
     *
     * @param $field
     * @param $operator
     * @param $value
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByField($field, $operator, $value, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->where($field, $operator, $value)->get($columns);
    }

    /**
     * Returns rows that match the criteria.
     *
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhere(array $where, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->where($where)->get($columns);
    }

    /**
     * Returns rows where the given field are LIKE the value specified.
     *
     * @param $field
     * @param $value
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhereLike($field, $value, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->where($field, 'LIKE', "%$value%")->get($columns);
    }

    /**
     * Returns rows where the given field contains one of the specified values.
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhereIn($field, array $values, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->whereIn($field, $values)->get($columns);
    }

    /**
     * Returns rows where the given field does not contain one of the specified values.

     * @param $field
     * @param array $values
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function findWhereNotIn($field, array $values, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->whereNotIn($field, $values)->get($columns);
    }

    /**
     * Creates a new model with the given data.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Updates the given model by ID, with the specified data.
     *
     * @param $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        $model = $this->find($id);
        return $model->update($data);
    }

    /**
     * Either updates or creates the given data, depending on whether
     * the $where data matches a row.
     *
     * @param array $where
     * @param array $data
     * @return return \Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate(array $where, array $data)
    {
        return $this->model->updateOrCreate($where, $data);
    }

    /**
     * Updates all records with the given data that match the search criteria.
     *
     * @param array $where
     * @param array $data
     * @return int
     */
    public function updateWhere(array $where, array $data)
    {
        return $this->model->where($where)->update($data);
    }

    /**
     * Updates all records with the given data that have values in the given array.
     *
     * @param $field
     * @param array $values
     * @param array $data
     * @return int
     */
    public function updateWhereIn($field, array $values, array $data)
    {
        return $this->model->whereIn($field, $values)->update($data);
    }

    /**
     * Updates all records with the given data that have does not have values in the given array.
     *
     * @param $field
     * @param array $values
     * @param array $data
     * @return int
     */
    public function updateWhereNotIn($field, array $values, array $data)
    {
        return $this->model->whereNotIn($field, $values)->update($data);
    }

    /**
     * Deletes a record by the given ID.
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $model = $this->model->find($id);
        return $model->delete();
    }

    /**
     * Deletes multiple records which match the given criteria.
     *
     * @param array $where
     * @return int
     */
    public function deleteWhere(array $where)
    {
        return $this->model->where($where)->delete();
    }

    /**
     * Deletes multiple records which fields match at least one of the given values.
     *
     * @param $field
     * @param array $values
     * @return int
     */
    public function deleteWhereIn($field, array $values)
    {
        return $this->model->whereIn($field, $values)->delete();
    }


    /**
     * Deletes multiple records which fields do not match at least one of the given values.
     *
     * @param $field
     * @param array $values
     * @return int
     */
    public function deleteWhereNotIn($field, array $values)
    {
        return $this->model->whereNotIn($field, $values)->delete();
    }


}