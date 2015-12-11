MoyOJ - 开源的信息学竞赛在线评测系统
======================

![MoyOJ](https://raw.githubusercontent.com/moycat/MoyOJ/master/MoyOJ.png)

MoyOJ（将）是一个美观、实用、强大的Online Judge，并且完全开源。

其中部分代码和思路来自其他开源项目，感谢他们！

然而这是一个坑
-----------

有坑先声明，作为一个苦逼天朝高中生的一员，不会有太多的时间更新……

正如班主任所说，心比天高，命比纸薄。然而生命不止，挖坑不息 \_(:3 」∠ )_

安装说明
-----------

都没写完，木有安装说明…… (つд⊂)

当前的TODO
-----------

 1. 优化数据库结构
 1. 优化Web与Python程序
 1. 优化代码风格
 1. 编写前端Web界面

 **当前大改中**
 1. 实现单个评测端多线程评测(传递虚参数以识别)
 2. 评测端和主服务器改用socket连接，采用推送+扫描遗漏派发任务，以降低数据库负载、提升评测端命中率。这将导致主服务器必须为独立服务器/VPS
 3. 添加memcached支持，同时可切换为文件缓存

未来的Feature
-----------

**纯属脑洞，请勿指望**

1. 多种编程语言的支持
1. 手机客户端
 
构架实现情况
-----------

- [X] 判题后端
    
    - [X] 判题/通信程序 `Python` `Scoekt`
    
    - [X] 沙盒环境 `Docker`
    
    - [X] 编译/运行/控制程序 `C++`
    
- [ ] 网页前端 `PHP` `CSS+JS`

    - [X] 提交功能
    
    - [ ] 用户功能
    
    - [ ] 美观的界面

    - [ ] 后台管理

- [X] 数据库 `MySQL`

本程序使用/借鉴的开源项目
-----------

~~[PyMySQL](https://github.com/PyMySQL/PyMySQL)：提供Python环境下的MySQL访问支持~~已弃用此方案

[hustoj](https://github.com/zhblue/hustoj)：使用了部分代码，借鉴了数据库结构

[WordPress](https://wordpress.org/)：WP的网站结构不错～

[Workerman](http://www.workerman.net/)：开源的PHP Socket框架
