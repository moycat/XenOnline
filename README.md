MoyOJ - 开源的信息学竞赛在线评测系统
======================

![MoyOJ](https://raw.githubusercontent.com/moycat/MoyOJ/master/MoyOJ.png)

MoyOJ（将）是一个美观、实用、强大的Online Judge，并且完全开源。

主要特性：推送式、分布式评测，主题、插件的二次开发、缓存提速、Docker沙盒。

主要技术：PHP/HHVM、MongoDB、Redis、Docker、Socket。

更多信息和文档请访问本项目的[Wiki](https://github.com/moycat/MoyOJ/wiki)(有待补全)。

Demo: [MoyOJ](https://moyoj.xyz/)

新的开发计划——Yuki
-----------

虽然已存在的版本基本能跑起来，但我还是决定重写，因为现在的样子真是太挫了……

这次重写，Web方面我将使用Laravel框架。Client Server应该还是Workerman框架，至于评测端应该不会有太大变动。

原先的版本可以在两个archive分支中找到。

高能预警
-----------

有坑先声明，作为一个苦逼天朝高中生的一员，不会有太多的时间更新……

尤其是在这么一个悲惨的寒假之后……

\_(:3 」∠ )_

未来的Feature
-----------

**纯属脑洞，请勿指望**

1. 多种编程语言的支持
1. 手机客户端

本程序使用/借鉴的开源项目
-----------

本项目部分代码和思路来自一些开源项目，感谢他们！

~~[PyMySQL](https://github.com/PyMySQL/PyMySQL)：提供Python环境下的MySQL访问支持~~（已弃用此方案）

[hustoj](https://github.com/zhblue/hustoj)：使用了其判断返回值的函数，借鉴了部分其他函数和数据库结构

[WordPress](https://wordpress.org/)：WP的网站结构不错～插件和主题系统的思路亦来自此

[Workerman](http://www.workerman.net/)：开源的PHP Socket框架

[Editor.md](https://pandao.github.io/editor.md/)：开源的Markdown在线编辑器
