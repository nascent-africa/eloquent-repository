<?php

namespace NascentAfrica\EloquentRepository;


use NascentAfrica\EloquentRepository\Exception\EloquentRepositoryException;
use Illuminate\Support\Collection as Criteria;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use NascentAfrica\EloquentRepository\Contracts\RepositoryInterface;
use NascentAfrica\EloquentRepository\Contracts\CriteriaInterface;
use NascentAfrica\EloquentRepository\Events\RepositoryEntityCreated;
use NascentAfrica\EloquentRepository\Events\RepositoryEntityDeleted;
use NascentAfrica\EloquentRepository\Events\RepositoryEntityUpdated;
use NascentAfrica\EloquentRepository\Traits\ComparesVersionsTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

/**
 * EloquentRepository class
 *
 * @package NascentAfrica\EloquentRepository
 * @author Anitche Chisom
 */
abstract class BaseRepository implements RepositoryInterface
{
    use ComparesVersionsTrait;

    /**
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Collection of Criteria
     *
     * @var Criteria
     */
    protected $criteria;

    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var \Closure
     */
    protected $scopeQuery = null;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * EloquentRepository constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->app = $container;
        $this->criteria = new Criteria();
        $this->makeModel();
        $this->boot();
    }

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = ['*']): Collection
    {
        $this->applyCriteria();
        $this->applyScope();

        if ($this->model instanceof Builder) {
            $collection = $this->model->get($columns);
        } else {
            $collection = $this->model->all($columns);
        }

        $this->resetModel();
        $this->resetScope();

        return $collection;
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     * @return void
     */
    protected function applyConditions(array $where): void
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * Apply criteria in current Query
     *
     * @return $this
     */
    protected function applyCriteria()
    {

        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof CriteriaInterface) {
                    $this->model = $c->apply($this->model, $this);
                }
            }
        }

        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope()
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }

    protected function boot()
    {}

    /**
     * Count results of repository
     *
     * @return int
     */
    public function count(): int
    {
        $result = $this->model->count();
        $this->resetModel();

        return $result;
    }

    /**
     * Count results of repository
     *
     * @param array $where
     * @param string $columns
     *
     * @return int
     */
    public function countWhere(array $where = [], $columns = '*'): int
    {
        $this->applyCriteria();
        $this->applyScope();

        if($where) {
            $this->applyConditions($where);
        }

        $result = $this->model->count($columns);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes): Model
    {
        $model = $this->model->newInstance($attributes);
        $model->save();
        $this->resetModel();

        if (function_exists('event')) {
            event(new RepositoryEntityCreated($this, $model));
        }

        return $model;
    }

    /**
     * Delete a entity in repository by id
     *
     * @param \Illuminate\Database\Eloquent\Model|$id
     *
     * @return int
     */
    public function delete($id): int
    {
        $this->applyScope();

        $model = $this->forceReturnModel($id);

        $originalModel = clone $model;

        $this->resetModel();

        $deleted = $model->delete();

        if (function_exists('event')) {
            event(new RepositoryEntityDeleted($this, $originalModel));
        }

        return $deleted;
    }

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     *
     * @return int
     */
    public function deleteWhere(array $where): int
    {
        $this->applyScope();

        $this->applyConditions($where);

        $deleted = $this->model->delete();

        if (function_exists('event')) {
            event(new RepositoryEntityDeleted($this, $this->model->getModel()));
        }

        $this->resetModel();

        return $deleted;
    }

    /**
     * Ensure a model is returned model.
     *
     * @param int|Model
     *
     * @throws ModelNotFoundException
     * @return Model
     */
    protected function forceReturnModel($type)
    {
        if ($type instanceof Model) {
            return $type;
        }

        return $this->find($type);
    }

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @throws ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, $columns = ['*']): Model
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->findOrFail($id, $columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find data by field and value
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByField($field, $value = null, $columns = ['*']): Collection
    {
        $this->applyCriteria();
        $this->applyScope();
        $collection = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();

        return $collection;
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhere(array $where, $columns = ['*']): Collection
    {
        $this->applyCriteria();
        $this->applyScope();

        $this->applyConditions($where);

        $collection = $this->model->get($columns);
        $this->resetModel();

        return $collection;
    }

    /**
     * Find data by multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhereIn($field, array $values, $columns = ['*']): Collection
    {
        $this->applyCriteria();
        $this->applyScope();
        $collection = $this->model->whereIn($field, $values)->get($columns);
        $this->resetModel();

        return $collection;
    }

    /**
     * Find data by excluding multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhereNotIn($field, array $values, $columns = ['*']): Collection
    {
        $this->applyCriteria();
        $this->applyScope();
        $collection = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();

        return $collection;
    }

    /**
     * Find data by between values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWhereBetween($field, array $values, $columns = ['*']): Collection
    {
        $this->applyCriteria();
        $this->applyScope();
        $collection = $this->model->whereBetween($field, $values)->get($columns);
        $this->resetModel();

        return $collection;
    }

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function first($columns = ['*']): Model
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->first($columns);

        $this->resetModel();

        return $model;
    }

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function firstOrNew(array $attributes = []): Model
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->firstOrNew($attributes);

        $this->resetModel();

        return $model;
    }

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function firstOrCreate(array $attributes = []): Model
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->firstOrCreate($attributes);

        $this->resetModel();

        return $model;
    }

    /**
     * Alias of All method
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*']): Collection
    {
        return $this->all($columns);
    }

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCriteria(CriteriaInterface $criteria): Collection
    {
        $this->model = $criteria->apply($this->model, $this);
        $collection = $this->model->get();
        $this->resetModel();

        return $collection;
    }

    /**
     * Get Collection of Criteria
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    /**
     * Get Searchable Fields
     *
     * @return array
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);

        return $this;
    }

    /**
     * Set hidden fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields)
    {
        $this->model->setHidden($fields);

        return $this;
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param  int  $limit
     * @return mixed
     */
    public function limit($limit)
    {
        $this->applyCriteria();
        $this->applyScope();
        $results = $this->model->limit($limit);

        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * Retrieve data array for populate field select
     *
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function lists($column, $key = null)
    {
        $this->applyCriteria();

        return $this->model->lists($column, $key);
    }

    /**
     * Instantiate model from path.
     *
     * @return Model
     * @throws EloquentRepositoryException
     */
    public function makeModel(): Model
    {
        $model = $this->app->make($this->model());
        if (!$model instanceof Model) {
            throw new EloquentRepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
        return $this->model = $model;
    }

    /**
     * Order query
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

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
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $results = $this->model->paginate($perPage, $columns, $pageName, $page);

        if (function_exists('app')) {
            $results->appends(app('request')->query());
        }

        $this->resetModel();
        return $results;
    }

    /**
     * Retrieve data array for populate field select
     *
     * Compatible with Laravel 5.3
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function pluck($column, $key = null)
    {
        $this->applyCriteria();

        return $this->model->pluck($column, $key);
    }

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria)
    {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }

            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }

            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     *
     * @return $this
     * @throws EloquentRepositoryException
     */
    public function pushCriteria($criteria)
    {
        if (is_string($criteria)) {
            $criteria = new $criteria;
        }
        if (!$criteria instanceof CriteriaInterface) {
            throw new EloquentRepositoryException("Class " . get_class($criteria) . " must be an instance of Prettus\\Repository\\Contracts\\CriteriaInterface");
        }
        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Reset all Criteria
     *
     * @return $this
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Query Scope
     *
     * @param \Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null): Paginator
    {
        $results = $this->model->simplePaginate($perPage, $columns, $pageName, $page);

        if (function_exists('app')) {
            $results->appends(app('request')->query());
        }

        $this->resetModel();
        return $results;
    }

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * Sync relations
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @param bool $detaching
     * @return mixed
     */
    public function sync($id, $relation, $attributes, $detaching = true)
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * SyncWithoutDetaching
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @return mixed
     */
    public function syncWithoutDetaching($id, $relation, $attributes)
    {
        return $this->sync($id, $relation, $attributes, false);
    }

    /**
     * Update a entity in repository by id
     *
     * @param       $id
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $attributes): Model
    {
        $this->applyScope();

        $model = $this->model->findOrFail($id);
        $model->fill($attributes);
        $model->save();

        $this->resetModel();

        if (function_exists('event')) {
            event(new RepositoryEntityUpdated($this, $model));
        }

        return $model;
    }

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        $this->applyScope();

        $model = $this->model->updateOrCreate($attributes, $values);

        $this->resetModel();

        if (function_exists('event')) {
            event(new RepositoryEntityUpdated($this, $model));
        }

        return $model;
    }

    /**
     * Set visible fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields)
    {
        $this->model->setVisible($fields);

        return $this;
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Add sub-select queries to count the relations.
     *
     * @param  mixed $relations
     * @return $this
     */
    public function withCount($relations)
    {
        $this->model = $this->model->withCount($relations);
        return $this;
    }

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param closure $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Trigger static method calls to the model
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        return call_user_func_array([new static(), $method], $arguments);
    }

    /**
     * Trigger method calls to the model
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $this->applyCriteria();
        $this->applyScope();

        return call_user_func_array([$this->model, $method], $arguments);
    }
}