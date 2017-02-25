# 部署 CWOJ

## 安装依赖项
CWOJ 需要你安装以下项目来完成依赖项的构建：
* node.js ( >= 6.0 )
* webpack ( >= 2.0 )
* composer ( >= 1.3 )

其中，composer 用于安装 Mathjax。

如果你使用 Ubuntu，你可以使用如下操作来完成安装：
```bash
curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash - # 这会将 node.js 的软件库添加至你的系统

curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list # 这会将 yarn 的软件仓库添加至系统。

sudo apt update
sudo apt install -y nodejs yarn
sudo npm install webpack -g

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

## 下载并安装程序包
你需要快速、稳定的 **国际** 互联网连接。

```
# 在项目根目录
yarn # 下载依赖项
webpack # 发布脚本到 web 目录

# 假设你的 composer 可执行文件名为 `composer.phar`
composer.phar install
```

## 部署
复制 web 文件夹内的所有内容至 `/var/www/codgic`，然后将公共目录设为 `/var/www/codgic/public`。
将 web/config/config_example.php 复制到 web/config/config.php，然后编辑配置文件内容。
无需拷贝 web 文件夹以外的内容。
