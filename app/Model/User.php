<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 27 Aug 2019 15:39:22 +0800.
 */

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\ModelCache\Cacheable;

/**
 * Class User
 *
 * @property int    $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @package App\Model
 */
class User extends Model
{
    use SoftDeletes;
    use Cacheable;

    protected $hidden
        = [
            'password'
        ];

    protected $fillable
        = [
            'name',
            'email',
            'password'
        ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    public function setPasswordAttribute($value)
    {
        // 坑, 由于php 之前版本的 bcrypt 算法存在bug, php提出了 2y 版本, 但是不被其他社区认可, 为了兼容java,替换版本号
        // 参考文档：https://en.wikipedia.org/wiki/Bcrypt
        $this->attributes['password']
            = str_replace('$2y$', '$2a$', password_hash($value, PASSWORD_BCRYPT));
    }
}
