<?php

namespace Test;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Event;
use NascentAfrica\EloquentRepository\BaseRepository;
use NascentAfrica\EloquentRepository\Contracts\RepositoryInterface;
use Faker\Generator as Faker;
use NascentAfrica\EloquentRepository\Events\RepositoryEntityCreated;
use NascentAfrica\EloquentRepository\Events\RepositoryEntityDeleted;
use NascentAfrica\EloquentRepository\Events\RepositoryEntityUpdated;
use NascentAfrica\EloquentRepository\Exceptions\EloquentRepositoryException;

class UserRepository extends BaseRepository implements RepositoryInterface
{
    // fwrite(STDERR, print_r($query, TRUE));
    public function model(): string {return User::class;}
};

/**
 * Class BaseRepositoryTest
 *
 * @package Test
 * @author Anitche Chisom
 */
class BaseRepositoryTest extends TestCase
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var Faker
     */
    protected $faker;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = $this->app->make(Faker::class);
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->withFactories(__DIR__.'/database/factories');

        $this->repository = $this->app->make(UserRepository::class);
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testFindWhereBetween()
    {
        factory(User::class, 4)->create();

        $userCollection = $this->repository->findWhereBetween('id', [1, 2]);

        $this->assertInstanceOf(Collection::class, $userCollection);

        $trueNumberCollection = collect([1, 2]);

        foreach ($userCollection as $user) {
            $this->assertTrue($trueNumberCollection->contains($user->id));
        }

        $falseNumberCollection = collect([3, 4]);

        foreach ($userCollection as $user) {
            $this->assertFalse($falseNumberCollection->contains($user->id));
        }
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testAll()
    {
        $collection = $this->repository->all();
        $this->assertInstanceOf(Collection::class, $collection);
    }

//    public function testSyncWithoutDetaching()
//    {
//
//    }
//
//    public function testGetFieldsSearchable()
//    {
//
//    }
//
//    public function testWhereHas()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testGet()
    {
        $collection = $this->repository->get();
        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testCreate()
    {
        Event::fake();

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];

        $user = $this->repository->create($data);

        // Assert an event was dispatched twice...
        Event::assertDispatched(RepositoryEntityCreated::class);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertDatabaseHas('users', $data);
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testGetByCriteria()
    {
        factory(User::class, 3)->create();

        $repositoryUser = $this->repository->getByCriteria(new \Test\Criteria);

        $this->assertInstanceOf(Collection::class, $repositoryUser);

        $this->assertEquals(2, $repositoryUser->first()->id);
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testFirst()
    {
        $factoryUser = factory(User::class)->create();

        $user = $this->repository->first();

        $this->assertInstanceOf(Model::class, $user);
        $this->assertEquals($factoryUser->id, $user->id);
    }

//    public function testLimit()
//    {
//
//    }
//
//    public function testScopeQuery()
//    {
//
//    }
//
//    public function testVisible()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testCount()
    {
        factory(User::class, 3)->create();

        $result = $this->repository->count();

        $this->assertEquals(3, $result);
    }

//    public function testSync()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testDeleteWhere()
    {
        $user = factory(User::class)->create();

        Event::fake();

        $result = $this->repository->deleteWhere(['email' => $user->email]);

        // Assert an event was dispatched twice...
        Event::assertDispatched(RepositoryEntityDeleted::class);

        $this->assertEquals(1, $result);
        $this->assertDatabaseMissing('users', $user->toArray());
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testDeleteWithModelMethod()
    {
        factory(User::class, 5)->create();

        Event::fake();

        $result = $this->repository->delete($this->repository->find(5));

        // Assert an event was dispatched twice...
        Event::assertDispatched(RepositoryEntityDeleted::class);

        $this->assertEquals(1, $result);
        $this->assertDatabaseMissing('users', ['id' => 5]);
    }

//    public function testResetScope()
//    {
//
//    }
//
//    public function testWithTrashed()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testFindByField()
    {
        $factoryUser = factory(User::class)->create();

        $query = $this->repository->findByField('email', $factoryUser->email);

        $this->assertInstanceOf(Collection::class, $query);
        $this->assertEquals($query->first()->email, $factoryUser->email);
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testFindWhere()
    {
        $factoryUser = factory(User::class)->create();

        $query = $this->repository->findWhere(['email' => $factoryUser->email]);

        $this->assertInstanceOf(Collection::class, $query);
        $this->assertEquals($factoryUser->email, $query->first()->email);
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testFindWhereNotIn()
    {
        factory(User::class, 4)->create();

        $userCollection = $this->repository->findWhereNotIn('id', [1, 2]);

        $this->assertInstanceOf(Collection::class, $userCollection);

        $numberCollection = collect([1,2]);

        foreach ($userCollection as $user) {
            $this->assertFalse($numberCollection->contains($user->id));
        }
    }

//    public function testResetModel()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testFind()
    {
        $factoryUser = factory(User::class)->create();

        $repositoryUser = $this->repository->find(1);

        $this->assertInstanceOf(Model::class, $repositoryUser);
        $this->assertEquals($factoryUser->id, $repositoryUser->id);
    }

//    public function testHidden()
//    {
//
//    }
//
//    public function testWith()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testFindWhereIn()
    {
        factory(User::class, 3)->create();

        $userCollection = $this->repository->findWhereIn('id', [1, 2, 3]);

        $this->assertInstanceOf(Collection::class, $userCollection);

        $numberCollection = collect([1,2,3]);

        foreach ($userCollection as $user) {
            $this->assertTrue($numberCollection->contains($user->id));
        }
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testFirstOrCreate()
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];

        $user = $this->repository->firstOrCreate($data);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertDatabaseHas('users', $data);
    }

//    public function testGetCriteria()
//    {
//
//    }
//
//    public function testHas()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testPaginate()
    {
        factory(User::class, 5)->create();

        $query = $this->repository->paginate(3);

        $this->assertInstanceOf(LengthAwarePaginator::class, $query);

        $this->assertEquals(3, $query->count());
    }

//    public function testOnlyTrashed()
//    {
//
//    }
//
//    public function testPushCriteria()
//    {
//
//    }
//
//    public function testOrderBy()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testUpdateOrCreate()
    {
        $factoryUser = factory(User::class)->make();

        Event::fake();

        $user = $this->repository->updateOrCreate([
            'name' => $factoryUser->name, 'email' => $factoryUser->email
        ], ['password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi']);

        // Assert an event was dispatched twice...
        Event::assertDispatched(RepositoryEntityUpdated::class);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertDatabaseHas('users', ['name' => $user->name]);
    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testCountWhere()
    {
        factory(User::class, 2)->create();

        $result = $this->repository->countWhere(['id' => 2]);

        $this->assertEquals(1, $result);
    }

//    public function testResetCriteria()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testFirstOrNew()
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];

        $user = $this->repository->firstOrNew($data);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertDatabaseMissing('users', $data);
    }

//    public function testPopCriteria()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testSimplePaginate()
    {
        factory(User::class, 5)->create();

        $query = $this->repository->simplePaginate(3);

        $this->assertInstanceOf(Paginator::class, $query);

        $this->assertEquals(3, $query->count());
    }

//    public function testWithCount()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testUpdate()
    {
        $factoryUser = factory(User::class)->create();

        Event::fake();

        $user = $this->repository->update($factoryUser->id, ['name' => 'Mark Willie']);

        // Assert an event was dispatched twice...
        Event::assertDispatched(RepositoryEntityUpdated::class);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertEquals('Mark Willie', $user->name);
    }

//    public function testPluck()
//    {
//
//    }
//
//    public function testSetModel()
//    {
//
//    }

    /**
     * @throws BindingResolutionException
     * @throws EloquentRepositoryException
     */
    public function testDelete()
    {
        factory(User::class)->create();

        Event::fake();

        $result = $this->repository->delete(1);

        // Assert an event was dispatched twice...
        Event::assertDispatched(RepositoryEntityDeleted::class);

        $this->assertEquals(1, $result);
        $this->assertDatabaseMissing('users', ['id' => 1]);
    }

//    public function testSkipCriteria()
//    {
//
//    }
}
