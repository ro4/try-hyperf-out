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
 * Class Role
 * 
 * @property int $id 
 * @property string $name 
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @package App\Model
 */
class Role extends Model
{
	use SoftDeletes, Cacheable;

	protected $fillable = [
		'name'
	];
}
