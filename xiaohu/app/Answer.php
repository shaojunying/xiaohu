<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    /*添加回答*/
    public function add()
    {
        /*用户未登录*/
        if (!user_ins()->is_logged_in()) {
            return ['status' => 0, 'msg' => 'login require'];
        }
        /*必须包含用户id和回答内容*/
        if (!rq('question_id') || !rq('content')) {
            return ['status' => 0, 'msg' => 'question_id and content is required'];
        }
        /*判断是否存在当前问题*/
        $question = question_ins()->find(rq('question_id'));
        if (!$question) {
            return ['status' => 0, 'msg' => 'question not exists'];
        }
        /*判断用户是否回答过此问题,如果回答过就禁止用户再次回答*/
        $result = $this
            ->where(['question_id' => rq('question_id'), 'user_id' => rq('user_id')])
            ->count();
        if (!$result) {
            return ['status' => 0, 'msg' => 'duplicate answers'];
        }

        $this->content = rq('content');
        $this->question_id = rq('question_id');
        $this->user_id = session('user_id');

        return $this->save() ?
            ['status' => 1, 'id' => $this->id] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }

    public function change()
    {
        /*判断用户是否登录*/
        if (!user_ins()->is_logged_in()) {
            return ['status' => 0, 'msg' => 'login required'];
        }
        /*必须包含用户id和回答内容*/
        if (!rq('id') || !rq('content')) {
            return ['status' => 0, 'msg' => 'id and content is required'];
        }
        $answer = $this->find(rq('id'));
        if (!$answer) {
            return ['status' => 0, 'msg' => 'answer not exists'];
        }
        $answer->content = rq('content');
        return $answer->save() ?
            ['status' => 1] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }

    public function read()
    {
        /*用户指定id的时候只返回一条回答*/
        if (rq('id')) {
            $answer = $this->find(rq('id'));
            if (!$answer) {
                return ['status' => 0, 'msg' => 'answer not exists'];
            }
            return ['status' => 1, 'data' => $this->all()];
        }
        /*检查是否指定每页回答数量*/
        $limit = rq('limit') ?: 15;
        $skip = (rq('page') ? (rq('page') - 1) : 0) * $limit;

        /*获取指定数量的问题*/
        $result = $this
            ->orderBy('created_at')
            ->limit($limit)
            ->skip($skip)
            ->get()
            ->keyBy('id');

        return ['status' => 1, 'data' => $result];
    }

    public function remove()
    {
        /*检查用户是否登录*/
        if (!user_ins()->is_logged_in()) {
            return ['status' => 0, 'msg' => 'login required'];
        }
        /*检查id是否存在*/
        if (!rq('id')) {
            return ['status' => 0, 'msg' => 'id required'];
        }
        $answer = $this->find(rq('id'));
        /*检查问题是否存在*/
        if (!$answer) {
            return ['status' => 0, 'msg' => 'question not exists'];
        }
        /*检查登录用户是否是问题创建用户*/
        if ($answer->user_id != session('user_id')) {
            return ['status' => 0, 'msg' => 'permission denied'];
        }
        return $answer->delete() ?
            ['status' => 1] :
            ['status' => 0, 'db delete failed'];
    }
}
