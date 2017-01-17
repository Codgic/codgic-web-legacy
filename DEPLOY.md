# 部署 CWOJ

## 安装依赖项
CWOJ 需要你安装以下项目来完成依赖项的构建：
* node.js ( >= 6.0 )
* webpack ( >= 2.0 )

如果你使用 Ubuntu，你可以使用如下操作来完成安装：
```bash
curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash - # 这会将 node.js 的软件库添加至你的系统

curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list # 这会将 yarn 的软件仓库添加至系统。

sudo apt update
sudo apt install -y nodejs yarn
sudo npm install webpack -g
```

## 下载并安装程序包
你需要快速、稳定的 **国际** 互联网连接。
```
# 在项目根目录
yarn # 下载依赖项
webpack # 发布脚本到 web 目录
```

## 部署
（坑）
