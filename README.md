1. composer install 
2. yarn install 
3. 创建数据库 yayang
4. 复制 .env.example 为 .env
5. 修改 .env 对应配置项
6. php artisan jwt:secret
5. php artisan migrate:install 
6. php artisan db:seed

7. 当要重置数据时执行  php artisan migrate:refresh  php artisan db:seed

8.前端资源编译
yarn run watch 

9.后端资源编译
yarn run watch --env=admin

10.各环境编译请参考
package.json 中命令


选品 初次/二次  可见自己创建的
选品审核 初次审核/二次审核 可见全部
美工选品 可见全部审核通过选品
美工上图 自己选品上传
产品审核 所有美工完成选品
产品分配 所有美工完成产品 进行分配
产品库 所有产品可见 默认显示审核通过产品
店铺产品库 店铺可见