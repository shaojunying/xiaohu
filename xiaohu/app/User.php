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
    // 注册api
    public function signup(){
        // 检查用户名和密码是否为空
        $has_username_and_password = $this->has_username_and_password();
        if (!($has_username_and_password)) {
            return ['status'=>0,'msg'=>'用户名和密码都不可为空'];
        }
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];
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
    // 登录api
    public function login()
    {
        // 检查用户名和密码是否为空
        $has_username_and_password = $this->has_username_and_password();
        if (!($has_username_and_password)) {
            return ['status'=>0,'msg'=>'用户名和密码都不可为空'];
        }
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];

//        检查用户是否存在
        $user = $this->where('username',$username)->first();
        if(!$user){
            return ['status'=>0,'msg'=>'用户不存在'];
        }
//        检查密码
        $hashed_password = $user->password;
        if (!Hash::check($password,$hashed_password)){
            return ['status'=>0,'msg'=>'密码有误'];
        }

//        将用户信息写入session
        session()->put('username',$username);
        session()->put('user_id',$user->id);

        return ['status'=>1,'id'=>$user->id];

    }

    /*判断用户名密码是否在参数中存在*/
    public function has_username_and_password(){
        $username = rq('username');
        $password = rq('password');
        // 检查用户名和密码是否为空
        if ($username && $password) {
            return [$username,$password];
        }
        else {
            return false;
        }
    }

    /*登出api*/
    public function logout(){
        /*删除username*/
        session()->forget('username');
        session()->forget('user_id');
//        return redirect('/');
        return ['status'=>1];
    }

    /*更改密码api*/
    public function change_password(){
        /*检查用户是否登录*/
        if (!$this->is_logged_in()){
            return ['status'=>0,'msg'=>'login is required'];
        }
        /*检查用户是否输入了旧密码和新密码*/
        if (!rq('old_password')||!rq('new_password')){
            return ['status'=>0,'msg'=>'old_password and new_password is required'];
        }

        /*获取当前用户对象*/
        $user = $this->find(session('user_id'));

        /*检查用户输入的旧密码是否正确*/
        if (!Hash::check(rq('old_password'),$user->password)){
            return ['status'=>0,'msg'=>'invalid old_password'];
        }
        $user->password = bcrypt(rq('new_password'));
        $user->save();
        return ['status'=>1];
    }

    /*检测用户是否登录*/
    public function is_logged_in(){
        /*如果session中存在user_id就返回user_id,否则返回false*/
        return session('user_id')?session('user_id'):false;
    }

    public function answer(){
        return $this
            ->belongsToMany("App\Answer")
            ->withPivot("vote")
            ->withTimestamps();
    }
}
