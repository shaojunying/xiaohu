# xiaohu

## 常规api调用规则
- 所有api都以`domain.com/api/...`开头
- api分为两部分,如`domain.com/api/part_1/part_2`
	- `part_1`为model名称,如user或question
	- `part_2`为行为名称,如reset_password
- CRUD
	- 每个model中都会有增删改查四个方法,分别对应`add`,`remove`,`change`,`read`
## model
### Question

#### 字段解释
- `id`
- `title`:标题
- `desc`:描述

#### `add`
- 权限: 已登录
- 传参:
	- 必填: `title`
	- 可选: `desc`

#### `change`
- 权限:已登录且为问题所有者
- 传参:
	- 必填: `id`
	- 可选: `title`,`desc`
## 一些笔记
- 项目的运行
    - php -S localhost:8080 -t public
- php使用migrate创建数据库
    - php artisan make:migration create_table_users --create=comments 方法创建一个用户表
    - php ../composer.phar dumpautoload 方法自动加载所有的类
    - php artisan migrate 执行新创建的方法
    - php artisan migrate:rollback 回滚到上一次执行
- 创建用户表对应的类
    - 注意此处User对应数据库中的users表
    - php artisan make:model User
- 清除session中参数的三个方法
    - $username = session()->pull('username');
    - session()->forget('username');
    - session()->put('username',null);
- 使用angular的template可以实现只刷新下半部分二不刷新导航栏
