<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function add()
    {
        /*检查是否登录*/
        if (!user_ins()->is_logged_in()) {
            return error('login requred');
        }
        /*检查标题是否存在*/
        if (!rq('title')) {
            return error('title required');
        }
        $this->title = rq('title');
        $this->user_id = session('user_id');
        /*如果描述存在,就添加到数据库*/
        if (rq('desc')) {
            $this->desc = rq('desc');
        }

        return $this->save() ?
            success(["id"=>$this->id]):
            error('db insert failed');
    }

    public function change()
    {
        /*检查是否登录*/
        if (!user_ins()->is_logged_in()) {
            return error('login required');
        }
        /*检查id是否存在*/
        if (!rq('id')) {
            return error('id required');
        }
        /*寻找id对应的问题对象*/
        $question = $this->find(rq('id'));

        /*不存在该问题对象*/
        if (!$question) {
            return error('question not exists');
        }

        /*当前登录用户与问题所属用户不匹配*/
        if ($question->user_id != session('user_id')) {
            return error('permission denied');
        }
        /*修改标题*/
        if (rq('title')) {
            $question->title = rq('title');
        }
        /*修改描述*/
        if (rq('desc')) {
            $question->desc = rq('desc');
        }
        return $question->save() ?
            success() :
            error('db insert failed');
    }

    public function read()
    {
        /*检查id是否存在*/
        if (rq('id')) {
            $question = $this->find(rq('id'));
            if (!$question) {
                return error('question not exists');
            }
            return success(["data"=>$this->find(rq('id'))]);
        }
        /*检查是否指定每页问题数量*/
        $limit = rq('limit') ?: 15;
        $skip = (rq('page') ? (rq('page') - 1) : 0) * $limit;

        /*获取指定数量的问题*/
        $result = $this
            ->orderBy('created_at')
            ->limit($limit)
            ->skip($skip)
            ->get(["id", "title", "desc", "user_id", "created_at", "updated_at"])
            ->keyBy('id');

        return success(["result"=>$result]);
    }

    public function remove()
    {
        /*检查用户是否登录*/
        if (!user_ins()->is_logged_in()) {
            return error('login required');
        }
        /*检查id是否存在*/
        if (!rq('id')) {
            return error('id required');
        }
        $question = $this->find(rq('id'));
        /*检查问题是否存在*/
        if (!$question) {
            return error('question not exists');
        }
        /*检查登录用户是否是问题创建用户*/
        if ($question->user_id != session('user_id')) {
            return error('permission denied');
        }
        return $question->delete() ?
            success() :
            error('db delete failed');
    }
}
