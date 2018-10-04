<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use Hash;

// dd(Request::get('age'));
// dd(Request::has('username'));
// dd(Request::all());
class User extends Model
{
    public function signup(){
        $username = Request::get('username');
        $password = Request::get('password');
        // 检查用户名和密码是否为空
        if (!($username && $password)) {
            return ['status'=>0,'msg'=>'用户名和密码都不可为空'];
        }
        // 检查用户名是否存在
        $user_exists = $this
        ->where('username',$username)
        ->exists();
        if ($user_exists) {
            return ['status'=>0,'msg'=>'用户名已存在'];
        }
        // 将密码加密
        $hashed_password = Hash::make($password);
        // 存入数据库
        $user = $this;
        $user->password = $hashed_password;
        $user->username = $username;
        if($user->save()){
            return ['status'=>1,'id'=>$user->id];
        }else {
            return ['status'=>0,'msg'=>'db insert failed'];
        }

    }
}
