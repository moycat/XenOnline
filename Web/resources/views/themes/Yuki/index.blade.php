<!doctype html>
<html lang="zh-CN" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="/static/html/bootstrap.min.css">
    <link href="/static/html/flat-ui.min.css" rel="stylesheet">
    <link href="/static/html/ionicons.min.css" rel="stylesheet">
    <link href="/static/html/Yuki/common.css" rel="stylesheet">
    @yield('extra_css')
    <script src="/static/html/jquery.min.js"></script>
    <script src="/static/html/bootstrap.min.js"></script>
    <script src="/static/html/js.cookie.min.js"></script>
    <script src="/static/html/stickUp.min.js"></script>
    <script src="/static/html/Yuki/common.js"></script>
    @yield('extra_js')
<!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    @if (isset($ready_js))
    <script type="text/javascript">
        jQuery(function($) {
            $(document).ready( function() {
                {!! $ready_js !!}
            });
        });
    </script>
    @endif
</head>
<body>
<div id="Yuki">
    <nav class="left-nav">
        <ul>
            <li href="/">{{ $siteName }}
                <span class="glyphicon glyphicon-home" aria-hidden="true" href="/"></span></li>
            <li href="/problem">题库
                <span class="glyphicon glyphicon-pencil" aria-hidden="true" href="/"></span></li>
            <li href="/solution">提交
                <span class="glyphicon glyphicon-check" aria-hidden="true" href="/"></span></li>
            <li href="/user">用户
                <span class="glyphicon glyphicon-user" aria-hidden="true" href="/"></span></li>
        </ul>
        <div class="sidebar">
            @section('sidebar')
            @show
        </div>
    </nav>
    <div class="openNav" onclick="toggleMenu()">
        <div class="icon"></div>
    </div>
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="card">
                    <p><strong>问题：</strong></p>
                    <p>根据某个流行的理论，一切事物（比如宇宙、空间、时间、能量、物质）都是从“虚无”中产生的。如果时间和空间都不存在，那么这个“虚无”到底是个什么东东？我不是学物理的，但我对这个问题很好奇。</p>
                    <p><span id="more-41"></span></p>
                    <p><strong>回答1：Matthew Kramer-LaPadula，作家，设计师，工程师，哲学家</strong></p>
                    <p>从理论上说，“虚无”的确存在。“虚无”这个概念，也可以称之为0或者空集，是现代数学集合论的根基。在20世纪中期，库尔特·哥德尔（Kurt Godel）证明了其著名的哥德尔不完备定理。这个定理和其他的一些理论共同表明，对于数论中任何一个给定的公理系统，都有那么些<strong>虽然真实且存在，但其真实性或存在性永远无法证明</strong>的东西。我经常把这个结论当成一个非常深奥的哲学真理——那就是世上可能有一些东西，它们确实存在，但我们永远也不能发现它们的存在，因为它们很神奇地在我们的观察能力或理解能力之外。</p>
                    <p>“虚无”就像这样的东西。要证明“虚无”并不只是一种假设出来的理论概念，就必须要有一个观察者来见证它。但是如果有那么一个“观察者”存在，“虚无”肯定就不存在了，因为已经有了“观察者”了。这里需要注意，虽然我说的是“观察者”，但并不代表那一定是一个人类。</p>
                    <p>也许这个古老的问题你会更熟悉一些：“<a href="https://zh.wikipedia.org/wiki/%E5%81%87%E5%A6%82%E4%B8%80%E6%A3%B5%E6%A8%B9%E5%9C%A8%E6%A3%AE%E6%9E%97%E8%A3%A1%E5%80%92%E4%B8%8B">假如一棵树在森林里倒下而没有人在附近听见，它有没有发出声音？</a>”我们不得而知。物理学会回答说是的，它的确发出了声音，因为树倒下的冲击力产生了压力波，这种波就是“声波”的实质。然而问题在于，“声音”是一种我们用听力“观察”而得来的体验，所以这个问题可以被归结为“<strong>如果声音没有被听到，那么它还存在吗？</strong>”压力波是有，但是没有观察者的“声音”是毫无意义的。</p>
                    <p>我认为“虚无”的情况正像这样，没有观察者，“虚无”只能是一种理论性的。但有了观察者，“虚无”是不可能存在的。</p>
                    <p>尽管如此，我们还是可以把“虚无”当成某种确实存在的东西玩味一番。但是从哲学观点来看，我认为我们永远都不会明确得知“虚无”到底是否存在。</p>
                    <p>&nbsp;</p>
                    <p><strong>回答2：Tim Othy，临床学硕士生</strong></p>
                    <p>你听说过<a href="https://zh.wikipedia.org/wiki/%E8%8A%9D%E8%AF%BA%E6%82%96%E8%AE%BA">芝诺悖论</a>吗？这个悖论有助于让你理解“虚无”和“无限”。</p>
                    <p>它是这样的：想象你要从A点到达B点。在你到达B点之前，你首先必须到达A、B的中点，我们称之为C点。但是要到达C点，你又需要先到达A、C的中点，我们称之为D点。但是要到达D点，你又需要先到达A、D的中点……</p>
                    <p>现在你肯定知道出什么问题了。如果你继续分下去，你会发现你无法到达任何一个地方！</p>
                    <p>“无限”和“虚无”就像这样。从定义上来说，你可以无限地逼近它们，但是你永远也无法真正地达到它们。</p>
                    <p>另一种类似的情况是测量海岸线。假设你测出一个岛屿的海岸线是60千米。但是你随即发现测量的精确度只到了千米，那么肯定还有一些小的湾口的海岸线不包含在内，因为它们的长度小于1千米。当你加入这些湾口的海岸线后，你发现海岸线的长度成了60400米，比先前测得的60千米要长。</p>
                    <p>但是等等！测量精确到米意味着还是有一些小的弧线因为不到1米长，而没有被计算在内。而你为了得到一个<strong>真实</strong>的海岸线长度，你把这些也算了进去。于是你以厘米的精度得到了海岸线的长度为6040700厘米——又比你之前得到的60400米要长。</p>
                    <p>然后怎样呢？只要你继续用更精确的单位去测量海岸线，它就会不断地增长，逼近无限。</p>
                    <p>现在用这个方式来计算你的预期寿命，如果你不断增加测量单位的精确度，你会发现你永远都死不了。</p>
                    <p>现在懂了吧？你是永生的。于是你可以毫无愧疚感地把所有的时间浪费在刷Quora上了……</p>
                    <p>&nbsp;</p>
                    <p><em>末影喵 via <a href="https://www.quora.com/What-is-nothing-2" target="_blank">Quora</a></em></p>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="row">
                <div class="col-sm-3">
                    <h4>
                        <span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>
                        评测机们 <small>Judgers</small>
                    </h4>
                </div>
                <div class="col-sm-3">
                    <h4>
                        <span class="glyphicon glyphicon-blackboard" aria-hidden="true"></span>
                        站点公告 <small>News</small>
                    </h4>
                </div>
                <div class="col-sm-6">
                    <h4>
                        <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>
                        {{ $siteName }} <small>About</small>
                        @section('about')
                        @show
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <a class="gotop" onclick="gotop()" href="#"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a>
</div>
</body>
</html>