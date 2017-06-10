# Codgic Laverne (1.0)

![Codgic](https://raw.githubusercontent.com/Codgic/codgic-web-legacy/master/web/public/assets/res/codgic.png)

**注：我们正在计划重写Codgic，故当前的Codgic将在未来被放弃。我们会继续对Codgic中的bug进行修复，但不会再增加新功能。**

Codgic（原名CWOJ）是一个免费、开源并且专为信息竞赛训练设计的在线评测系统解决方案。 自2015年11月20日以来，我们一直致力于寻求一个适合于个人、学校及其它教育机构的在线评测系统解决方案。

Laverne，电影《疯狂动物城》中主人公Judy Hopps的中间名，代表着一种尝试一切不畏失败的精神。 它给予了我在困难甚至不可能中取得突破的动力。

注： 1.x仍然处于早期开发阶段，意味着它包含大量只写了不到一半的代码并且只适用于测试用途。
​     
## 分支
- master: 当前开发分支 (1.0-Laverne)。
- stable-0.x: 稳定版本分支 (0.x)。

## 部署
- 评测端在[这里](https://github.com/CDFLS/cwoj_daemon)。
- 请参见 DEPLOY.md。

## 许可
- Codgic主要基于[Bashu OnlineJudge](https://github.com/593141477/bashu-onlinejudge).
- Codgic也引用了一下其它的开源项目, 其中大部分都基于MIT协议。

## 特性
相比原始的 Bashu Online Judge, 我们做了如下提升:
- [x] <b>完全兼容PHP7</b>
- [x] 多语言支持 (Alpha) + UX细节升级
- [x] 针对移动设备优化 + 升级到Bootstrap 3。
- [x] 日间/夜间模式 + 自动切换
- [x] CodeMirror代码编辑器 + Prism代码语法高亮。
- [x] 密码找回    
- [x] 新闻中心
- [x] Gravatar头像
- [x] 用户在线/离线状态    
- [x] 重写的用户权限管理  
- [x] 比赛 (Beta) 
- [ ] 百科 (未完成)
