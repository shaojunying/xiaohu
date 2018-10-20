;(function () {
    'use strict';
    angular.module("xiaohu",['ui.router',])
    // 因为laravel和angular都使用{}来访问变量,冲突
    //    所以改变angular访问变量的方式
        .config(['$interpolateProvider',
            '$stateProvider',
            '$urlRouterProvider',
            function ($interpolateProvider,
                          $stateProvider,
                          $urlRouterProvider) {
            $interpolateProvider.startSymbol("[:");
            $interpolateProvider.endSymbol(":]");

            $urlRouterProvider.otherwise('/home');

            $stateProvider
                //定义首页
                .state('home',{
                    url:'/home',
                    templateUrl:"home.tpl"
                })
                .state('login',{
                    url:'/login',
                    templateUrl:'login.tpl'
                })
                .state('signup',{
                    url:'/signup',
                    templateUrl:'signup.tpl'
                })
                .state('question',{
                    abstract:true,
                    url:'/question',
                    template:'<div ui-view></div>'
                })
                .state('question.add',{
                    url:'/add',
                    templateUrl:'question.add.tpl'
                })
        }])

        .service('UserService',[
            '$state',
            '$http',
            function ($state,$http) {
                let me = this;

                //注册操作
                me.signup_data={};
                me.signup=function () {
                    $http.post('/api/user',me.signup_data)
                        .then(function (r) {
                            if (r.data.status){
                                me.signup_data={};
                                $state.go('login');
                            }
                        },function (e) {
                            console.log('e',e);
                        })
                };

                //登录操作
                me.login_data={};
                me.login=function(){
                    $http.post('api/login',me.login_data)
                        .then(function (r) {
                            if (r.data.status){
                                $state.go('home');
                            }
                            else {
                                me.login_failed = true;
                            }
                        })
                };

                //判断用户名是否存在
                me.username_exists = function () {
                    $http.post('/api/user/exists',
                        {username:me.signup_data.username})
                        .then(function (r) {
                            if (r.data.status && r.data.count){
                                me.signup_username_exists=true;
                            } else {
                                me.signup_username_exists=false;
                            }
                        },function (e) {
                            console.log('e',e);
                        })
                }
            }
        ])

        //将注册的命名空间与服务对象绑定
        .controller('SignupController',[
            '$scope',
            'UserService',
            function ($scope,UserService) {
            $scope.User = UserService;
            //参数1: 要监控的对象
            //参数2: 当监控的对象发生变化时进行的操作
            //参数3: 设为true表示循环地监视,否则只监视一次
            $scope.$watch(function () {
                return UserService.signup_data;
            },function (n, o) {
                if (n.username != o.username) {
                    UserService.username_exists();
                }
            },true)
        }])

//    将登录的命名空间与服务对象绑定
        .controller('LoginController',[
            '$scope',
            'UserService',
            function ($scope,UserService) {
                $scope.User=UserService;
            }
            ])

        .service('QuestionService',[
            '$state',
            '$http',
            function ($state,$http) {
                let me = this;
                me.new_question = {};
                me.go_add_question = function () {
                    $state.go('question.add')
                };
                me.add=function () {
                    if (!me.new_question.title){
                        return;
                    }

                    $http.post('/api/question/add',me.new_question)
                        .then(function (r) {
                            if (r.data.status){
                                me.new_question = {};
                                $state.go('home');
                            }
                        },function (e) {
                            console.log('e',e);
                        })
                }
            }
        ])
        .controller('QuestionAddController',[
            '$scope',
            'QuestionService',
            function ($scope,QuestionService) {
                $scope.Question = QuestionService;
            }
        ])
})();
