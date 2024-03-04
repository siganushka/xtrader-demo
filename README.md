# 如何安装？

### 克隆项目

```bash
$ git clone https://github.com/siganushka/xtrader-demo.git
$ cd ./xtrader-demo
```

### 安装项目

```bash
$ composer install
```

### 配置参数

```bash
$ composer dump-env {ENV} # ENV 为当前环境，可选为 dev, test, prod
```

> 打开 ``.env.local.php`` 文件修改项目所需参数，比如数据库信息

### 创建数据库

```bash
$ php bin/console doctrine:database:create          # 创建数据库
$ php bin/console doctrine:schema:update --force    # 创建表结构
$ php bin/console doctrine:fixtures:load            # 生成测试数据
```
