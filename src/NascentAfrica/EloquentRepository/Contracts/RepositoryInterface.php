<?php

namespace NascentAfrica\EloquentRepository\Contracts;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as Criteria;
use NascentAfrica\EloquentRepository\BaseRepository;
use NascentAfrica\EloquentRepository\Exceptions\EloquentRepositoryException;

/**
 * EloquentRepository class
 *
 * @package NascentAfrica\EloquentRepository
 * @author Anitche Chisom
 */
interface RepositoryInterface
{
    /**
     * Retrieve all data of repository
     *
     * @param string[] $columns
     * @return Collection
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function all($columns = ['*']): Collection;

    /**
     * Count the resources in the database.
     *
     * @return int
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function count(): int;

    /**
     * Count results of repository
     *
     * @param array $where
     * @param string $columns
     * @return int
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function countWhere(array $where = [], $columns = '*'): int;

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @return Model
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function create(array $attributes): Model;

    /**
     * Delete a entity in repository by id
     *
     * @param Model|mixed $id
     * @return int
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function delete($id): int;

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     * @return int
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function deleteWhere(array $where): int;

    /**
     * Find resource by it's ID
     *
     * @param $id
     * @param string[] $columns
     * @return Model
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function find($id, $columns = ['*']): Model;

    /**
     * Find data by field and value
     *
     * @param $field
     * @param null $value
     * @param string[] $columns
     * @return Collection
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function findByField($field, $value = null, $columns = ['*']): Collection;

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param string[] $columns
     * @return Collection
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function findWhere(array $where, $columns = ['*']): Collection;

    /**
     * Find data by multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param string[] $columns
     * @return Collection
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function findWhereIn($field, array $values, $columns = ['*']): Collection;

    /**
     * Find data by excluding multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param string[] $columns
     * @return Collection
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function findWhereNotIn($field, array $values, $columns = ['*']): Collection;

    /**
     * Find data by between values in one field
     *
     * @param $field
     * @param array $values
     * @param string[] $columns
     * @return Collection
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function findWhereBetween($field, array $values, $columns = ['*']): Collection;

    /**
     * Retrieve first data of repository
     *
     * @param string[] $columns
     * @return Model
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function first($columns = ['*']): Model;

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     * @return Model
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function firstOrNew(array $attributes = []): Model;

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     * @return Model
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function firstOrCreate(array $attributes = []): Model;

    /**
     * Alias of All method
     *
     * @param string[] $columns
     * @return Collection
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function get($columns = ['*']): Collection;

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     * @return Collection
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function getByCriteria(CriteriaInterface $criteria): Collection;

    /**
     * Get Collection of Criteria
     *
     * @return Criteria
     */
    public function getCriteria(): Criteria;

    /**
     * Get Searchable Fields
     *
     * @return array
     */
    public function getFieldsSearchable(): array;

    /**
     * Get the model instance being queried.
     *
     * @return Builder|Model
     */
    public function getModel();

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation);

    /**
     * Set hidden fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields);

    /**
     * Set the "limit" value of the query.
     *
     * @param int $limit
     * @return Builder|mixed
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function limit($limit);

    /**
     * Only return trashed results.
     *
     * @return $this|BaseRepository
     * @throws EloquentRepositoryException
     */
    public function onlyTrashed();

    /**
     * Order query
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc');

    /**
     * Paginate the given query.
     *
     * @param int $perPage
     * @param string[] $columns
     * @param string $pageName
     * @param null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null);

    /**
     * Get an array with the values of a given column.
     *
     * @param string $column
     * @param null|string $key
     * @return array|Criteria
     */
    public function pluck($column, $key = null);

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
     * @throws EloquentRepositoryException
     * @throws BindingResolutionException
     */
    public function resetModel();

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope();

    /**
     * Perform a search against the model's indexed data.
     *
     * @param string $search
     * @param null $callback
     * @return $this|Collection|\Laravel\Scout\Builder
     * @throws EloquentRepositoryException
     */
    public function search($search = '', $callback = null);

    /**
     * @param Model $model
     * @return BaseRepository
     */
    public function setModel(Model $model): BaseRepository;

    /**
     * Query Scope
     *
     * @param Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(Closure $scope);

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param null $perPage
     * @param string[] $columns
     * @param string $pageName
     * @param null $page
     * @return Paginator
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null): Paginator;

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * Sync relations
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @param bool $detaching
     * @return mixed
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function sync($id, $relation, $attributes, $detaching = true);

    /**
     * SyncWithoutDetaching
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @return mixed
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function syncWithoutDetaching($id, $relation, $attributes);

    /**
     * Update a entity in repository by id
     *
     * @param $id
     * @param array $attributes
     * @return Model
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function update($id, array $attributes): Model;

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     * @return Model
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;

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
     * @param mixed $relations
     * @return $this
     */
    public function withCount($relations);

    /**
     * Include trashed to query.
     *
     * @return $this
     */
    public function withTrashed();

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param Closure $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure);
}