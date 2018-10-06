<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    public function add()
    {
        /*用户未登录*/
        if (!user_ins()->is_logged_in()) {
            return ['status' => 0, 'msg' => 'login require'];
        }

        /*没有评论内容*/
        if (!rq('content')) {
            return ['status' => 0, 'msg' => 'empty content'];
        }

        /*问题评论和回答评论参数有且只能有一个*/
        if ((!rq('question_id') && (!rq('answer_id'))) ||
            (rq('question_id') && rq('answer_id'))) {
            return ['status' => 0, 'msg' => 'question_id or answer_id required'];
        }

        if (rq('question_id')) {
            /*对问题的评论*/
            $question = question_ins()->find(rq('question_id'));
            if (!$question) {
                return ['status' => 0, 'msg' => 'question not exists'];
            }
            $this->question_id = rq('question_id');
        } else {
            /*对回答的评论*/
            $answer = answer_ins()->find(rq('answer_id'));
            if (!$answer) {
                return ['status' => 0, 'msg' => 'answer not exists'];
            }
            $this->answer_id = rq('answer_id');
        }
        /*检查是否引用其他评论*/
        if (rq('reply_to')) {
            $target = comment_ins()->find(rq('reply_to'));
            if (!$target) {
                return ['status' => 0, 'msg' => 'comment not exists'];
            }
//            /*不能回复自己的评论*/
//            if ($target->user_id == session('user_id')){
//                return ['status'=>0,'msg'=>"can't reply yourself" ];
//            }
            $this->reply_to = rq('reply_to');
        }
        /*存储评论内容*/
        $this->content = rq('content');
        $this->user_id = session('user_id');
        return $this->save() ?
            ['status' => 1, 'id' => $this->id] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }

    public function read()
    {
        if (!rq('question_id') && !rq('answer_id')) {
            return ['status' => 0, 'question_id or answer_id is required'];
        }
        if (rq('question_id')) {
            $question = question_ins()->find(rq('question_id'));
            if (!$question) {
                return ['status' => 0, 'question not exists'];
            }
            $data = $this->where('question_id', rq('question_id'));
        } else {
            $answer = answer_ins()->find(rq('answer_id'));
            if (!$answer) {
                return ['status' => 0, 'answer not exists'];
            }
            $data = $this->where('answer_id', rq('answer_id'));
        }

        $data = $data->get()->keyBy('id');
        return ['status' => 1, 'data' => $data];
    }

    public function remove()
    {
        /*用户未登录*/
        if (!user_ins()->is_logged_in()) {
            return ['status' => 0, 'msg' => 'login require'];
        }
        /*判断是否传入评论id*/
        if (!rq('id')) {
            return ['status' => 0, 'msg' => 'id is required'];
        }
        /*找到对应的评论*/
        $comment = $this->find(rq('id'));
        if (!$comment) {
            return ['status' => 0, 'msg' => 'comment not exists'];
        }
        /*判断是否拥有权限*/
        if ($comment->user_id != session('user_id')) {
            return ['status' => 0, 'msg' => 'permission denied'];
        }
//        注意:此处如果2引用1,3引用2,那么删除1的时候不会删除3,只会删除2
        $this->where('reply_to', rq('id'))->delete();
        return $comment->delete() ?
            ['status' => 1] :
            ['status' => 0, 'msg' => 'db delete failed'];
    }
}
