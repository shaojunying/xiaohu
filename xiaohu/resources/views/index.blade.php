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
<div class="navbar">
    <a href="" ui-sref="home">主页</a>
    <a href="" ui-sref="login">登录</a>
</div>
<div>
    <div ui-view></div>
</div>
</body>
<script type="text/ng-template" id="home.tpl">
<div>
    <h1>首页</h1>
</div>
</script>
<script type="text/ng-template" id="login.tpl">
    <div>
        <h1>登录</h1>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid autem, dolores ea enim impedit, laborum laudantium minus nesciunt nulla qui ratione suscipit tenetur veniam! Cumque deleniti dignissimos eos voluptate voluptatum!
    </div>
</script>
</html>
