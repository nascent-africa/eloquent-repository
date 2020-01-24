<?php

namespace Test;


use App\User;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use NascentAfrica\EloquentRepository\BaseRepository;

/**
 * Description of RepositoryTestCase
 *
 * @author Anitche Chisom
 */
class RepositoryTestCase extends BaseTestCase
{
    protected $container;

    protected $repository;

    /**
     * @var array
     */
    protected $data = [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'secret'
    ];

    /**
     *
     * @var \Test\NascentAfrica\EloquentRepository\Database
     */
    protected $database;

    public function __construct()
    {
        // fwrite(STDERR, print_r($query, TRUE));
        parent::__construct();

        $this->container = new Container;

        $this->repository = $this->container->make(\App\UserRepository::class);
    }

    /**
     * Test to for a successful instantiation.
     *
     * @test
     */
    public function repositoryInstantiation()
    {
        $this->assertInstanceOf(BaseRepository::class, $this->repository);
    }

    /**
     * Test "all" method.
     *
     * @test
     */
    public function allMethod()
    {
        $users = factory(User::class, 3)->create();

        $query = $this->repository->all(['*']);

        $this->assertInstanceOf(Collection::class, $query);
        $this->assertEquals($users->toArray(), $query->toArray());
    }

    /**
     * Test "count" method.
     *
     * @test
     */
    public function countMethod()
    {
        factory(User::class, 3)->create();

        $result = $this->repository->count();

        $this->assertEquals($result, 3);
    }

    /**
     * Test "countWhere" method.
     *
     * @test
     */
    public function countWhereMethod()
    {
        factory(User::class, 2)->create();

        $result = $this->repository->countWhere(['id' => 2], ['id']);

        $this->assertEquals($result, 1);
    }

    /**
     * Test "create" method.
     *
     * @test
     */
    public function createMethod()
    {
        Event::fake();

        $user = $this->repository->create($this->data);

        // Assert an event was dispatched twice...
        Event::assertDispatched(\NascentAfrica\EloquentRepository\Events\RepositoryEntityCreated::class);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertDatabaseHas('users', $this->data);
    }

    /**
     * Test delete using integer type
     *
     * @test
     */
    public function deleteWithInegerMethod()
    {
        factory(User::class)->create();

        Event::fake();

        $result = $this->repository->delete(1);

        // Assert an event was dispatched twice...
        Event::assertDispatched(\NascentAfrica\EloquentRepository\Events\RepositoryEntityDeleted::class);

        $this->assertEquals($result, 1);
        $this->assertDatabaseMissing('users', $this->data);
    }

    /**
     * Test delete using Eloquent Model type
     *
     * @test
     */
    public function deleteMithModelMethod()
    {
        factory(User::class)->create();

        Event::fake();

        $result = $this->repository->delete($this->repository->find(1));

        // Assert an event was dispatched twice...
        Event::assertDispatched(\NascentAfrica\EloquentRepository\Events\RepositoryEntityDeleted::class);

        $this->assertEquals($result, 1);
        $this->assertDatabaseMissing('users', $this->data);
    }

    /**
     * Test deleteWhere method.
     *
     * @test
     */
    public function deleteWhereMethod()
    {
        $user = factory(User::class)->create();

        Event::fake();

        $result = $this->repository->deleteWhere(['email' => $user->email]);

        // Assert an event was dispatched twice...
        Event::assertDispatched(\NascentAfrica\EloquentRepository\Events\RepositoryEntityDeleted::class);

        $this->assertEquals($result, 1);
        $this->assertDatabaseMissing('users', $this->data);
    }

    /**
     * Test "find" method.
     *
     * @test
     */
    public function findMethod()
    {
        $factoryUser = factory(User::class)->create();

        $repositoryUser = $this->repository->find(1);

        $this->assertInstanceOf(Model::class, $repositoryUser);
        $this->assertEquals($repositoryUser->id, $factoryUser->id);
    }

    /**
     * Test "findByField" method.
     *
     * @test
     */
    public function findByFieldMethod()
    {
        $factoryUser = factory(User::class)->create();

        $query = $this->repository->findByField('email', $factoryUser->email);

        $this->assertInstanceOf(Collection::class, $query);
        $this->assertEquals($query->first()->email, $factoryUser->email);
    }

    /**
     * Test "findWhere" method.
     *
     * @test
     */
    public function findWhereMethod()
    {
        $factoryUser = factory(User::class)->create();

        $query = $this->repository->findWhere(['email' => $factoryUser->email]);

        $this->assertInstanceOf(Collection::class, $query);
        $this->assertEquals($query->first()->email, $factoryUser->email);
    }

    /**
     * Test "findWhereIn" method.
     *
     * @test
     */
    public function findWhereIn()
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
     * Test "findWhereNotIn" method.
     *
     * @test
     */
    public function findWhereNotIn()
    {
        factory(User::class, 4)->create();

        $userCollection = $this->repository->findWhereNotIn('id', [1, 2]);

        $this->assertInstanceOf(Collection::class, $userCollection);

        $numberCollection = collect([1,2]);

        foreach ($userCollection as $user) {

            $this->assertFalse($numberCollection->contains($user->id));
        }
    }

    /**
     * Test findWhereBetween method.
     *
     * @test
     */
    public function findWhereBetween()
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
     * Test "first" method.
     *
     * @test
     */
    public function firstMethod()
    {
        $factoryUser = factory(User::class)->create();

        $user = $this->repository->first();

        $this->assertInstanceOf(Model::class, $user);
        $this->assertEquals($factoryUser->id, $user->id);
    }

    /**
     * Test "firstOrNew" method.
     *
     * @test
     */
    public function firstOrNewMethod()
    {
        $user = $this->repository->firstOrNew($this->data);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertDatabaseMissing('users', $this->data);
    }

    /**
     * Test "firstOrCreate" method.
     *
     * @test
     */
    public function firstOrCreateMethod()
    {
        $user = $this->repository->firstOrCreate($this->data);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertDatabaseHas('users', $this->data);
    }

    /**
     * Test "get" method.
     *
     * @test
     */
    public function getMethod()
    {
        $factoryUser = factory(User::class, 3)->create();

        $repositoryUsers = $this->repository->get(['*']);

        $this->assertInstanceOf(Collection::class, $repositoryUsers);

        $this->assertEquals($repositoryUsers->count(), 3);
        $this->assertEquals($repositoryUsers->toArray(), $factoryUser->toArray());
    }

    /**
     * Test "getByCriteria" method.
     *
     * @test
     */
    public function getByCriteriaMethod()
    {
        factory(User::class, 3)->create();

        $repositoryUser = $this->repository->getByCriteria(new \App\Criteria);

        $this->assertInstanceOf(Collection::class, $repositoryUser);

        $this->assertEquals($repositoryUser->first()->id, 2);
    }

    /**
     * Test "paginate" method.
     *
     * @test
     */
    public function paginateMethod()
    {
        factory(User::class, 5)->create();

        $query = $this->repository->paginate(3);

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $query);

        $this->assertEquals($query->count(), 3);
    }

    /**
     * Test "simplePaginate" method.
     *
     * @test
     */
    public function simplePaginationMethod()
    {
        factory(User::class, 5)->create();

        $query = $this->repository->simplePaginate(3);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\Paginator::class, $query);

        $this->assertEquals($query->count(), 3);
    }

    /**
     * Test "update" method.
     *
     * @test
     */
    public function updateMethod()
    {
        $factoryUser = factory(User::class)->create();

        Event::fake();

        $user = $this->repository->update($factoryUser->id, ['name' => 'Mark Willie']);

        // Assert an event was dispatched twice...
        Event::assertDispatched(\NascentAfrica\EloquentRepository\Events\RepositoryEntityUpdated::class);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertEquals($user->name, 'Mark Willie');
    }

    /**
     * Test repository update method.
     *
     * @test
     */
    public function updateOrCreateMethod()
    {
        $factoryUser = factory(User::class)->create();

        Event::fake();

        $user = $this->repository->updateOrCreate([
            'name' => $factoryUser->name, 'email' => $factoryUser->email
            ], ['password' => 'secret']);

        // Assert an event was dispatched twice...
        Event::assertDispatched(\NascentAfrica\EloquentRepository\Events\RepositoryEntityUpdated::class);

        $this->assertInstanceOf(Model::class, $user);
        $this->assertDatabaseHas('users', ['name' => $user->name]);
        $this->assertEquals($user->password, 'secret');
    }
}
