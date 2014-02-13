#第三章 AngularJS开发

现在, 我们已经探究了组成AngularJS的一些轮子. 我们已经知道用户进入我们的应用程序后如何获取数据, 如何显示文本, 以及如何做一些时髦的验证, 过滤和改变DOM. 但是我们要如何把它们组织在一起呢?

在本章, 我们将讨论以下内容:

+ 如何适应快速开发布局AngularJS应用程序
+ 启动服务器查看应用程序行为
+ 使用Karma编写和运行单元测试和场景测试
+ 针对产品部署编译和压缩你的AngularJS应用程序
+ 使用Batarang调试你的AngularJS应用程序
+ 简化开发流程(从创建文件到运行应用程序和测试)
+ 使用依赖管理库RequireJS整合AnguarJS项目

本章旨在提供一个20000英尺的视图以告诉你如何可行的布局你的AngularJS应用程序. 我们不会进入实际应用程序本身. 在第4章, 深入一个使用和展示了各种各样AngularJS特性的示例一用程序.

## 目录

- [项目组织](#项目组织)
- [工具](#工具)
- [运行你的应用程序](#运行你的应用程序)
	- [使用Yeoman](#使用yeoman)
	- [不使用Yeoman](#不使用yeoman)
- [测试AngularJS](#测试angularjs)
- [单元测试](#单元测试)
- [端到端/集成测试](#端到端集成测试)
- [编译](#编译)
- [其他优秀工具](#其他优秀工具)
	- [调试](#调试)
	- [Batarang](#batarang)
- [Yeoman: 优化你的工作流程](#yeoman-优化你的工作流程)
	- [安装Yeoman](#安装yeoman)
	- [启动一个新的AngularJS项目](#启动一个新的angularjs项目)
	- [运行服务器](#运行服务器)
	- [添加新的路由, 视图和控制器](#添加新的路由-视图和控制器)
	- [测试的故事](#测试的故事)
	- [构建项目](#构建项目)
- [使用RequireJS整合AngularJS](#使用requirejs整合angularjs)


##项目组织

推荐使用Yeoman构建你的项目, 将会为你的AngularJS应用程序创建所有必要的辅助程序文件.

Yeoman是一个包含多个框架和客户端库的强大的工具. 它通过自动化一些日常任务的引导的方式提供了一个快速的开发环境和增强你的应用程序. 本章我们会使用一个完整的小节介绍如何安装和使用Yeoman, 但是在那之前, 我们先来简单的介绍以下使用Yeoman命令替代那些手动执行的操作.

我们还详细介绍了涉及到让你决定不使用Yeoman的情况, 因为在Windows平台的计算机上Yeoman确实存在一些问题, 并且设置它还稍微有一点挑战性.

对于那些不使用Yeoman的情况, 我们将会看看一个示例应用程序结构(可以在Github示例仓库的`chapter3/sample-app`目录中找到 - [链接](https://github.com/shyamseshadri/angularjs-book/tree/master/chapter3/sample-app)), 下面是推荐的结构, 也是使用Yeoman生成的结构. 应用程序的文件可以分为以下类别:

**JS源文件**

看看`app/scripts`目录. 这里是你所有的JS源文件所在目录. 一个主文件来给你的应用程序设置Angular模块和路由.

此外, 这里还有一个单独的文件夹--`app/scripts/controller`--这里面是各个控制器. 控制器提供行为和发布数据到作用域中, 然后显示在视图中. 通常, 它们与视图都是一一对应的.

指令, 过滤器和服务也可以在`app/scripts`下面找到, 不管是否优雅和复杂, 作为一个完整的文件(direcyives.js, filters.js, services.js)或者单个的都行.

**Angular HTML模板文件**

现在, 使用Yeoman创建的每一个AngularJS局部模板都可以在`app/views`目录中找到. 这是映射到大多数`app/scripts/controller`目录中.

还有另外一个重要的Angular模板文件, 就是主要的`app/index.html`. 这用户获取AngularJS源文件, 也是你为应用程序创建的任意源文件.

如果你最终会创建一个新的JS文件, 要确保把它添加到`index.html`中, 同时还要更新的主模块和路由(Yeoman也会为你做这些).

**JS库依赖**

Yeoman在`app/scripts/vendor`中为你提供了所有的JS源文件依赖. 想在应用程序中使用[Underscore](http://underscorejs.org/)和[SocketIO](http://socket.io/)? 没问题--将依赖添加到vendor目录中(还有你的`index.html`), 并开始在你的用用程序中引用它.

**静态资源**

最终你创建了一个HTML应用程序, 它还会考虑到你的应用程序还有作为需要的CSS和图像依赖. `app/styles`和`app/img`目录就是出于这个目的而产生的. 你只需要添加你需要的东西到目录中并在你的应用程序中引用它们(当然, 要使用正确的绝对路径).

> 默认情况下Yeoman不会创建`app/img`路径.

**单元测试**

测试是非常重要的, 并且当它涉及到AngularJS时是毫不费力的. 在测试方面`test/spec`目录应该映射到你的`app/scripts`目录. 每个文件都应该有一个包含它的单元测试的spec文件映射(镜像). 种子文件会给每个控制器文件创建一个存根文件, 在`test/spec/controllers`目录下, 与原来的控制器具有相同的名称. 它们都是Jasmine风格的规范, 描述了每个控制器预期行为的规范.

**集成测试**

AngularJS自带了端对端的测试支持以正确的方式内置到库里面. 你所有的Jasmine规范形式的E2E(端对端)测试, 都保存在`tests/e2e`目录下.

> 默认情况下Yeoman不会创建`test/目录`.

> 虽然E2E测试可能看起来像Jasmine, 但实际上不是的. 它们的函数是异步执行的, 来未来, 可以通过Angular场景运行器(Angular Scenario Runner)运行. 因此不要指望能够做正常情况下Jasmine测试所做的事情(像使用console.log重复输出一个值的情况).

还生成了一个简单的HTML文件, 可以在浏览器中打开它来手动的运行测试. 然而Yeoman不会生成存根文件, 但是它们遵循相似风格的单元测试.

**配置文件**

这里需要两个配置文件. 第一个是`karma.conf.js`, 这是Yeoman为你生成的用于运行单元测试的. 第二个, 是Yeoman不会生成的`karma.e2e.conf.js`. 这用于运行场景测试. 在本场尾部的继承RequireJS一节中有一个简单的文件. 这用于配置依赖关系的详情, 同时这个文件用在你使用karma运行单元测试的时候.

你可能会问: 如何运行我的应用程序? 什么是单元测试? 甚至我该如何编写你们所讨论的这种各样的零件?

别担心, 年轻的蚱蜢, 所有的这些在适当的时间都会解释. 在这一章里面, 我们将处理设置项目和开发环境的问题, 因此一旦我们掺入一些惊人的代码, 那些问题都可以快速的带过. 你所编写的代码以及如何将它们与你最终的惊人的应用程序联系在一起的问题, 我们将在接下来的几章中讨论.

##工具

AngularJS只是你开发实际网页的工具箱的一部分. 在这一节, 我们将一起开看看一些你用以确保高效和快速开发的不同的工具, 从IDEs到测试运行器到调试工具.

###IDEs

让我们从你如何编写源代码开始. 有大量的JavaScript编辑器可以选择, 有免费的也有付费的. 长时间以来的事实证明Emacs和Vi是开发JS的最好选择. 现在, 各种IDEs都自带了语法高亮, 自动完成以及其他功能, 它给你一个选择的余地, 这可能是值得的. 那么, 应该使用那一个呢?

如果你不介意付几十块钱(尽管它有一个30天的免费试用期), [WebStorm](www.jetbrains.com/webstorm/‎)是个不错的选择, 当今时代, WebStorm由JetBrains提供了最广泛的Web开发平台. 它所具有的特性, 在之前只有强类型语言才有, 包括代码自动完成(如图3-1所示, 指定浏览器版本), 代码导航, 语法和多无高亮, 同时支持多个库和框架的启动即可使用. 此外, 它还漂亮的集成了在IDE中正确的调试JavaScript的功能, 而且这些都是基于Chrome执行的.

![ide](figure/3-1.png)

最大的你应该考虑使用WebStorm开发AngularJS原因是它是唯一继承AngularJS插件的IDEs. 这个插件支持在你的HTML模板中正确的自动完成AngularJS的HTML标签. 这些都是常用的代码片段, 否则你每次都要输入拼凑的代码片段. 因此不应该像下面这样输入:
```js
	directive('$directiveName$', function factory($injectables$){
		var directiveDefinitionObject = {
			$directiveAttr$;
			compile: function complie(tElement, tAttrs, transclude){
				$END$;
				return function(scope, elements, attrs){
					//...
				}
			}
		};
		return directiveDefinitionObject;
	});
```
在WebStorm中, 你可以只输入以下内容:
```bash
	ngdc
```
然后按`Tab`键获取同样的代码. 这只是大多数代码自动完成插件提供的功能之一.

##运行你的应用程序

现在让我们讨论如何运行所有你所做的事情 - 查看应用程序活动起来, 在浏览器中. 真实的感受以下应用程序是如何工作, 我们需要一个服务器来服务于我们的HTML和JavaScript代码. 我将探讨两种方式, 一种非常简单的方式是使用Yeoman运行应用程序, 另外一种不是很容易的不用Yeoman的方法, 但是同样很好.

###使用Yeoman

Yeoman让你很简单的使用一个Web服务器服务你所有的静态资源和相关的JavaScript文件. 只需要运行以下命令:
```bash
	yeoman server
```
它将启动一个服务器同时在你的浏览器中打开AngularJS应用程序的主页. 每当你改变你的源代码时, 它甚至会刷新(自动刷新)浏览器. 很酷不是吗?

###不使用Yeoman

如果不使用Yeoman, 你可能需要配置一个服务器来服务你所有主目录中的文件. 如果你不知道一个简单的方法做到这一点, 或者不想浪费时间创建你自己的Web服务器, 你可以在Node.js中使用ExpressJS快速的编写一个简单的Web服务器(只要简单的使用`npm install -g express`来获取它). 它可能看起来像下面这样:
```js
	//available at chapter3/sample-app/web-server.js

	var express = require('express'),
	    app = express(),
	    port = parseInt(process.env.PORT, 10) || 8080;
		app.configure(function(){
			app.use(express.methodOverride());
			app.use(express.bodyParser());
			app.use(express.static(__dirname + '/'));
			app.use(app.router);
		});

	app.listen(port);
	console.log("Now serving the app at http://localhost:" + port + "app");
```
一旦你有了这个文件, 你就可以使用Node运行这个文件, 通过使用下面的命令:
```bash
	node web-server.js
```
同时它将在8080端口启动服务器(或者你自己选择端口).

可选的, 在应用程序文件夹中使用Python你应该运行:
```bash
	python -m SimpleHTTPServer
```
无论你是否决定继续, 一旦你配置好服务器并运行起来, 都将被导航导下面的URL:
```
	http://localhost:[port-number]/app/index.html
```
然后你就可以在浏览器中查看你刚刚创建的应用程序. 注意, 你需要手动的刷新浏览器来查看改变, 不同于使用Yeoman.

##测试AngularJS

之前已经说过(甚至在本章的前面), 我们再重新说一次: 测试是必不可少的, AngularJS使编写合理的单元测试和集成测试变得很简单. 虽然AngularJS使用多个测试运行器运行的很好, 但我们坚信[Karma](http://karma-runner.github.io/0.8/index.html)胜过大多数你所需要的提供强大, 坚实和及其快速的运行器.

###Karma

Karma存在的主要的原因是它让你的测试驱动开发(TDD)流程变得简单, 快速和有趣. 它使用NodeJS和SocketIO(你不需要知道它们是什么, 只需要假设它们是很棒很酷的库), 并允许在多个浏览器中及其快速的运行你的代码和测试. 在[https://github.com/vojtajina/karma/](https://github.com/vojtajina/karma/)中可以找到更多信息.

> **TDD简介**
>
> 测试驱动开发或者TDD, 是一个通过确保在开发生命周期内首先编写测试的敏捷方法, 这是在代码实现之前进行的, 这就是测试驱动的开发(不只是作为一种验证工具).
>
> TDD的原则很简单:
>
+ 代码只需要在一个所需要的代码测试失败时编写.
+  编写最少的代码以确保测试通过.
+  在每一步删除重复代码.
+  一旦所有的测试通过, 接下来就是给下一个所需要的功能添加失败测试.
>
> 以下是确保这些原则的简单规则:
>
+ 有组织的开发你的代码, 每一行代码都要有目的的编写.
+ 你的代码仍然是高度模块化, 紧密结合和可复用的(你需要能够测试它)
+ 提供一系列的全面的测试列表, 以防后期的破坏和Bugs.
+ 测试也应该充当规范和文档, 以适应后期需要和变化.
>
> 在AngularJS中我们发现这是真的, 同时在整个AngularJS代码库中我们都是使用TDD来开发. 对于像JavaScript这样的无需编译的动态语言, 我们坚信良好的单元测试可以减轻未来的头痛.

那么, 我们如何获取迷人的Karma呢? 好吧, 首先确保在你的机器上安装了NodeJS. 它自带了NPM(Node包管理器), 这使得它易于管理和安装成千上万的NodeJS可用的库.

一旦你安装了NodeJS和NPM, 安装Karma只需要简单的运行下面的命令:
```bash
	sudo npm install -g karma
```
到这里. 你只要简单的三部来开始使用Karma(我刚才说了, 请不要了解它现实上是怎么使用的).

**获取配置文件**:

如果你是用Yeoman创建应用程序骨架, 那么你就已经有一个现成的Karma配置文件等你来使用. 如果不是, 那么继续, 并且在你的应用程序目录的根文件夹中执行下面的命令:
```bash
	karma init
```
在你的终端控制器中执行(定位到目录文件夹,然后执行命令), 他会生成一个虚拟的配置文件(`karma.conf.js`), 你可以根据你的喜好来编辑它, 它默认带有一些很好的标准. 你可以使用它.

**启动Karma服务器**

只需要运行下面的命令:
```bash
	karma start [optionalPathToConfigFile]
```
这将会在9876端口启动Karma服务器(这是默认情况, 你可以通过编辑在上一步提到的`karma.conf.js`文件来改变). 虽然Karma应该打开一个浏览器并自动捕获它, 它将在控制台中打印所有其他浏览器中捕获所需要的指令. 如果你懒得这样做, 只需要在其他浏览器或者设备中浏览`http://localhost:9876`, 并且你最好在多个浏览器中运行测试.

> 虽然Karma可以自动捕获常用的浏览器, 在启动时.(FireFox, Chrome, IE, Opera, 甚至是PhantomJS), 但它不仅限于只是这些浏览器. 任何可以浏览一个URL的设备都可能可以作为Karma运行器. 因此如果你打开你的iPhone或者Android设备上浏览器并浏览`http://machinename:9876`(只要是可访问的), 你都可能在移动设备上运行同样的测试.

**运行测试**

执行下面的命令:
```bash
	karma run
```
就是这样. 运行这个命令之后, 你应该获得正好打印在控制台中的结果. 很简单, 不是吗?

##单元测试

AngularJS是的编写单元测试变得更简单, 默认情况下支持编写[Jasmine](http://pivotal.github.io/jasmine/)风格的测试(就是Karma). Jasmine就是我们所说的行为驱动开发框架, 它允许你编写规范来说明你的代码行为应该如何表现. 一个Jasmine测试范例看起来可能是这样子的.
```js
	describe("MyController:", function(){
		it("to work correctly", function(){
			var a = 12;
			var b = a;

			expect(a).toBe(b);
			expect(a).not.toBe(null);
		});
	});
```
正如你可以看到, 它本身就是非常容易阅读的格式, 大部分的代码都可以用简单的英文来阅读理解. 它还提供了一个非常多样化和强大的匹配集合(就像`expect`从句), 当然它还有[xUnit](http://xunit.codeplex.com/)常用的`setUp`和`tearDowns`(函数在每个独立的测试用例之前或者之后执行).

AngularJS提供了一些优雅的原型, 和测试函数一样, 它允许你有在单元测试中创建服务, 控制器和过滤器的权利, 以及模拟`HTTPRequests`输出等等. 我们将在第五章讨论这个.

Karma可以使它很容易的集成到你的开发流程中, 以及在你编写的代码中获取即时的反馈.

**集成到IDEs中**

Karma并没有所有最新版和最好的(greatest)IDEs使用的插件(已经实现的还没有), 但实际上你并不需要. 所有你所需要做的就是在你的IDEs中添加一个执行"karma start"和"karma run"的快捷命令. 这通常可以通过添加一个简单的脚本来执行, 或者实际的shell命令, 依赖于你所选择的编辑器. 当然, 每次完成运行你都应该看到结果.

**在每一个变化上运行测试**

这是许多TDD开发者的理想国: 能够运行在它们所有的测试中, 每次它们按下保存, 在几毫秒之内迅速的得到返回结果. 使用AngularJS和Karma可以很容易做到这一点. 事实证明, Karma配置文件(记住就是前面的`karma.conf.js`)有一个看似无害的名为**`autoWatch`**的标志. 设置它为true来告诉Karma每次运行你的测试文件(这就是你的源代码和测试代码)都监控它的变化. 然后在你的IDE中执行"karma start", 猜猜会怎样? Karma运行结果将可供你的IDE使用. 你甚至不需要切换控制台或者终端来了解发生了什么.

##端到端/集成测试

随着应用程序的发展(或者有这个趋势, 事实上很快, 之前你甚至已经意识到这一点), 测试它们是否如预期那样工作而不需要手动的裁剪任何功能. 毕竟, 没一添加新的特性, 你不仅要验证新特性的工作, 还要验证老特性是否仍然更够正常工作, 并且没有bug和功能也没有退化. 如果你开始添加多个浏览器, 你可以很容看出, 其实这些可以变成一个组合.

AngularJS视图通过提供一个Scenario Runner来模拟用户与应用程序交互来缓解这种现象.

Scenario Runner允许你按照类Jasmine的语法来描述应用程序. 正如之前的单元测试, 我们将会有一些的`describes`(针对这个特性), 同时它还是独立(描述每个单独功能的特性). 和往常一样, 你可以有一些共同的行为, 对于执行每个规范之前和之后.(我们称之为测试).

来看一个应用程序示例, 他返回过滤器结果列表, 看起来可能像下面这样:
```js
	describe("Search Results", function(){
		beforeEach(function(){
			browser().navigateTo("http://localhost:8000/app/index.html");
		});
		it("Should filter results", function(){
			input("searchBox").enter("jacksparrow");
			element(":button").click();
			expect(repeater("ul li").count()).toEqual(10);
			input("filterText").enter("Bees");
			expect(repeater("ul li").count()).toEqual(1);
		});
	});
```
有两种方式运行这些测试. 不过, 无论使用那种方式运行它们, 你都必须有一个Web服务器来启动你的应用程序服务(请参见上一节来查看如何做到这一点). 一旦做到这一点, 可以使用下列方法之一:

1. **自动化**: Karma现在支持运行Angular情景测试. 创建一个Karma配置文件然后进行以下改变:

  a. 添加一个ANGULAR_SCENARIO & ANGULAR_SCENARIO_ADAPTER到配置的文件部分.

  b. 添加一个代理服务器将请求定位到正确的测试文件所在目录, 例如:

  	proxies = {'/': 'http://localhost:8000/test/e2e'};

  c. 添加一个Karma root(根目录/基础路径)以确保Karma的源文件不会干扰你的测试, 像这样:

  	urlRoot = '/_karma_/';

  然后只需要记得通过浏览`http://localhost:9876/_karma_`来捕捉Karma服务器, 你应该使用Karma自由的运行你的测试.

2. **手动**: 手动的方法允许你从你的Web服务器上打开一个简单的页面运行(和查看)所有的测试.

  a. 创建一个简单的`runner.html`文件, 这来源于Angular库的`angular-scenario.js`文件.

  b. 所有的JS源文件都遵循你所编写的你的场景组件部分的规范.

  c. 启动你的Web服务器, 浏览`runner.html`文件.

为什么你应该使用Angular场景运行器, 或者说是外部的第三方库活端对端的测试运行器? 使用场景运行器有令人惊讶的好处, 包括:

**AngularJS意识**

Angular场景情运行器, 顾名思义, 它是由Angular创建的. 因此, 他就是Angular aware, 它直到并理解各种各样的Angular元素, 类似绑定. 需要输入一些文本? 检查绑定的值? 验证中继器(repeater)状态? 所有的这些都可以通过场景运行器轻松的完成.

**无需随机等待**

Angular意识也意味着Angular直到所有的XHR何时向服务器放出, 从而可以避免页面加载所等待的间隔时间. 场景运行器直到何时加载一个页面, 从而比Selenium测试更具确定性, 例如, 超时等待页面加载时任务可能失败.

**调试功能**

探究JavaScript, 如果你查看你的代码不是很好; 当你希望暂停和恢复测试时, 所有的这些都运行场景测试吗? 然而所有的这一切通过Angular场景运行器都是可行的, 等等.

##编译

在JavaScript世界里, 编译通常意味着压缩代码, 虽然一些实际的编译可能使用的时Google的Closure库. 但是为么你会希望将所有漂亮的, 写的很好, 很容易理解代码变得不可读呢?

原因之一是我们的目标是是应用程序更快的响应用户. 这是为什么客户端应用程序几年前不想现在腾飞得这么快的主要原因. 能够越早获取应用程序并运行, 响应得也越早.

这种快速响应是压缩JS代码的动机. 代码越小, 越能有效的减小负载, 同时能够更快的将相关文件发送给用户. 这在移动应用程序中显得尤为重要, 因为其规模为成为瓶颈.

这里有集中方法可以压缩你给应用程序所编写的AngularJS代码, 每种方法都具有不同的效果.

**基本的和简单的优化**

这包括压缩所有在你的代码中使用的变量, 但是不会压缩属性. 这就是所谓的传递给Closure Compiler的简单优化.

者不会给你带来多大的文件大小的节省, 但是你仍然可以获得一个可观的最小开销.

这项工作的原因是编译器(Closure或者UglifyJS)并不会重命名你从模板中引用的属性.  因此, 你的模板会继续工作, 仅仅重命名局部变量和参数.

对于Google Closure, 只需简单的调用下面的命令:
```bash
	java -jar closure_compiler.js --compilation_level | SIMPLE_OPTIMIZATIONS --js path/to/files.js
```
**高级优化**

高级优化有一点棘手, 它会试图重名几乎任何东西和每个函数. 得到这个级别的优化工作, 你将需要通过显示的告诉它哪些函数, 变量和属性需要重命名(通过使用一个externsfile). 者通常是通过模板访问函数和属性.

编译将会使用这个`externs`文件, 然后重命名所有的东西. 如果处理好, 这可能会导致的你的JavaScript文件大幅度的减小, 但是它的确需要相当大的工作像, 包括每次改变代码都要更急externs文件.

要记住一件事: 当你想要压缩代码时, 你要使用依赖注入的形式(在控制器上指定`$inject`属性).

下面的代码不会工作
```js
	function MyController($scope, $resource){
		//Stuff here
	}
```
你需要像下面这样做:
```js
	function MyController($scope, $resource){
		//Some Stuff here
	}
	MyController.$inject = ['$scope', '$resource'];
```
或者是使用模块, 像这样:
```js
	myAppModule('MyController', ['$scope', '$resource', function($scope, $resource){
		//Some stuff here
	}]);
```
一旦所有的变量都混淆或者压缩只有, 这是使用Angular找出那些你最初使用的服务和变量的方式.

> 每次都是数组的方式注入是比较好的处理发方式, 以避免开始编译代码时的错误. 挠头并视图找出为什么提供的$e变量丢失了(一些任务的混淆版本压缩了它)是不值得的.

##其他优秀工具

在本节, 我们将会看一些其他有助于简化你的开发流程和提高效率的工具. 这包括使用Batarang调试真实的代码和使用Yeoman开发.

###调试

当你使用JavaScript工作时, 在浏览器中调试你的代码会成为一个习惯. 你越早接受这个事实, 对你越有好处. 值得庆幸的是, 当过去没有Firebug时, 这件事已经走过了漫长的路. 现在, 不管选择什么浏览器, 一般来说你都可以介入代码来分析错误和判断应用程序的状态. 只需要去了解Chrome和Internet Explorer的开发者工具, 能同时在FireFox和Chrome中工作的Firebug.

这里有一些帮助你进一步调试应用程序的技巧提示:

+ 永远记住, 当你希望调试引用程序时, 记得切换到非压缩版本的代码和依赖中进行. 你不仅会获得更好(可读)变量名, 也会获得代码行号和实际有用的信息以及调试功能.
+ 尽量保持你的源代码为独立的JS文件, 而不是内联在HTML中.
+ 断点调试是很有用的! 它们允许你检查你的应用程序状态, 模型, 以及给定的时间点上的所有信息.
+ "暂停所有异常"是内置在当今大多数开发者工具中的一个非常有用的选项. 当发现一个异常是调试器会终止继续运行并高亮导致异常的代码行.

###Batarang

当然, 我们有Batarang. Batarang是一个添加AngularJS知识的Chrome扩展, 它是嵌套在Google Chrome中内置开发者工具. 一旦安装(你可以从[http://bit.ly/batarangjs](http://bit.ly/batarangjs)中获取), 它就会在Chorme的开发者工具面板中添加一个AngularJS选项.

你有没有想过你的AngularJS应用程序的当前状态是什么? 当前(视图)包含的每个模型, 作用域和变量是什么? 你的应用程序性能如何?  如果你还没有想过, 相信我, 你会的. 当你需要这么做时, Batarang会为你服务.

这里有Batarang的四个主要的有用的附加功能:

####模型选项

Batarang允许你从根源向下深入探究`scope`. 然后你可以看到这些`scopes`是如何嵌套以及模型是如何与之关联的.(如图3-2所示). 你甚至可以实时的改变它们并在应用程序中查看变化的反映. 很酷, 不是吗?

![model tab](figure/3-2.png)

Figure 3-2 Model tree in Batarang

####性能选项

性能选项必须单独启用, 它会注入一些特殊的JavaScript代码到你的应用程序中. 一旦你启用它, 你就可以看到不同的作用域和模型, 并且可以在每个作用域执行所有的性能监控表达式(如图3-3所示). 随着你使用应用程序, 性能也会得到更新, 因此它可以很好的实时工作.

![perforemance tab](figure/3-3.png)

Figure 3-3. Performance tab in Batarang

####服务依赖

对于一个简单的应用程序, 不会超过1-2个控制器和服务依赖. 但是事实上, 全面的应用程序, 如果没有工具支持, 服务依赖管理会成为噩梦. 幸好这里有Batarang可以给你提供服务, 填补这个洞, 因为它给你提供了一个干净, 简单的方法来查看可视化的服务依赖图表(如图3-4所示).

![service dependencies](figure/3-4.png)

Figure 3-4. Charting dependencies in Batarang

####元素属性和控制台访问

当你通过HTML模板来探究一个AngularJS应用程序时, 元素选项的属性窗格中现在有一个额外的AngularJS属性部分. 这允许你检查模型所连接的给定元素的`scope`. 它也会公开这个元素的`scope`到控制台中, 因此你可以在控制台中通过`$scope`变量来访问它. 如图3-5所示:

![properties](figure/3-5.png)

Figure 3-5. AngularJS properties within Batarang

##Yeoman: 优化你的工作流程

相当多的工具如雨后春笋般涌现, 以帮助你在开发应用程序时优化工作流程. 我们在前面章节所谈及的Yeoman就是这样一种工具, 它拥有令人印象深刻的功能集, 包括:

+ 轻快的脚手架
+ 内置预览服务器
+ 集成包管理
+ 一流的构建过程
+ 使用PhantomJS进行单元测试

它还很好的集成和扩展了AngularJS, 这也是我们为什么强烈推荐任何AngularJS项目使用它的主要原因之一. 让我们通过上面的集中方式使用Yeoman时你的生活更轻松.

###安装Yeoman

安装Yeoman是一个相当复杂的过程, 但也可以通过一些脚本来帮助你安装.

在Mac/Linux机器上, 运行下面的命令:

	curl -L get .yeoman.io | bash

然后只需按照打印的只是来获取Yeoman.

对于Windows机器, 或者运行它是遇到任何问题, 到[https://github.com/yeoman/yeoman/wiki/Manual-Install](https://github.com/yeoman/yeoman/wiki/Manual-Install)并按照说明来安装会让你畅通无阻.

###启动一个新的AngularJS项目

正如前面所提到的, 甚至一个简单的项目都有许多技术需要处理, 从模板到基础控制, 再到库依赖, 一切事情都需要结构化. 你可以手动的做这些工作, 或者也可以使用Yeoman处理它.

只需为你的项目中简单的创建一个目录(Yeoman将把目录名称当作项目名称), 然后运行下面的命令:

	yeoman init angular

这将创建一个本章项目优化部分所详细描述的一个完整的结构, 包括渲染路由的框架, 单元测试等等.

###运行服务器

如果你不适用Yeoman, 那么你不得不创建一个HTTP服务器来服务你的前端代码. 但是如果使用Yeoman, 那么你将获得一个内置的预先配置好的服务器并且还能获得一些额外的好处. 你可以使用下面的命令启动服务器:

	yeoman server

这不单单只启动一个Web服务器来服务于你的代码, 它还会自动打开你的Web浏览器并在你改变你的应用程序时刷新你的浏览器.

###添加新的路由, 视图和控制器

添加一个新的Angular路由涉及多个步骤, 其中包括:

+ 在`index.html`中启用新的控制器JS文件
+ 添加正确的路由到AngularJS模块中
+ 创建HTML模板
+ 添加单元测试

所有的这些在Yeoman中使用下面的命令可以在一步完成:
```bash
	yeoman init angular:route routeName
```
因此, 如果你运行`yeoman init angular:route routeName`结束之后它将执行以下操作:

+ 在`app/scripts/controllers`目录中创建一个`home.js`控制器骨架
+ 在`test/specs/controllers`目录中创建一个`home.js`测试规范
+ 将`home.html`模板添加到`app/views`目录中
+ 链接主引用模块中的home路由(在app/scripts/app.js`文件中)

所有的这些都只需要一条单独的命令!

###测试的故事

我们已经看过使用Karma如何轻松的启动和运行测试. 最终, 运行所有的单元测试只需要两条命令.

Yeoman使它变得更容易(如果你相信它). 每当你使用Yeoman生成一个文件, 它都会给你创建一个填充它的测试存根. 一旦你安装了Karma, 使用Yeoman运行测试只需执行下面的命令即可:
```bash
	yeoman test
```
###构建项目

构建一个完备的应用程序可能是痛苦的, 或者至少涉及到需要步骤. Yeoman通过允许你像下面这样做减轻了不少痛苦:

+ 连接(合并)所有JS脚本到一个文件中
+ 版本化文件
+ 优化图片
+ 生成应用程序缓存清单

所有的这些好处都来自于一条命令:
```bash
	yeoman build
```
Yeoman不支持压缩文件, 但是根据来发者提供的信息, 它很快会到来.

##使用RequireJS整合AngularJS

如果你提前做好更多的事情, 正好会让你的开发环境更简单. 后期修改你的开发环境, 会需要修改更多的文件. 依赖管理和创建包部署是任何规模的项目所忧虑的.

使用JavaScript设置你的开发环境是相当困难的, 因为它涉及Ant构建维护, 连接你的文件来构建脚本, 压缩它们等等. 值得庆幸的是, 在不久之前已经出现了像RequireJS这样的工具, 它允许你定义和管理你的JS依赖关系, 以及将他们挂到一个简单的构建过程中. 随着这些异步加载管理的工具诞生, 能够确保所有的依赖文件在执行之前加载好, 重点工作可以放在实际的功能开发, 在此之前从未如此简单过.

值得庆幸的是, AngularJS能够很好的发挥[RequireJS](http://requirejs.org/), 因此你可以做到两全其美. 这里有一个目标示例, 我们找到了在一个系统中能够工作的很好而且易于遵循的方式来提供一个样本设置.

让我们一起来看看这个项目的组织(类似前面描述的骨架, 稍微有一点变化):

1. **app**: 这个目录是所有显示给用户的应用程序代码宿主目录. 包括HTML, JS, CSS, 图片和依赖的库.

    a. /**style**: 包含所有的CSS/Less文件

    b. /**images**: 包含项目的所有图片文件

    c. /**script**: 主AngularJS代码库. 这个目录也包括我们的引导程序代码, 主要的RequireJS集成

        i. /**controllers**: 这里是AngularJS控制器

        ii. /**directives**: 这里是AngularJS指令

        iii. /**filters**: 这里是AngularJS过滤器

        iv. /**services**: 这里是AngularJS服务

    d. /**vendor**: 我们所依赖的库(Bootstrap, RequireJS, jQuery)

    e. /**views**: 视图的HTML模板部分和项目所使用的组件

2. **config**: 包含单元测试和场景测试的Karma配置

3. **test**: 包含应用程序的单元测试和场景测试(整合的)

    a. /**spec**: 包含应用程序的JS目录中的单元测试和镜像结构

    b. /**e2e**: 包含端到端的场景规范

我们所需要做的第一件事情是在`main.js`文件(在app目录)中引入RequireJS, 然后使用它加载所有的其他依赖项. 这里有一个例子, 我们的JS项目除了自己的代码还会依赖于jQuery和Twitter的Bootstrap.
```js
	//the app/scripts/main.js file, which defines our RequireJS config
	require.config({
		paths: {
			angular: 'vendor/angular.min',
			jquery: 'vendor/jquery',
			domReady: 'vendor/require/domReady',
			twitter: 'vendor/bootstrap',
			angularResource: 'vendor/angular-resource.min'
		},
		shim: {
			'twitter/js/bootstrap': {
				deps: ['jquery/jquery']
			},
			angular: {
				deps: ['jquery/jquery', 'twitter/js/bootstrap'],
				exports: 'angular'
			},
			angularResource: {
				deps: ['angular']
			}
		}
	});

	require([
		'app',
		//Note this is not Twitter Bootstrap
		//but our AngularJS bootstrap
		'bootstrap',
		'controllers/mainControllers',
		'services/searchServices',
		'directives/ngbkFocus'
		//Any individual controller, service, directive or filter file
		//that you add will need to be pulled in here.
		//This will have to be maintained by hand.
		],
		function(angular, app){
			'use strict';

			app.config(['$routeProvider',
				function($routeProvider){
					//define your Routes here
				}
			]);
		}
	);
```
然后我们定义一个`app.js`文件. 这个文件定义我们的AngularJS应用程序, 同时告诉它, 它依赖于我们所定义的所有控制器, 服务, 过滤器和指令. 我们所看到的RequireJS依赖列表中所提到的只是一点点.

你可以认为RequireJS依赖列表就是一个JavaScript的import语句块. 也就是说, 代码块内的函数直到所有的依赖列表都满足或者加载完成它都不会执行.

另外请注意, 我们不会单独 告诉RequireJS, 载入的执行,服务或者过滤器是什么, 因为这些并不属于项目的结构. 每个控制器, 服务, 过滤器和指令都是一个模块, 因此只定义这些为我们的依赖就可以了.
```js
	// The app/scripts/app.js file, which defines our AngularJS app
	define(['angular', 'angularResource', 'controllers/controllers','services/services', 'filters/filters','directives/directives'],function (angular) {
		return angular.module(‘MyApp’, ['ngResource', 'controllers', 'services','filters', 'directives']);
	});
```
我们还有一个`bootstrap.js`文件, 它要等到DOM准备就绪(这里使用的RequireJS的插件`domReady`), 然后告诉AngularJS继续执行, 这是很好的.
```js
	// The app/scripts/bootstrap.js file which tells AngularJS
	// to go ahead and bootstrap when the DOM is loaded
	define(['angular', 'domReady'], function(angular, domReady) {
		domReady(function() {
			angular.bootstrap(document, [‘MyApp’]);
		});
	});
```
这里将引导从应用程序中分割出来, 还有一个有事, 即我们可以使用一个伪造的文件潜在的取代我们的`mainApp`或者出于测试的目的使用一个`mockApp`. 例如 如果你所依赖的服务器不可开, 你只需要创建一个`fakeApp`使用伪造的数据来替换所有的`$http`请求, 以保持你的开发秩序. 这样的话, 你就可以只悄悄的使用一个`fakeBootstrap`和一个`fakeApp`到你的应用程序中.

现在, 你的`main.html`主模板(app目录中)可能看起来像下面这样:
```html
	<!DOCTYPE html>
	<html> <!-- Do not add ng-app here as we bootstrap AngularJS manually-->
	<head>
		<title>My AngularJS App</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="styles/bootstrap.min.css">
		<link rel="stylesheet" type="text/css"
		href="styles/bootstrap-responsive.min.css">
		<link rel="stylesheet" type="text/css" href="styles/app.css">
	</head>
	<body class="home-page" ng-control ler="RootController">
		<div ng-view ></div>
		<script data-main="scripts/main" src="lib/require/require.min.js"></script>
	</body>
	</html>
```
现在, 我们来看看`js/controllers/controllers.js`文件, 这看起来几乎与`js/directives/directives.js`, `js/filters/filters.js`和`js/services/services.js`一模一样:
```js
	define(['angular'], function(angular){
		'use strict';
		return angular.module('controllers', []);
	});
```
因为我们使用了RequireJS依赖的结构, 所有的这些都会保证只在Angular依赖满足并加载完成的情况下才会运行.

每个文件都定义为一个Angular模块, 然后通过将单个的控制器, 指令, 过滤器和服务添加到定义中来使用.

让我们来看看一个指定定义(比如第二章的`focus`指令):
```js
	//File: ngbkFocus.js

	define(['directives/directives'], function(directives) {
		directives.directive(ngbkFocus, ['$rootScope'], function($rootScope){
			return {
				restict: 'A',
				scope: true,
				link: function(scope, element, attrs){
					element[0].focus();
				}
			}
		});
	});
```
指令自什么很琐碎的, 让我们仔细看看发生了什么. 围绕着文件的RequireJS shim告诉我们`ngbkFocus.js`依赖于在模块中声明的`directices/directives.js`文件. 然后它使用注入指令模块将自身指令声明添加进来. 你可以选择多个指令或者一个单一的对应的文件. 这完全由你决定.

一个重点的注意事项: 如果你有一个控制器进入到服务中(也就是说你的`RootController`依赖于你的`UserSevice`, 并且获取`UserService`注入), 那么你必须确保将你定义的文件加入RequireJS依赖中, 就像这样:
```js
	define(['controllers/controllers', 'services/userService'], function(controllers){
		controllers.controller('RootController', ['$scope', 'UserService', function($scope, UserService){
			//Do what's needed
		}]);
	});
```
这基本上是你整个源文件目录的结构设置.

但是你会问, 这如何处理我的测试? 我很高兴你会问这个问题, 因为你会得到答案.

有个很好的消息, Karma支持RequireJS. 只需安装最新和最好版本的Karma.(使用`npm install -g karma`).

一旦你安装好Karma, Karma针对单元测试的配置也会响应的改变. 以下是我们如果设置我们的单元测试来运行我们之前定义的项目结构:
```js
    // This file is config/karma.conf.js.
    // Base path, that will be used to resolve files
    // (in this case is the root of the project)
    basePath = '../';

    // list files/patterns to load in the browser
    files = [
        JASMINE,
        JASM I NE_ADAPTER
        REQUIRE,
        REQU I RE_ADAPTER ,
        // !! Put all libs in RequireJS 'paths' config here (included: false).
        // All these files are files that are needed for the tests to run,
        // but Karma is being told explicitly to avoid loading them, as they
        // will be loaded by RequireJS when the main module is loaded.
        {pattern: 'app/scripts/vendor/**/*.js', included: false},
        // all the sources, tests // !! all src and test modules (included: false)
        {pattern: 'app/scripts/**/*.js', included: false},
        {pattern: 'app/scripts/*.js', included: false},
        {pattern: 'test/spec/*.js', included: false},
        {pattern: 'test/spec/**/*.js', included: false},
        // !! test main require module last
        'test/spec/main.js'
     ];
    // list of files to exclude
    exclude = [];

    // test results reporter to use
    // possible values: dots || progress
    reporter = 'progress';

    // web server port
    port = 8989;

    // cli runner port
    runnerPort = 9898;

    // enable/disable colors in the output (reporters and logs)
    colors = true;

    // level of logging
    logLevel = LOG_INFO;

    // enable/disable watching file and executing tests whenever any file changes
    autoWatch = true;

    // Start these browsers, currently available:
    // - Chrome
    // - ChromeCanary
    // - Firefox
    // - Opera
    // - Safari
    // - PhantomJS
    // - IE if you have a windows box
    browsers = ['Chrome'];

    // Cont inuous Integrat ion mode
    // if true, it captures browsers, runs tests, and exits
    singleRun = false;
```
 我们使用一个稍微不同的格式来定义的我们的依赖(包括: false是非常重要的). 我们还添加了REQUIRE_JS和适配依赖. 最终进行这一系列工作的是`main.js`, 他会触发我们的测试.
```js
	// This file is test/spec/main.js

	require.config({
		// !! Karma serves files from '/base'
		// (in this case, it is the root of the project /your-project/app/js)
		baseUrl: ' /base/app/scr ipts' ,
		paths: {
        angular: 'vendor/angular/angular.min',
			jquery: 'vendor/jquery',
			domReady: 'vendor/require/domReady',
			twitter: 'vendor/bootstrap',
			angularMocks: 'vendor/angular-mocks',
			angularResource: 'vendor/angular-resource.min',
			unitTest: '../../../base/test/spec'
		},

		// example of using shim, to load non-AMD libraries
		// (such as Backbone, jQuery)
		shim: {
			angular: {
				exports: 'angular'
			},
			angularResource: { deps:['angular']},
			angularMocks: { deps:['angularResource']}
		}
	});

	// Start karma once the dom is ready.
	require([
        'domReady' ,
        // Each individual test file will have to be added to this list to ensure
        // that it gets run. Again, this will have to be maintained manually.
        'unitTest/controllers/mainControllersSpec',
        'unitTest/directives/ngbkFocusSpec',
        'unitTest/services/userServiceSpec'
        ], function(domReady) {
            domReady(function() {
                window.__karma__.start();
        });
    });
```
由此设置, 我们可以运行下面的命令
```bash
	karma start config/karma.conf.js
```
然后我们就可以运行测试了.

当然, 当它涉及到编写单元测试就需要稍微的改变一下. 它们需要RequireJS支持的模块, 因此让我们来看一个测试范例:
```js
	// This is test/spec/directives/ngbkFocus.js
	define(['angularMocks', 'directives/directives', 'directives/ngbkFocus'], function() {
		describe('ngbkFocus Directive', function() {
			beforeEach(module('directives'));

			// These will be initialized before each spec (each it(), that is),
			// and reused
			var elem;
			beforeEach(inject(function($rootScope, $compile) {
				elem = $compi le('<input type=”text” ngbk-focus>')($rootScope);
			}));

			it('should have focus immediately', function() {
				expect(elem.hasClass('focus')).toBeTruthy();
			});
		});
	});
```
我们的每个测试将做到以下几点:

1. 拉取`angularMocks`获取我们的angular, angularResource, 当然还有angularMocks.

2. 拉取高级模块(directives中的指令, controllers中的控制器等等), 然后它实际上测试的是单独的文件(loadingIndicator).

3. 如果你的测试愈来愈其他的服务或者控制器, 除了在AngularJS中告知意外, 要确保也定义在RequireJS的依赖中.

这种方法可以用于任何测试, 而且你应该这么做.

值得庆幸的是, RequireJS的处理方式并不会影响我们所有的端到端的测试, 因此可以使用我们目前所看到的方式简单的做到这一点. 一个范例配置如下, 假设你的服务其在http://localhost:8000上运行你的应用程序:
```js
	// base path, that will be used to resolve files
	// (in this case is the root of the project
	basePath = '../';

	// list of files / patterns to load in the browser
	files = [
		ANGULAR_SCENARIO,
		ANGULAR_SCENARIO_ADAPTER,
		'test/e2e/*.js'
	];

	// list of files to exclude
	exclude = [];

	// test results reporter to use
	// possible values: dots || progress
	reporter = 'progress';

	// web server port
	port = 8989;

	// cli runner port
	runnerPort = 9898;

	// enable/disable colors in the output (reporters and logs)
	colors = true;

	// level of logging
	logLevel = LOG_INFO;

	// enable/disable watching file and executing tests whenever any file changes
	autoWatch = true;

	urlRoot = '/_karma_/';

	proxies = {
		'/': 'http://localhost:8000/'
	};

	// Start these browsers, currently available:
	browsers = ['Chrome'];

	// Cont inuous Integrat ion mode
	// if true, it capture browsers, run tests and exit
	singleRun = false;
```
