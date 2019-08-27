<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 27 Aug 2019 15:39:22 +0800.
 */

namespace App\Model;

use Carbon\Carbon;

/**
 * Class UserRole
 * 
 * @property int $id 
 * @property int $user_id 
 * @property int $role_id 
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Model
 */
class UserRole extends Model
{
	protected $table = 'user_role';

	protected $casts = [
		'user_id' => 'int',
		'role_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'role_id'
	];
}
