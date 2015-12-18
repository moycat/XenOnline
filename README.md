MoyOJ - 开源的信息学竞赛在线评测系统
======================

![MoyOJ](https://raw.githubusercontent.com/moycat/MoyOJ/master/MoyOJ.png)

MoyOJ（将）是一个美观、实用、强大的Online Judge，并且完全开源。

MoyOJ支持分布式评测，评测端与主服务器通过socket连接，推送式获取提交，减轻数据库负担，支持memcached缓存以进一步提高性能。

MoyOJ带有主题、插件功能，降低了二次开发的难度。如果不满意自带主题，你可以自己编写主题，调用MoyOJ的API即可。

更多信息和文档请访问本项目的[Wiki](https://github.com/moycat/MoyOJ/wiki)(有待补全)。

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
 1. 优化Web封装，改进界面，编写后台
 1. 发现BUG，完善程序

未来的Feature
-----------

**纯属脑洞，请勿指望**

1. 多种编程语言的支持
1. 手机客户端
 
构架实现情况
-----------

- [X] 判题后端
    
    - [X] 判题/通信程序 `Python`
    
    - [X] 沙盒环境 `Docker`
    
    - [X] 编译/运行/控制程序 `C++`
    
- [ ] 网页前端 `PHP` `CSS+JS`

    - [X] 提交功能
    
    - [ ] 用户功能
    
    - [ ] 美观的界面

    - [ ] 后台管理

- [X] 提交分发服务端 `PHP`

- [X] 数据库 `MySQL`

本程序使用/借鉴的开源项目
-----------

~~[PyMySQL](https://github.com/PyMySQL/PyMySQL)：提供Python环境下的MySQL访问支持~~（已弃用此方案）

[hustoj](https://github.com/zhblue/hustoj)：使用了其判断返回值的函数，借鉴了数据库结构

[WordPress](https://wordpress.org/)：WP的网站结构不错～插件和主题系统的思路亦来自此

[Workerman](http://www.workerman.net/)：开源的PHP Socket框架
