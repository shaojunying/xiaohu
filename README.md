# xiaohu

### 一些笔记
- php使用migrate创建数据库
    - php artisan make:migration create_table_users 方法创建一个用户表
    - php ../composer.phar dumpautoload 方法自动加载所有的类
    - php artisan migrate 执行新创建的方法
    - php artisan migrate:rollback 回滚到上一次执行
- 创建用户表对应的类
    - php artisan make:model User
