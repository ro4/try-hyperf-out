<?php


namespace App\Task;


use App\Model\User;
use Hyperf\Crontab\Annotation\Crontab;

/**
 * @Crontab(name="show-user", rule="* * * * *", callback="showUser", memo="这是一个示例的定时任务")
 * Class UserTask
 *
 * @package App\Task
 */
class UserTask
{
    public function showUser()
    {
        var_dump('定时任务');
    }
}