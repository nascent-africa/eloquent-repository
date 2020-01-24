# Eloquent Repository
Eloquent Repository is used to abstract the data layer, making our application more flexible to maintain.

Many thanks to [Anderson Andrade](https://github.com/andersao) for this project is a stripped down version of [andersao/l5-repository](https://github.com/andersao/l5-repository). This was done because i preferred to do my validations and transformations else where and not in my repository.

## Installation

### Composer

Execute the following command to get the latest version of the package:

```terminal
composer require nascentafrica/eloquent-repository
```

### Laravel

#### >= laravel5.5

ServiceProvider will be attached automatically

#### Other

In your `config/app.php` add `NascentAfrica\EloquentRepository\RepositoryServiceProvider::class` to the end of the `providers` array:

```php
'providers' => [
    ...
    NascentAfrica\EloquentRepository\RepositoryServiceProvider::class,
],
```

If Lumen

```php
$app->register(NascentAfrica\EloquentRepository\LumenRepositoryServiceProvider::class);
```

Publish Configuration

```shell
php artisan vendor:publish --provider "NascentAfrica\EloquentRepository\Providers\RepositoryServiceProvider"
```

## Methods

### NascentAfrica\EloquentRepository\Contracts\RepositoryInterface

- all($columns = array('*'))
- first($columns = array('*'))
- paginate($limit = null, $columns = ['*'])
- find($id, $columns = ['*'])
- findByField($field, $value, $columns = ['*'])
- findWhere(array $where, $columns = ['*'])
- findWhereIn($field, array $where, $columns = [*])
- findWhereNotIn($field, array $where, $columns = [*])
- findWhereBetween($field, array $where, $columns = [*])
- count()
- countWhere(array $where = [], $columns = '*'): int
- create(array $attributes)
- update(array $attributes, $id)
- updateOrCreate(array $attributes, array $values = [])
- delete($id)
- deleteWhere(array $where)
- orderBy($column, $direction = 'asc');
- with(array $relations);
- has(string $relation);
- whereHas(string $relation, closure $closure);
- hidden(array $fields);
- visible(array $fields);
- scopeQuery(Closure $scope);
- getFieldsSearchable();
- setPresenter($presenter);
- skipPresenter($status = true);


### NascentAfrica\EloquentRepository\Contracts\RepositoryCriteriaInterface

- pushCriteria($criteria)
- popCriteria($criteria)
- getCriteria()
- getByCriteria(CriteriaInterface $criteria)
- skipCriteria($status = true)
- getFieldsSearchable()

### NascentAfrica\EloquentRepository\Contracts\CacheableInterface

- setCacheRepository(CacheRepository $repository)
- getCacheRepository()
- getCacheKey($method, $args = null)
- getCacheMinutes()
- skipCache($status = true)

### NascentAfrica\EloquentRepository\Contracts\CriteriaInterface

- apply($model, RepositoryInterface $repository);


## Usage

### Create a Model

Create your model normally, but it is important to define the attributes that can be filled from the input form data.

```php
namespace App;

class Post extends Eloquent { // or Ardent, Or any other Model Class

    protected $fillable = [
        'title',
        'author',
        ...
     ];

     ...
}
```

### Create a Repository

```php
namespace App;

use NascentAfrica\EloquentRepository\BaseRepository;

class PostRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "App\\Post";
    }
}
```

### Generators

Create your repositories easily through the generator.

#### Config

You must first configure the storage location of the repository files. By default is the "app" folder and the namespace "App". Please note that, values in the `namespaces` array are actually used as both *namespace* and file paths.

```php
    ...
    'generator'  => [
        'basePath'      => app()->path(),
        'rootNamespace' => 'App\\',
        'namespaces'       => [
            'repositories' => '\Repositories',
            'interfaces'   => '\Contracts\Repositories',
            'criteria'     => '\Criteria',
            'providers'    => '\Providers',
            'models'        => '',
        ],

        'provider'  => 'RepositoryServiceProvider'
    ]
```

You may want to save the root of your project folder out of the app and add another namespace, for example

```php
    ...
     'generator'=>[
        'basePath'      => base_path('src/Lorem'),
        'rootNamespace' => 'Lorem\\'
    ]
```

Additionally, you may wish to customize where your generated classes end up being saved.  That can be accomplished by editing the `paths` node to your liking.  For example:

```php
    ...
    'generator'  => [
        'basePath'      => app()->path(),
        'rootNamespace' => 'App\\',
        'namespaces'       => [
            'repositories' => '\Repositories',
            'interfaces'   => '\Repositories',
            'criteria'     => '\Criteria',
            'providers'    => '\Providers',
            'models'        => '\Models',
        ],

        'provider'  => 'RepositoryServiceProvider'
    ]
```

#### Commands

To generate everything you need for your Model, run this command:

```terminal
php artisan na:repository PostRepository
```

This will create the Model if it does not exist, the Repository and the interface classes.
It will also create a new `RepositoryServiceProvider` if it does not exist that will be used to bind the Eloquent Repository with its corresponding Repository Interface.
To load it, just add this to your AppServiceProvider@register method:

```php
    $this->app->register(RepositoryServiceProvider::class);
```

When running the command, you will be creating the "Entities" folder and "Repositories" inside the folder that you set as the default.

Now that is done, you still need to bind its interface for your real repository, for example in your own Repositories Service Provider.

```php
App::bind('{YOUR_NAMESPACE}Repositories\PostRepository', '{YOUR_NAMESPACE}Repositories\PostRepositoryEloquent');
```

And use

```php
public function __construct({YOUR_NAMESPACE}Repositories\PostRepository $repository){
    $this->repository = $repository;
}
```

Find all results in Repository

```php
$posts = $this->repository->all();
```

Find all results in Repository with pagination

```php
$posts = $this->repository->paginate($limit = null, $columns = ['*']);
```

Find by result by id

```php
$post = $this->repository->find($id);
```

Hiding attributes of the model

```php
$post = $this->repository->hidden(['country_id'])->find($id);
```

Showing only specific attributes of the model

```php
$post = $this->repository->visible(['id', 'state_id'])->find($id);
```

Loading the Model relationships

```php
$post = $this->repository->with(['state'])->find($id);
```

Find by result by field name

```php
$posts = $this->repository->findByField('country_id','15');
```

Find by result by multiple fields

```php
$posts = $this->repository->findWhere([
    //Default Condition =
    'state_id'=>'10',
    'country_id'=>'15',
    //Custom Condition
    ['columnName','>','10']
]);
```

Find by result by multiple values in one field

```php
$posts = $this->repository->findWhereIn('id', [1,2,3,4,5]);
```

Find by result by excluding multiple values in one field

```php
$posts = $this->repository->findWhereNotIn('id', [6,7,8,9,10]);
```

Find all using custom scope

```php
$posts = $this->repository->scopeQuery(function($query){
    return $query->orderBy('sort_order','asc');
})->all();
```

Create new entry in Repository

```php
$post = $this->repository->create( Input::all() );
```

Update entry in Repository

```php
$post = $this->repository->update( Input::all(), $id );
```

Delete entry in Repository

```php
$this->repository->delete($id)
```

Delete entry in Repository by multiple fields

```php
$this->repository->deleteWhere([
    //Default Condition =
    'state_id'=>'10',
    'country_id'=>'15',
])
```

### Create a Criteria

#### Using the command

```terminal
php artisan na:criteria MyCriteria
```

Criteria are a way to change the repository of the query by applying specific conditions according to your needs. You can add multiple Criteria in your repository.

```php

use NascentAfrica\EloquentRepository\Contracts\RepositoryInterface;
use NascentAfrica\EloquentRepository\Contracts\CriteriaInterface;

class MyCriteria implements CriteriaInterface {

    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->where('user_id','=', Auth::user()->id );
        return $model;
    }
}
```

### Using the Criteria in a Controller

```php

namespace App\Http\Controllers;

use App\PostRepository;

class PostsController extends BaseController {

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(PostRepository $repository){
        $this->repository = $repository;
    }


    public function index()
    {
        $this->repository->pushCriteria(new MyCriteria1());
        $this->repository->pushCriteria(MyCriteria2::class);
        $posts = $this->repository->all();
		...
    }

}
```

Getting results from Criteria

```php
$posts = $this->repository->getByCriteria(new MyCriteria());
```

Setting the default Criteria in Repository

```php
use NascentAfrica\EloquentRepository\Eloquent\BaseRepository;

class PostRepository extends BaseRepository {

    public function boot(){
        $this->pushCriteria(new MyCriteria());
        // or
        $this->pushCriteria(AnotherCriteria::class);
        ...
    }

    function model(){
       return "App\\Post";
    }
}
```

### Skip criteria defined in the repository

Use `skipCriteria` before any other chaining method

```php
$posts = $this->repository->skipCriteria()->all();
```

### Popping criteria

Use `popCriteria` to remove a criteria

```php
$this->repository->popCriteria(new Criteria1());
// or
$this->repository->popCriteria(Criteria1::class);
```


### Using the RequestCriteria

RequestCriteria is a standard Criteria implementation. It enables filters to perform in the repository from parameters sent in the request.

You can perform a dynamic search, filter the data and customize the queries.

To use the Criteria in your repository, you can add a new criteria in the boot method of your repository, or directly use in your controller, in order to filter out only a few requests.

#### Enabling in your Repository

```php
use NascentAfrica\EloquentRepository\BaseRepository;
use NascentAfrica\EloquentRepository\Criteria\RequestCriteria;


class PostRepository extends BaseRepository {

	/**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email'
    ];

    public function boot(){
        $this->pushCriteria(app('NascentAfrica\EloquentRepository\Criteria\RequestCriteria'));
        ...
    }

    function model(){
       return "App\\Post";
    }
}
```

Remember, you need to define which fields from the model can be searchable.

In your repository set **$fieldSearchable** with the name of the fields to be searchable or a relation to fields.

```php
protected $fieldSearchable = [
	'name',
	'email',
	'product.name'
];
```

You can set the type of condition which will be used to perform the query, the default condition is "**=**"

```php
protected $fieldSearchable = [
	'name'=>'like',
	'email', // Default Condition "="
	'your_field'=>'condition'
];
```


#### Enabling in your Controller

```php
	public function index()
    {
        $this->repository->pushCriteria(app('NascentAfrica\EloquentRepository\Criteria\RequestCriteria'));
        $posts = $this->repository->all();
		...
    }
```

#### Example the Criteria

Request all data without filter by request

`http://localhost/users`

```json
[
    {
        "id": 1,
        "name": "John Doe",
        "email": "john@gmail.com",
        "created_at": "-0001-11-30 00:00:00",
        "updated_at": "-0001-11-30 00:00:00"
    },
    {
        "id": 2,
        "name": "Lorem Ipsum",
        "email": "lorem@ipsum.com",
        "created_at": "-0001-11-30 00:00:00",
        "updated_at": "-0001-11-30 00:00:00"
    },
    {
        "id": 3,
        "name": "Laravel",
        "email": "laravel@gmail.com",
        "created_at": "-0001-11-30 00:00:00",
        "updated_at": "-0001-11-30 00:00:00"
    }
]
```

Conducting research in the repository

`http://localhost/users?search=John%20Doe`

or

`http://localhost/users?search=John&searchFields=name:like`

or

`http://localhost/users?search=john@gmail.com&searchFields=email:=`

or

`http://localhost/users?search=name:John Doe;email:john@gmail.com`

or

`http://localhost/users?search=name:John;email:john@gmail.com&searchFields=name:like;email:=`

```json
[
    {
        "id": 1,
        "name": "John Doe",
        "email": "john@gmail.com",
        "created_at": "-0001-11-30 00:00:00",
        "updated_at": "-0001-11-30 00:00:00"
    }
]
```

By default RequestCriteria makes its queries using the **OR** comparison operator for each query parameter.
`http://localhost/users?search=age:17;email:john@gmail.com`

The above example will execute the following query:
``` sql
SELECT * FROM users WHERE age = 17 OR email = 'john@gmail.com';
```

In order for it to query using the **AND**, pass the *searchJoin* parameter as shown below:

`http://localhost/users?search=age:17;email:john@gmail.com&searchJoin=and`





Filtering fields

`http://localhost/users?filter=id;name`

```json
[
    {
        "id": 1,
        "name": "John Doe"
    },
    {
        "id": 2,
        "name": "Lorem Ipsum"
    },
    {
        "id": 3,
        "name": "Laravel"
    }
]
```

Sorting the results

`http://localhost/users?filter=id;name&orderBy=id&sortedBy=desc`

```json
[
    {
        "id": 3,
        "name": "Laravel"
    },
    {
        "id": 2,
        "name": "Lorem Ipsum"
    },
    {
        "id": 1,
        "name": "John Doe"
    }
]
```

Sorting through related tables

`http://localhost/users?orderBy=posts|title&sortedBy=desc`

Query will have something like this

```sql
...
INNER JOIN posts ON users.post_id = posts.id
...
ORDER BY title
...
```

`http://localhost/users?orderBy=posts:custom_id|posts.title&sortedBy=desc`

Query will have something like this

```sql
...
INNER JOIN posts ON users.custom_id = posts.id
...
ORDER BY posts.title
...
```


Add relationship

`http://localhost/users?with=groups`



#### Overwrite params name

You can change the name of the parameters in the configuration file **config/repository.php**

### Cache

Add a layer of cache easily to your repository

#### Cache Usage

Implements the interface CacheableInterface and use CacheableRepository Trait.

```php
use NascentAfrica\EloquentRepository\BaseRepository;
use NascentAfrica\EloquentRepository\Contracts\CacheableInterface;
use NascentAfrica\EloquentRepository\Traits\CacheableRepository;

class PostRepository extends BaseRepository implements CacheableInterface {

    use CacheableRepository;

    ...
}
```

Done , done that your repository will be cached , and the repository cache is cleared whenever an item is created, modified or deleted.

#### Cache Config

You can change the cache settings in the file *config/repository.php* and also directly on your repository.

*config/repository.php*

```php
'cache'=>[
    //Enable or disable cache repositories
    'enabled'   => true,

    //Lifetime of cache
    'minutes'   => 30,

    //Repository Cache, implementation Illuminate\Contracts\Cache\Repository
    'repository'=> 'cache',

    //Sets clearing the cache
    'clean'     => [
        //Enable, disable clearing the cache on changes
        'enabled' => true,

        'on' => [
            //Enable, disable clearing the cache when you create an item
            'create'=>true,

            //Enable, disable clearing the cache when upgrading an item
            'update'=>true,

            //Enable, disable clearing the cache when you delete an item
            'delete'=>true,
        ]
    ],
    'params' => [
        //Request parameter that will be used to bypass the cache repository
        'skipCache'=>'skipCache'
    ],
    'allowed'=>[
        //Allow caching only for some methods
        'only'  =>null,

        //Allow caching for all available methods, except
        'except'=>null
    ],
],
```

It is possible to override these settings directly in the repository.

```php
use NascentAfrica\EloquentRepository\BaseRepository;
use NascentAfrica\EloquentRepository\Contracts\CacheableInterface;
use NascentAfrica\EloquentRepository\Traits\CacheableRepository;

class PostRepository extends BaseRepository implements CacheableInterface {

    // Setting the lifetime of the cache to a repository specifically
    protected $cacheMinutes = 90;

    protected $cacheOnly = ['all', ...];
    //or
    protected $cacheExcept = ['find', ...];

    use CacheableRepository;

    ...
}
```

The cacheable methods are : all, paginate, find, findByField, findWhere, getByCriteria

## License
This software is released under The MIT License (MIT).
