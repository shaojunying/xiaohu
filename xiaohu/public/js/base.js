;(function () {
    'use strict';
    angular.module("xiaohu",['ui.router',])
    // 因为laravel和angular都使用{}来访问变量,冲突
    //    所以改变angular访问变量的方式
        .config(function ($interpolateProvider,
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
        })

        // .controller('TestController',function ($scope) {
        //     $scope.name="shaojunying";
        // })
})();
