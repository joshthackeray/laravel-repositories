<?php

namespace JoshThackeray\Repositories;

interface RepositoryInterface
{
    public function all($columns = array('*'));

    public function find($id, $columns = array('*'));

    public function findOrCreate(array $where, array $data);

    public function findByField($field, $operator, $value, $columns = array('*'));

    public function findWhere(array $where, $columns = array('*'));

    public function findWhereLike($field, $value, $columns = array('*'));

    public function findWhereIn($field, array $values, $columns = array('*'));

    public function findWhereNotIn($field, array $values, $columns = array('*'));

    public function create(array $data);

    public function update($id, array $data);

    public function updateWhere(array $where, array $data);

    public function updateWhereIn($field, array $values, array $data);

    public function updateWhereNotIn($field, array $values, array $data);

    public function updateOrCreate(array $where, array $data);

    public function delete($id);

    public function deleteWhere(array $where);

    public function deleteWhereIn($field, array $values);

    public function deleteWhereNotIn($field, array $values);
}