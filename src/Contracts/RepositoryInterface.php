<?php

namespace NascentAfrica\EloquentRepository\Contracts;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\Paginator;

/**
 * RepositoryInterface class
 *
 * @package NascentAfrica\EloquentRepository
 * @author Anitche Chisom
 */
interface RepositoryInterface
{
    /**
     * Get model path
     *
     * @return string
     */
    public function model(): string;

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = ['*']): Collection;

    /**
     * Count results of repository
     *
     * @return int
     */
    public function count(): int;

    /**
     * Count results of repository
     *
     * @param array $where
     * @param string $columns
     *
     * @return int
     */
    public function countWhere(array $where = [], $columns = '*'): int;

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes): Model;

    /**
     * Delete a entity in repository by id
     *
     * @param \Illuminate\Database\Eloquent\Model|mixed $id
     *
     * @return int
     */
    public function delete($id): int;

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     *
     * @return int
     */
    public function deleteWhere(array $where): int;

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @throws ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, $columns = ['*']): Model;

    /**
     * Find data by field and value
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByField($field, $value = null, $columns = ['*']): Collection;

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhere(array $where, $columns = ['*']): Collection;

    /**
     * Find data by multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhereIn($field, array $values, $columns = ['*']): Collection;

    /**
     * Find data by excluding multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhereNotIn($field, array $values, $columns = ['*']): Collection;

    /**
     * Find data by between values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhereBetween($field, array $values, $columns = ['*']): Collection;

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function first($columns = ['*']): Model;

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function firstOrNew(array $attributes = []): Model;

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function firstOrCreate(array $attributes = []): Model;

    /**
     * Alias of All method
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*']): Collection;

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCriteria(CriteriaInterface $criteria): Collection;

    /**
     * Paginate the given query.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     *
     * @throws \InvalidArgumentException|EloquentRepositoryException
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null): LengthAwarePaginator;


    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null): Paginator;

    /**
     * Update a entity in repository by id
     *
     * @param       $id
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $attributes): Model;

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;



    /*
     * Not tested.
     */

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria);

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     *
     * @return $this
     * @throws EloquentRepositoryException
     */
    public function pushCriteria($criteria);

    /**
     * Reset all Criteria
     *
     * @return $this
     */
    public function resetCriteria();

    /**
     * Set visible fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields);

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations);

    /**
     * Add sub-select queries to count the relations.
     *
     * @param  mixed $relations
     * @return $this
     */
    public function withCount($relations);

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param closure $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure);
}
