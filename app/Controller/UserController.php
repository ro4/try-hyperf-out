<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Role;
use App\Model\User;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;
use Psr\Http\Message\ResponseInterface;

/**
 * @Controller(prefix="users")
 * Class UserController
 *
 * @package App\Controller
 */
class UserController extends BaseController
{

    /**
     * @GetMapping(path="")
     * @return LengthAwarePaginatorInterface
     */
    public function lists()
    {
        $query   = User::query();
        $page    = (int)$this->request->input('page', 1);
        $perPage = (int)$this->request->input('per_page', 10);
        if ($name = $this->request->input('name_fuzzy')) {
            Validators::is($name, 'string:0..30');
            $query->where('name', 'like', "%{$name}%");
        }

        if ($page) {
            Validators::is($page, 'integer:1..');
        }

        if ($perPage) {
            Validators::is($perPage, 'integer:..1000');
        }

        $query->with('roles');

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return $users;
    }

    /**
     * @PostMapping(path="")
     * @return ResponseInterface
     * @throws AssertionException
     */
    public function store()
    {
        $name     = $this->request->input('name');
        $password = $this->request->input('password');
        $email    = $this->request->input('email');
        $roleIds  = $this->request->input('role_ids');
        $params   = [];

        if ($name) {
            Validators::is($name, 'string:3..30');
            $params['name'] = $name;
        } else {
            throw new AssertionException('name is required');
        }

        if ($password) {
            Validators::is($password, 'string:6..30');
            $params['password'] = $password;
        } else {
            throw new AssertionException('password is required');
        }

        if ($email) {
            Validators::isEmail($email);
            $params['email'] = $email;
        } else {
            throw new AssertionException('email is required');
        }

        if ($roleIds) {
            Validators::is($roleIds, 'array');
        }
        /** @var User $user */
        $user = Db::transaction(function () use ($params, $roleIds) {
            /** @var User $user */
            $user = User::query()->create($params);

            if ($roleIds) {
                $roleIds = array_unique($roleIds);
                $user->roles()->sync(Role::findManyFromCache($roleIds));
            }

            return $user;
        });

        return $this->response->json(['id' => $user->id]);
    }

    /**
     * @GetMapping(path="{id:\d+}")
     * @param $id
     *
     * @return ResponseInterface
     */
    public function detail($id)
    {
        $user = User::query()->with('roles')->findOrFail($id);

        return $this->response->json($user->toArray());
    }

    /**
     * @PutMapping(path="{id:\d+}")
     * @param $id
     *
     * @return ResponseInterface
     */
    public function update($id)
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);

        $name     = $this->request->input('name');
        $password = $this->request->input('password');
        $email    = $this->request->input('email');
        $roleIds  = $this->request->input('role_ids');

        $params = [];

        if ($name) {
            Validators::is($name, 'string:3..30');
            $params['name'] = $name;
        }

        if ($password) {
            Validators::is($password, 'string:6..30');
            $params['password'] = $password;
        }

        if ($email) {
            Validators::isEmail($email);
            $params['email'] = $email;
        }

        if ($roleIds) {
            Validators::is($roleIds, 'array');
        }

        Db::transaction(function () use ($user, $params, $roleIds) {
            $user->update($params);
            if ($roleIds) {
                $roleIds = array_unique($roleIds);
                $user->roles()->sync(Role::findManyFromCache($roleIds));
            }
        });

        return $this->response->json(['id' => $user->id]);
    }
}
