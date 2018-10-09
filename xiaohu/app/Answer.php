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
            return error('login require');
        }
        /*必须包含用户id和回答内容*/
        if (!rq('question_id') || !rq('content')) {
            return error('question_id and content is required');
        }
        /*判断是否存在当前问题*/
        $question = question_ins()->find(rq('question_id'));
        if (!$question) {
            return error( 'question not exists');
        }
        /*判断用户是否回答过此问题,如果回答过就禁止用户再次回答*/
        $result = $this
            ->where(['question_id' => rq('question_id'), 'user_id' => rq('user_id')])
            ->count();
        if ($result) {
            return error( 'duplicate answers');
        }

        $this->content = rq('content');
        $this->question_id = rq('question_id');
        $this->user_id = session('user_id');

        return $this->save() ?
            success(["id"=>$this->id]) :
            error( 'db insert failed');
    }

    public function change()
    {
        /*判断用户是否登录*/
        if (!user_ins()->is_logged_in()) {
            return error('login required');
        }
        /*必须包含用户id和回答内容*/
        if (!rq('id') || !rq('content')) {
            return error('id and content is required');
        }
        $answer = $this->find(rq('id'));
        if (!$answer) {
            return error('answer not exists');
        }
        $answer->content = rq('content');
        return $answer->save() ?
            success():
            error('db insert failed');
    }

    public function read()
    {
        /*用户指定id的时候只返回一条回答*/
        if (rq('id')) {
            $answer = $this->find(rq('id'));
            if (!$answer) {
                return error( 'answer not exists');
            }
            return success(["data"=>$this->all()]);
        }
        /*检查是否指定每页回答数量*/
        list($limit,$skip) = paginate(rq('page'),rq('limit'));

        /*获取指定数量的问题*/
        $result = $this
            ->orderBy('created_at')
            ->limit($limit)
            ->skip($skip)
            ->get()
            ->keyBy('id');

        return success(["result"=>$result]);
    }

    public function remove()
    {
        /*检查用户是否登录*/
        if (!user_ins()->is_logged_in()) {
            return error( 'login required');
        }
        /*检查id是否存在*/
        if (!rq('id')) {
            return error( 'id required');
        }
        $answer = $this->find(rq('id'));
        /*检查问题是否存在*/
        if (!$answer) {
            return error( 'question not exists');
        }
        /*检查登录用户是否是问题创建用户*/
        if ($answer->user_id != session('user_id')) {
            return error( 'permission denied');
        }
        return $answer->delete() ?
            success() :
            error('db delete failed');
    }


    /*投票api*/
    public function vote(){
        if (!user_ins()->is_logged_in()){
            return error('login is required');
        }
        if (!rq('id')||!rq("vote")){
            return error('id and vote are required');
        }

        $answer = $this->find(rq('id'));
        if (!$answer){
            return error('answer not exists');
        }

        $vote = rq('vote') <= 1 ? 1:2;
        $answer->users()
            ->newPivotStatement()
            ->where('user_id',session('user_id'))
            ->where("answer_id",rq('id'))
            ->delete();

        $answer
            ->users()
            ->attach(session("user_id"),['vote'=>$vote]);

        return success();

    }

    public function users(){
        return $this
            ->belongsToMany("App\User")
            ->withPivot("vote")
            ->withTimestamps();
    }
}
