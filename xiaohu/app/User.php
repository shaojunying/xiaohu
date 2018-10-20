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
            return error('用户名和密码都不可为空');
        }
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];
        // 检查用户名是否存在
        $user_exists = $this
        ->where('username',$username)
        ->exists();
        if ($user_exists) {
            return error('用户名已存在');
        }
        // 将密码加密
        $hashed_password = Hash::make($password);
        // 存入数据库
        $user = $this;
        $user->password = $hashed_password;
        $user->username = $username;
        if($user->save()){
            return success(["id"=>$user->id]);
        }else {
            return error('db insert failed');
        }
    }

    /*获取用户信息api*/
    public function read(){
        if (!rq('id')){
            return error('required id');
        }

        $get = ['id','username',"avatar_id_url","intro"];
        $user = $this->find(rq('id'),$get);
        $data = $user->toArray();
        $answer_count = answer_ins()->where('user_id',rq('id'))->count();
        $question_count = question_ins()->where('user_id',rq('id'))->count();
//        $answer_count = $user->answer()->count();
        $data['answer_count']=$answer_count;
        $data['question_count']=$question_count;
        return success(["data"=>$data]);
    }

    // 登录api
    public function login()
    {
        // 检查用户名和密码是否为空
        $has_username_and_password = $this->has_username_and_password();
        if (!($has_username_and_password)) {
            return error('用户名和密码都不可为空');
        }
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];

//        检查用户是否存在
        $user = $this->where('username',$username)->first();
        if(!$user){
            return error('用户不存在');
        }
//        检查密码
        $hashed_password = $user->password;
        if (!Hash::check($password,$hashed_password)){
            return error('密码有误');
        }

//        将用户信息写入session
        session()->put('username',$username);
        session()->put('user_id',$user->id);

        return success(["id"=>$user->id]);

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
        return success();
    }

    /*更改密码api*/
    public function change_password(){
        /*检查用户是否登录*/
        if (!$this->is_logged_in()){
            return error('login is required');
        }
        /*检查用户是否输入了旧密码和新密码*/
        if (!rq('old_password')||!rq('new_password')){
            return error('old_password and new_password is required');
        }

        /*获取当前用户对象*/
        $user = $this->find(session('user_id'));

        /*检查用户输入的旧密码是否正确*/
        if (!Hash::check(rq('old_password'),$user->password)){
            return error('invalid old_password');
        }
        $user->password = bcrypt(rq('new_password'));
        $user->save();
        return success();
    }

    /*检测用户是否登录*/
    public function is_logged_in(){
        /*如果session中存在user_id就返回user_id,否则返回false*/
        return session('user_id')?:false;
    }

    public function answer(){
        return $this
            ->belongsToMany("App\Answer")
            ->withPivot("vote")
            ->withTimestamps();
    }

    /*重设密码api*/
    /**
     * @return array
     */
    public function reset_password()
    {
        /*之前没有发送记录，直接发送验证码*/
        if ($this->is_robot()){
            return error("max frequency reached");
        }

        /*检查是否传入手机号*/
        if (!rq('phone')){
            return error('phone is required');
        }
        $user = $this->where('phone',rq('phone'))->first();
        if (!$user){
            return error('invalid phone number');
        }
        $captcha = $this->generate_captcha();
        $user->phone_captcha = $captcha;
        if ($user->save())
        {
            $this->send_sms();
            $this->update_robot_time();
            return success();
        }
        return error("insert database false");
    }

    public function is_robot($time=10){
        if (!session('last_sms_time')){
            return false;
        }
        $current_time = time();
        $last_sms_time = session('last_sms_time');

        /*短信接口调用过于频繁*/
        return $current_time-$last_sms_time < $time;
    }
    public function update_robot_time(){
        /*方便下一次做检查*/
        session()->put('last_sms_time',time());
    }

    /*验证输入的短信验证码api*/
    public function validate_reset_password(){

        if ($this->is_robot(2)){
            return error("max frequency reached");
        }

        if (!rq('phone')||!rq('phone_captcha')||!rq('new_password')){
            return error('phone, new_password and phone_captcha are required');
        }

        $user = $this->where([
            'phone'=>rq('phone'),
            'phone_captcha'=>rq('phone_captcha')
        ])->first();
        if (!$user){
            return error('invalid phone or invalid phone_captcha');
        }
        $user->password = bcrypt(rq('new_password'));

        if ($user->save())
        {
            $this->update_robot_time();
            return success();
        }
        return error("update database false");
    }
    /*发送短信验证码*/
    public function send_sms(){
        return true;
    }
    /*生成验证码*/
    public function generate_captcha(){
        return rand(1000,9999);
    }

    public function exists()
    {
        return success(['count'=>$this->where(rq())->count()]);
    }
}
