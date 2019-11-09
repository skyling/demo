1. composer install 
2. yarn install 
3. 创建数据库 demo
4. 复制 .env.example 为 .env
5. 修改 .env 对应配置项
5. php artisan migrate:install 
6. php artisan db:seed

7. 当要重置数据时执行  php artisan migrate:refresh  php artisan db:seed

8.前端资源编译
yarn run watch 

9.后端资源编译
yarn run watch --env=admin

10.各环境编译请参考
package.json 中命令