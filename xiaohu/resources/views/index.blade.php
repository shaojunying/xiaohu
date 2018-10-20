<!doctype html>
<html lang="zh" ng-app="xiaohu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/node_modules/normalize-css/normalize.css"/>
    <link rel="stylesheet" href="/css/base.css">
    <script src="/node_modules/angular/angular.js"></script>
    <script src="/node_modules/jquery/dist/jquery.js"></script>
    <script src="/node_modules/angular-ui-router/release/angular-ui-router.js"></script>
    <script src="/js/base.js"></script>
    <title>晓乎</title>
</head>
<body>
<div class="navbar clearfix">
    <div class="container">
        <div class="float-left">
            <div class="navbar-item brand">晓乎</div>
            <form ng-submit="Question.go_add_question()" ng-controller="QuestionAddController" id="quick-ask">
                <div class="navbar-item">
                    <input ng-model="Question.new_question.title" type="text">
                </div>
                <div class="navbar-item">
                    <button type="submit">提问</button>
                </div>
            </form>
        </div>
        <div class="float-right">
            <a ui-sref="home" class="navbar-item">首页</a>
            @if(is_logged_in())
                <a ui-sref="login" class="navbar-item">{{session('username')}}</a>
                <a href="{{url('/api/logout')}}" class="navbar-item">登出</a>
            @else
                <a ui-sref="login" class="navbar-item">登录</a>
                <a ui-sref="signup" class="navbar-item">注册</a>
            @endif
        </div>
    </div>
</div>
<div class="page">
    <div ui-view></div>
</div>

</body>

<script type="text/ng-template" id="home.tpl">
<div class="home container">
    主页
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi commodi consequuntur, corporis culpa distinctio esse fuga iste perspiciatis saepe. Dolorum eaque enim, exercitationem iste quasi ratione rem sit tempore voluptate.
</div>
</script>

<script type="text/ng-template" id="login.tpl">
    <div ng-controller="LoginController" class="login container card">
        <h1>登录</h1>
        <form name="login_form" ng-submit="User.login()">
            <div class="input-group">
                <label>用户名</label>
                <input type="text" name="username" required="required" ng-model="User.login_data.username">
            </div>
            <div class="input-group">
                <label>密码</label>
                <input type="password" name="password" required="required" ng-model="User.login_data.password">
            </div>
            <div ng-if="User.login_failed">用户名或密码有误</div>
            <div class="input-group">
                <button class="primary" type="submit"
                ng-disabled="login_form.username.$error.required ||
                             login_form.password.$error.required">
                    登录
                </button>
            </div>
        </form>
    </div>
</script>

<script type="text/ng-template" id="signup.tpl">
    <div ng-controller="SignupController" class="signup container card">
        <h1>注册</h1>
        <form name="signup_form" ng-submit="User.signup()">
            <div class="input-group">
                <label>用户名</label>
                <input name="username"
                       type="text"
                       ng-minlength="2"
                       ng-maxlength="24"
                       required="required"
                       ng-model="User.signup_data.username"
                        ng-model-options="{debounce:500}">
                <div ng-if="signup_form.username.$touched" class="input-error-set">
                    <div ng-if="signup_form.username.$error.required">用户名为必填项</div>
                    <div ng-if="signup_form.username.$error.minlength||
                                signup_form.username.$error.maxlength">用户名需要在2-24位之间</div>
                    <div ng-if="User.signup_username_exists">用户名已存在</div>
                </div>
            </div>
            <div class="input-group">
                <label>密码</label>
                <input name="password"
                       type="password"
                       ng-minlength="6"
                       ng-maxlength="255"
                       required="required"
                       ng-model="User.signup_data.password">
                <div ng-if="signup_form.password.$touched" class="input-error-set">
                    <div ng-if="signup_form.password.$error.required">密码为必填项</div>
                    <div ng-if="signup_form.password.$error.minlength||
                                signup_form.password.$error.maxlength">密码需要在6-255位之间</div>
                </div>
            </div>
            <button class="primary" type="submit"
                    ng-disabled="signup_form.$invalid"
                    >注册</button>
        </form>
    </div>
</script>

<script type="text/ng-template" id="question.add.tpl">
    <div ng-controller="QuestionAddController" class="question_add container">
        <div class="card">
            <form name="question_add_form" ng-submit="Question.add()">
                <div class="input-group">
                    <label>问题标题</label>
                    <input
                            ng-minlength="5"
                            ng-maxlength="255"
                           name="title"
                           required
                           type="text"
                           ng-model="Question.new_question.title">
                </div>
                <div class="input-group">
                    <label>问题描述</label>
                    <textarea type="text" ng-model="Question.new_question.desc"></textarea>
                </div>
                <div class="input-group">
                    <button ng-disabled="question_add_form.title.$error.required||
                    question_add_form.title.$error.minlength ||
                    question_add_form.title.$error.maxlength"
                            type="submit"
                    class="primary">提交</button>
                </div>
            </form>
        </div>

    </div>
</script>

</html>
