# 其他关注点

在这一章中, 我们将看一切目前Angular所实现的其他有用的特性, 但是我们不会涵盖所有的或者深入的章节和例子.

## 目录

- [$location](#location)
- [HTML5模式和Hashbang模式](#html5模式和hashbang模式)
- [AngularJS模块方法](#angularjs模块方法)
	- [主方法在哪？](#主方法在哪)
	- [加载和依赖](#加载和依赖)
	- [快捷方法](#快捷方法)
- [$on, $emit和$broadcast之间的作用域通信](#on-emit和broadcast之间的作用域通信)
- [Cookies](#cookies)
- [国际化和本地化](#国际化和本地化)
	- [在AngularJS中我能做什么？](#在angularjs中我能做什么)
	- [如何获取所有工作？](#如何获取所有工作)
	- [常见问题](#常见问题)
- [净化HTML和模块](#净化html和模块)
	- [Linky](#linky)

## $location

到现在为止, 你已经看到了不少使用AngularJS中的`$location`服务的例子. 它们大多数都只是短暂的一撇--在这里访问, 那里设置. 在这一小节, 我们将深入研究AngularJS中的`$location`服务时什么, 以及什么时候你应该使用它, 什么时候不应该使用它.

`$location`服务是一个存在于任何浏览器中的`window.location`的包装器. 那么为什么你应该使用它而不是直接使用`window.location`呢?

**不再使用全局状态**

`window.location`是一个使用全局状态的很好的例子(实际上, 浏览器中的`window`和`document`对象都是很好的例子). 一旦你的应用程序中有全局的状态(通常我们都说全局变量), 它的测试, 维护和工作都会变得困难(即使不是现在, 从长远来看它肯定是一个潜在的隐患). `$location`服务隐藏了这个潜在的隐患(也就是我们所谓的全局状态), 并且允许你通过注入mocks到你的单元测试中来测试你的浏览器位置信息.

**API**

`window.location`让你能够完全访问浏览器位置信息的内容. 也就是说, `window.location`给你一个字符串而`$location`服务给你提供了更好的服务, 它提供了类似于jQuery的setters和getters让你能够使用它以一个干净的方式工作.

**AngularJS集成**

如果你使用`$location`, 你可以在任何你希望使用的时候使用它. 但是如果直接使用`window.location`, 在有变化时你必须负责通知给AngularJS, 并且还要监听这些改变/变化.

**HTML5集成**

`$location`服务会在HTML5 APIs在浏览器中可用时智能的识别并使用它们. 如果它们不可用, 它会降级使用默认的用法.

那么什么时候你应该使用`$location`服务呢? 任何你想反应URL变化的时候(它并不是通过`$routes`来覆盖的, 而且你应该主要用于基于URL工作的视图中), 以及在浏览器中响应当前URL变化的时候使用.

让我们考虑使用一个小例子来看看你应该如何在一个实际的应用程序中使用`$location`服务. 想象一下我们有一个`datepicker`, 并且当我们选择一个日期时, 应用程序导航到某个URL. 让我们一起来看看它看起来可能是什么样子:

	// Assume that the datepicker calls $scope.dateSelected with the date
	$scope.dateSelected = function(dateTxt) {
		$location.path('filteredResults?startDate=' + dateTxt);
		// If this were being done in the callback for
		// an external library, like jQuery, then we would have to
		$scope.$apply();
	};

####用或者不用$apply?

对于AngularJS开发者来说什么时候调用`$scope.$apply()`, 什么时候不能调用它是比较混乱的. 互联网上的建议和谣言非常猖獗. 在本小节我们将让它变得非常清楚.

但是首先让我们先尝试以一个简单的形式使用`$apply`.

`Scope.$apply`就像一个延迟的worker. 我们会告诉它有很多工作要做, 它负责响应并确保更新绑定和所有变化的视图效果. 但并不是所有的时间都只做这项工作, 它只会在它觉得有足够的工作要做时才会做. 在所有的其他情况下, 它只是点点头并标记在稍候处理. 它只是在你给它指示时并显示的告诉它处理实际的工作. AngularJS只是定期在它的声明周期内做这些, 但是如果调用来自于外部(比如说一个jQuery UI事件), `scope.$apply`只是做一个标记, 但并不会做任何事. 这就是为什么要调用`scope.$apply`来告诉它"嘿!你现在需要做这件事, 而不是等待!".

这里有四个快速的提示告诉你应该什么时候(以及如何)调用`$apply`.

+ **不要**始终调用它. 当AngularJS发现它将导致一个异常(在其`$digest`周期内, 我们调用它)时调用`$apply`. 因此"有备无患"并不是你希望使用的方法.

+ 当控制器在AngularJS外部(DOM时间, 外部回调函数如jQuery UI控制器等等)调用AngularJS函数时**调用**它. 对于这一点, 你希望告诉AngularJS来更新它自身(模型, 视图等等), 而`$apply`就是做这个的.

+ 只要可能, 通过传递给`$apply`来执行你的代码或者函数, 而不是执行函数, 然后调用`$apply()`. 例如, 执行下面的代码:

	$scope.$apply(function(){
		$scope.variable1 = 'some value';
		excuteSomeAction();
	});

而不是下面的代码:

	$scope.variable1 = 'some value';
	excuteSomeAction();
	$scope.$apply();

尽管这两种方式将有相同的效果, 但是它们的方式明显不同.

第一个会在`excuteSomeAction`被调用时将捕获发生的任何错误,  而后者则会瞧瞧的忽略此类错误. 只有使用第一种方式时你才会从AngularJS中获取错误的提示.

+ kaov使用类似的`safeApply`:

	$scope.safeApply = function(fn){
		var phase = this.$root.$$phase;
		if(phase == '$apply' || phase == '$digest') {
			if(fn && (typeof(fn) === 'function')) {
				fn();
			}
		}else{
			this.$apply(fn);
		}
	};

你可以在顶层作用域或者根作用域中捕获到它, 然后在任何地方使用`$scope.$safeApply`函数. 一直都在讨论这个, 希望在未来的版本中这会称为默认的行为.

是否那些其他的方法也可以在`$location`对象中使用呢? 表7-1包含了一个快速的参考用于让你绑定使用.

让我们来看看`$location`服务是如何表现的, 如果浏览器中的URL时`http://www.host.com/base/index.html#!/path?param1=value1#hashValue`.

Table 7-1 Functions on the $location service

<table>
	<thead>
		<tr>
			<th>Getter Function</th>
			<th>Getter Value</th>
			<th>Setter Function</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>absUrl()</td>
			<td><i>http://www.host.com/base/index.html#!/path?param1=value1#hashValue,</i></td>
			<td>N/A</td>
		</tr>
		<tr>
			<td>hash()</td>
			<td>hashValue</td>
			<td>hash('newHash')</td>
		</tr>
		<tr>
			<td>host()</td>
			<td>www.host.com</td>
			<td>N/A</td>
		</tr>
		<tr>
			<td>path()</td>
			<td>/path</td>
			<td>path('/newPath')</td>
		</tr>
		<tr>
			<td>protocol()</td>
			<td>http</td>
			<td>N/A</td>
		</tr>
		<tr>
			<td>search()</td>
			<td>{'a':'b'}</td>
			<td>search({'c':'def'})</td>
		</tr>
		<tr>
			<td>url()</td>
			<td>/path?param1=value1?hashValue</td>
			<td>url('/newPath?p2=v2')</td>
		</tr>
	</tbody>
</table>

表7-1的Setter Function一列提供了一个值样本表示setter函数与其的对象类型.

注意`search()`setter函数还有一些操作模式:

+ 基于一个`object<string, string>`调用`search(searchObj)`表示所有的参数和它们的值.
+ 调用`search(string)`将直接在URL上设置URL的参数为`q=String`.
+ 使用一个字符串参数和值调用`search(param, value)`来设置URL中一个特定的搜索参数(或者使用null调用它来移除参数).

使用任意一个这些setter函数并不意味着window.location将立即获得改变. `$location`服务会在Angular生命周期内运行,  所有的位置改变将积累在一起并在周期的后期应用. 所以可以随时作出改变, 一个借一个的, 而不用担心用户会看到一个不断闪烁和不断变更的URL的情况.

## HTML5模式和Hashbang模式

`$location`服务可以使用`$locationProvider`(就像AngularJS中的一切一样, 可以注入)来配置. 对它提供两个属性特别有兴趣, 分别是:

**html5Mode**

一个决定`$location`服务是否工作在HTML5模式中的布尔值.

**hashPrefix**

提个字符串值(实际上是一个字符)被用作Hashbang URLs(在Hashbang模式或者旧版浏览器的HTML模式中)的前缀. 默认情况下它为空, 所以Angular的hash就只是''. 如果`hashPrefix`设置为'!', 然后Angular就会使用我们所称作的Hashbang URLs(url紧随'!'之后).

你可能会问, 这些模式是什么? 嗯, 假设你有一个超级棒的网站`www.superawesomewebsite.com`在使用AngularJS.

比方说你有一个特定的路由(它有一些参数和一个hash), 比如`/foo?bar=123#baz`.

在默认的Hashbang模式中(使用`hashPrefix`设置为'!'), 或者不支持HTML5模式的旧版浏览器中, 你的URL看起来像这样:

	http://www.superawesomewebsite.com/#!/foo?bar=123#baz

然而在HTML5模式中, URL看起来会像这样:

	http://www.superawesomewebsite.com/foo?bar=123#baz

在这两种情况下, `location.path()`就是`/foo`, `location.search()`就是`bar=123`, location.hash()`就是`baz`. 因此如果是这种情况, 为什么你不希望使用HTML5模式呢?

Hashbang方法能够在所有的浏览器中无缝的工作, 并且只需要最少的配置. 你只需要设置`hashBang`前缀(默认情况下为!)并且你可以做到更好.

HTML模式中, 在另一方面, 还可以通过使用HTML5的History API来访问浏览器的URL. 而`$location`服务能足够智能的判断浏览器是否支持HTML5模式, 必要的情况下还可以降级使用Hashbang方法, 因此你不需要担心额外的工作. 但是你不得不注意以下事情:

**服务端配置**

因为HTML5的链接看起来像你应用程序的所有其他URL, 你需要很小心的在服务端将你应用程序的所有链接路由连接到你的主HTML页面(最有可能的是,`index.html`). 例如, 如果你的应用是`superawesomewebsite.com`的登录页, 并且你的应用中有一个`/amazing?who=me`的路由, 然后URL在浏览其中显示为`http://www.superawesomewebsite.com/ amazing?who=me+`.

当你浏览你的应用程序时, 默认情况下表现很好, 因为有HTML5 History API介入和负责很多事情. 但是如果你尝试直接浏览这个URL, 你的服务器会认为你是不是疯了, 因为在服务端它并不知道这个资源. 所以你必须确保所有指向`/amazing`的请求被充定向到`/index.html#!/amazing`.

AngularJS将会以这种形式来在这一点注意这些事情. 它会检测路径的改变并冲顶像到我们所定义的AngularJS路由中.

**Link rewriting(链接改写)**

你可以很容易的像下面这样指定一个URL:

	<a href="/some?foo=bar">link</a>

根据你是否使用的HTML5模式, AngularJS会注意分别重定向到`/some?foo=bar`或者`index.html#!/some?foo=bar`. 没有额外的步骤需要你处理. 很棒, 是不是?

但是下面的链接形式像不会被改写, 并且浏览器将在这个页面上执行一个完整的重载:

+ a. 链接像下面这样包含一个`target`元素

	<a href="/some/link" target="_self">link<a/>

+ b. 链接到一个不用域名的绝对路径:

	<a href="http://www.angularjs.org">link</a>

这里时不同的, 因为它是一个绝对的URL路径, 而前面的记录会使用现有的基础URL.

+ c. 链接基于一个不同的已经定义好的路径开始时:

	<a href="/some-other-base/link">link</a>

**Relative Links(相对链接)**

一定要检查所有的相对链接(相对路径), 图片, 脚本等等. 你必须在你主HTML文件的头部指定基本的参照URL(<base href="/my-base">), 或者你必须在每一处使用绝对URLs路径(以/开头的), 因为相对的URL将会使用文档中初试的绝对URL被解析为绝对的URL, 这往往不同于应用程序的根源.

强烈建议从文档根源启用History API来运行Angular应用程序, 因为它要注意所有相对路径的问题.

## AngularJS模块方法

AngularJS模块负责定义如何引导你的应用程序。它还声明定义了应用程序片段。接下来让我们一起看看它是如何实现这一点的。

### 主方法在哪？

如果你来自于Java，甚至是Python编程语言社区，你可能会疑惑，AngularJS中的主方法在哪？你知道的，主方法会引导一切，并且它是首先会个执行的东西？它会将JavaScript函数和实例以及所有的事情联系在一起，然后再通知你的应用程序去运行？

但是在AngularJS中没有。替代的是Angular中的模块的概念。模块允许我们声明指定我们应用程序的依赖，以及应用程序的引导是如何发生的。使用这种方式的原因时多方面的。

1. 首先是**声明**。这意味以这种方式编写代码更容易编写和理解。就像阅读英语一样！
2. 它是**模块化**的。它会迫使你去思考如何定义你的组件和依赖，并让它们很明确。
3. 它还允许**简单测试**。在你的单元测试中，你可以选择性的拉去模块来测试，以规避代码中不可以测试的部分。同时在你的场景测试中，你还可以加载附加的模块，这样可以结合某些组件一起工作让工作变得更容易。

那么接下来，先让我们看看你要如何使用一个已经定义好的模块，然后再来看看我们如何声明一个模块。

比方说我们有一个模块，实际上，我们的应用程序中有一个名为"MyAwesoneApp"的模块。在我的HTML中，我可以只添加下面的\<html\>标签(从技术上将，也可以是任何其他的标签)。

	<html ng-app="MyAwesomeApp">

这里的`ng-app`指令会告诉你的AngularJS可以使用`MyAwesomeApp`模块来引导你的应用程序。

那么，这个模块是如何定义的呢？嗯，首先我们建议你分你的服务，指令和过滤器模块。然后在你的主模块中，你就可以只声明你所依赖的其他模块(与我们在第4章中使用RequireJS的例子一样)。

这种方式让你管理模块变得更容易，因为它们都是很好的完整的代码块。每个模块有且仅有一个职责。这样就允许你在测试中只载入你所关心的模块，从而减少了初始化这些模块的数量。这样，测试就可以变得更小并且只会关心重点。

### 加载和依赖

模块的加载发生在两个不同的阶段，并且它们都有对应的函数。它们分别是配置和运行块(阶段)：

**配置块**

AngularJS会在这个阶段挂接和注册所有的供应商(提供的模块)。这是因为，只有供应商和常量才能够注入到配置块中。服务能不能被初始化，并不能被注入到这个阶段。

**运行块**

运行快用于快速启动你的应用程序，并且在注入任务完成创建之后开始执行应用程序。从此刻开始会阻止接下来的系统配置，只有实例和常量可以注入到运行块中。在AngularJS中，运行块是最接近你想要寻找的主方法的东西。

### 快捷方法

那么可以用模块做什么呢？我们可以实例化控制器，指令，过滤器和服务，但是模块类允许你做更多的事情，正如表7-2所示：

Table 7-2 模块的快捷方法

<table>
	<thead>
		<tr>
			<th>API方法</th>
			<th>描述</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>config(configFn)</td>
			<td>模块加载时使用这个方法注册模块需要做的工作。</td>
		</tr>
		<tr>
			<td>constant(name, object)</td>
			<td>这个首先发生，因此你可以在这里声明所有的常量`app-wide`，和声明所有可用的配置(也就是列表中的第一个方法)以及方法实例(从这里获取所有的方法，如控制器，服务等等).</td>
		</tr>
		<tr>
			<td>controller(name, constructor)</td>
			<td>我们已经看过了很多控制器的例子，它主要用于设置一个控制器。</td>
		</tr>
		<tr>
			<td>directive(name, directiveFactory)</td>
			<td>正如第6章所讨论的，它允许你为应用程序创建指令。</td>
		</tr>
		<tr>
			<td>filter(name, filterFactory)</td>
			<td>允许你创建命名AngularJS过滤器，正如第6章所讨论的。</td>
		</tr>
		<tr>
			<td>run(initializationFn)</td>
			<td>使用这个方法在注入设置完成时处理你要执行的工作，也就是将你的应用程序展示给用户之前。</td>
		</tr>
		<tr>
			<td>value(name, object)</td>
			<td>允许跨应用程序注入值。</td>
		</tr>
		<tr>
			<td>service(name, serviceFactory)</td>
			<td>下一节中讨论。</td>
		</tr>
		<tr>
			<td>factory(name, factoryFn)</td>
			<td>下一节中讨论。</td>
		</tr>
		<tr>
			<td>provider(name, providerFn)</td>
			<td>下一节中讨论。</td>
		</tr>
	</tbody>
</table>

你可能意识到，在前面的表格中我们省略了三个特定API-Factory，Provider，和Service的详细信息。还有一个原因是：这三者之间的用法很容易混肴，因此我们使用一个简单的例子来更好的说明一下什么时候(以及如何)使用它们每一个。

**The Factory**

Factory API可以用来在每当我们有一个类或者对象需要一定逻辑或者参数之前才能初始化的时候调用。一个Factory就是一个函数，这个函数的职责是创建一个值(或者一个对象)。让我们来看一个例子，greeter函数需要和它的salutation参数一起初始化：

	function Greeter(salutation) {
		this.greet = function(name) {
			return salutation + ' ' + name;
		}
	}

greeter工厂方法(它就是一个工厂函数或者说构造函数)看起来就像这样：

	myApp.factory('greeter', function(salut) {
		return new Greeter(salut);
	});

然后可以像这样调用：

	var myGreeter = greeter('Halo');

**The Service**

什么时服务？嗯，一个Factory和一个Service之间的不同就是Factory方法会调用传递给它的函数并返回一个值。而Service方法会在传递给它的控制器方法上调用"new"操作符并返回调用结果。

因此前面的greeter工厂可以替换为如下所示的geeter服务：

	myApp.service('greeter', Greeter);

那么我每次访问一个greeter实例时，AngularJS都会调用`new Greeter()`并返回调用结果。

**The Provider**

这是最复杂的(大部分的都是可配置，很的)一部分。Provider结合了Factory和Service，同时它会在注入系统完全到位之前抛出Provider函数能够进行配置的信息(也就是说，它就发生在配置块中)。

让我们来看看使用Provider修改之后的greeter Service，它看起来可能是下面这样的：

	myApp.provider('greeter', function() {
		var salutation = 'Hello';
		this.setSalutation = function(s){
			salutation = s;
		}

		function Greeter(a) {
			this.greet = function() {
				return salutation + ' ' + a;
			}
		}

		this.$get = function(a) {
			return new Greeter(a);
		}
	});

这就允许我们在运行时(例如，根据用户选择语言)设置salutation的值。

	var myApp = angular.module(myApp, []).config(function(greeterProvider){
		greeterProvider.setSalutation('Namaste');
	});

每当有人访问greeter对象实例的时候AngularJS都会吉利调用`$get`方法。

> **警告！**
>
> 这里有一个轻量级的实现，但是它们之间的用法有明显的区别：

	angular.module('myApp', []);
>
> 以及

	angular.module('myApp');

> 这里的不同之处在于第一种方式会创建一个新的Angular模块，然后它会拉取在方括号([...])中列出的所依赖的模块。第二种方式使用的是现有的模块，它已经在第一次调用用定义好了。

> 因此你应该确保在完整的应用程序中，下面的代码只使用一次就行了：

	angular.module('myApp', [...]); // Or MyModule, if you are modularizing your app

> 如果你不打算将它保存为一个变量并且跨应用程序引用它，然后在其他文件中使用`angular.module(MyApp)`来确保你获取的是一个正确处理过的AngularJS模块。模块中的一切都在模块定义中访问变量，或者直接将某些东西加入到模块定义的地方。

## $on, $emit和$broadcast之间的作用域通信

AngularJS中的作用域有一个非常有层次和嵌套分明的结构。其中它们都有一个主要的`$rootScope`(也就说对应的Angular应用或者`ng-app`)，然后其他所有的作用域部分都是继承自这个`$rootScope`的，或者说都是嵌套在主作用域下面的。很多时候，你会发现这些作用域不会共享变量或者说都不会从另一个原型继承什么。

那么在这种情况下，如何在作用域之间通信呢？其中一个选择就是在应用程序作用域之中创建一个单例服务，然后通过这个服务处理所有子作用域的通信。

在AngularJS中还有另外一个选择：通过作用域中的事件处理通信。但是这种方法有一些限制；例如，你并不能广泛的将事件传播到所有监控的作用域中。你必须选择是否与父级作用域或者子作用域通信。

但是在我们讨论这些之前，那么如何监听这些事件呢？这里有一个例子，在我们任意的恒星系统的作用域中等待和监控一个我们称之为"planetDestroyed"的事件。

	$scope.$on('planetDestoryed', function(event, galaxy, planet){
		// Custom event, so what planet was destroyed
		scope.alertNearbyPlanets(galaxy, planet);
	});

或许你会疑惑，传递给事件监听器的这些附加的参数是从哪里来的？那么就让我们来看看一个独立的planet是如何与它的父级作用域通信的。

	scope.$emit('planetDestroyed', scope.myGalaxy, scope.myPlanet);

`$emit`的附加参数是以作为监听器函数的函数参数的形式来传递的。并且，`$emit`只会从它自己当前作用域向上通信，因此，星球上的穷人们(如果它们有自身的作用域)在它们的星球被毁灭之前并不会收到通知。

类似的，如果银河系统希望向下与它的成员通信，也就是恒星系统，那么它们之间的通信可能像下面这样：

	scope.$emit('selfDestructSystem', targetSystem);

然后，所有的恒星系统都可能在目标系统中监听这个事件，并使用下面的命令来决定它们是否应该自毁：

	scope.$on('selfDestructSystem', function(event, targetSystem){
		if(scope.mySystem === targetSystem){
			scope.selfDestruct(); // Go ka-boom!!
		}
	});

当然，正如事件向上(或者向下)传播，它都可能必须在同一级或者作用域中说："够了，你不能通过！"，或者阻止事件的默认行为。传递给监听器的事件对象都有函数来处理上面的这些所有事情，或者更多，因此让我们在表7-3中看看你可以获取事件对象的哪些信息。

表7-3 事件对象的属性和方法

<table>
	<thead>
		<tr>
			<th>事件属性</th>
			<th>目的</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>event.targetScope</td>
			<td>发出或者传播原始事件的作用域</td>
		</tr>
		<tr>
			<td>event.currentScope</td>
			<td>目前正在处理的事件的作用域</td>
		</tr>
		<tr>
			<td>event.name</td>
			<td>事件名称</td>
		</tr>
		<tr>
			<td>event.stopPropagation()</td>
			<td>一个防止事件进一步传播(冒泡/捕获)的函数(这只适用于使用`$emit`发出的事件)</td>
		</tr>
		<tr>
			<td>event.preventDefault()</td>
			<td>这个方法实际上不会做什么事，但是会设置`defaultPrevented`为true。直到事件监听器的实现者采取行动之前它才会检查`defaultPrevented`的值。</td>
		</tr>
		<tr>
			<td>event.defaultPrevented</td>
			<td>如果调用了`preventDefault`则为true</td>
		</tr>
	</tbody>
</table>

> 说明：关于通信这一节译文很粗糙，待斟酌校对。

## Cookies

不就之后，在你的应用程序中(假设它足够大并且很复杂)，你需要在客户端通过用户的session来存储用户会话的某些状态。你可能还记得(或者说还会做噩梦)，通过`document.cookie`接口来处理纯文本形式的cookies。

值得庆幸的是，这么多年过去了，并且HTML5提供的相关API都能够在现在已经出现的大多数现代浏览器上可用。此外， AngularJS还给你提供了很好的`$cookie`和`$cookieStore` API用来处理cookies。这两个服务都能够很好的发挥HTML5 cookies，当HTML5 API可用时浏览器会选择使用HTML5提供的API，如果不可用则默认选择`document.cookies`。无论那种方式，你都可以选择使用相同的API来进行工作。

首先让我们来看看`$cookie`服务。`$cookie`是一个简单的对象。它有键(属性)和值。给这个对象添加一个键和对应的值，就会将相关信息添加到cookie中，反之，从对象中移除键(属性)时就会从cookie中删除对应的信息。它就是这么简单。

但是大多数时候，你都不会希望直接在`$cookie`上工作。直接在`cookies`上工作意味着你必须自己处理字符串转换操作和解析工作，并且还要从在对象中转换相应的数据。对于这些情况，我们有一个`$cookieStore`方法，它提供了一种编写和移除`cookie`的方式。因此你可以很方便的使用`$cookieStore`来构建一个Search控制器用户记忆最后五个搜索结果，就像下面这样：

	function SearchController($scope, $cookieStore) {
		$scope.search = function(text) {
			// Do the search here
			...
			// Get the past results, or initialize an empty array if nothing found
			var pastSearches = $cookieStore.get('myapp.past.searches') || [];
			if(pastSearches.length > 5) {
				pastSearches = pastSearches.splice(0);
			}
			pastSearches.push(text);
			$cookieStore.put('myapp.past.searches', pastSearches);
		}
	}

## 国际化和本地化

你可能会听到人们提到这些术语，当它们使用不同的语言支持应用程序时。但是在这两者之间有一些细微的区别。想象一下有一个简单的应用程序作为进入用户银行账单的入口。每当你进入这个应用时，它显示且只显示一个东西：

*欢迎！2012年10月25日你的账单数据为`$XX,XXX`。*

现在，明显，上面的代码(信息)目标用户直接定位为美国公民。但是如果我们希望这个应用程序也能够在英国(UK)很好的工作(简单的说就是由程序自身根据环境来选择语言)要做些什么呢？大不列颠(英国)使用的是不同的日期格式和货币符号，但是你又不希望每次你需要应用程序只是一个新的环境是都发生一次变化(比如在`en_US`和`en_UK`)。这里需要抽象的处理输出的日期/事件格式，以及货币符号，都需要从你的代码中来适配**国际化**的环境(或者i18n--18表示单词中i和n之间的字符数)。

如果我们希望这个应用程序在印度也适用呢？或者俄罗斯？此外还有日期格式和货币符号(和格式)，甚至在UI中使用的字符串都需要改变。这种在不同地区转换和本地化分离的二进制字符串的形式就是我们所知道的**本地化**(或者L10n--使用大写的L来区分i和l)。

### 在AngularJS中我能做什么？

AngularJS支持下面所列出的i18n/L10n：

+ currency
+ date/time
+ number

对于这些使用`ngPluralize`指令也可以多元化支持(对于英语就如同i18n/L10n)。

所有的这些多元化的支持都是通过`$locale`服务来处理和维护的，用户管理本地特定的规则设置。`$locale`服务清理本地的IDs，一般由两部分组成：国家代码和语言代码。例如，`en_US`和`en_UK`，分别表示美式英语和英式英语。指定一个国家代码是可选的，并且只指定一个"en"也是有效的本地代码。

### 如何获取所有工作？

获取L10n(本地化)和i18n(国际化)工作的过程在AngularJS中分为三个步骤：

**index.html changes**

AngularJS需要你有一个单独的`index.html`来处理每个受支持的语言环境。你的服务器也需要知道所提供的`index.html`，根据用户地区的偏好设置(这也可以通过客户端的变化来触发，当用户改变它的语言环境时)。

**创建语言环境规则集**

接下来的步骤是针对每个受支持的语言环境创建一个`angular.js`，就像`angular_en-US.js`和`angular_zh-CN.js`。者涉及到在`angular.js`或者`angular.min.js`的结束处关联每个特定语言的本地规则(前面两个语言环境的默认文件就是`angular-locale_en_US.js`和`angular-locale_zh-CN.js`)。因此你的`angular_en-US.js`首先要包含`angular.js`的内容，然后就是`angular-locale_en-US.js`的内容。

**本地规则集来源**

最后一步就是涉及到你必须确保你的本地`index.html`引用本度规则集而不是原始的`angular.js`文件。因此`index_en-US.html`中应该使用`angular_en-US.js`而不是`angular.js`。

### 常见问题

**翻译长度**

你设计的UI在显示June 24, 1988时，在div中尽量控制其大小以适当的正确显示。然后你在西班牙语环境中打开你的UI，然而24 de junio de 1988不再适应同一空间...

那么当你国际化你的应用程序时，请记住，你的字符串长度可能发生巨大的变化，从一个语言翻译为另一个语言时。你应该适当的设计你的CSS，并且应该在各个不同的语言环境中进行完整的测试(不要忘记还存在从右到左的语言)。

**时区问题**

AngularJS的日期/时间过滤器会直接获取来自浏览器的时区设置。因此它依赖于计算机的时区设置，不同的人可能看到不同的信息。无论时JS还是AngularJS都有任意的内置支持由开发者指定的显示时间的时区的机制。

## 净化HTML和模块

AngularJS会很认真对待其安全性，它会尝试尽最大的努力以确保将大多数的攻击转向最小化。一种常见的攻击方式就是注入不安全的HTML内容到你的web页面中，使用这种方式触发一个跨站攻击或者注入攻击。

考虑有这样一个例子，在作用域中我们有一个称之为`myUnsafeHTMLContent`的变量。然后使用利用HTML，使用`OnMouseOver`指令修改元素的内容为`PWN3D!`，就像下面这样：

	$scope.myUnsafeHTMLContent = '<p style="color:blue;>an html' +
		'<em onmouseover="this.textContent=\'PWN3D!\'">click hreer</em>' +
		'snippet</p>';

在AngularJS中其默认行为是：你有一些HTML内容存储在一个变量中并且尝试绑定给它，其返回结果是AngularJS脱离你的内容并打印它。因此，最终得到的HTML内容被视为纯文本内容。

因此：

	<div ng-bind='myUnsafeHTMLContent'></div>

会返回：

	<p style="color:blue">an html
	<em onmouseover="this.textContent='PWN3D!'">click here</em> snippet</p>

最后作为文本渲染在你的Web页面上。

但是如果你想将`myUnsafeHTMLContent`的内容作为HTML呈现在你的AngularJS应用程序呢？在这种情况下，AngularJS惠友额外的指令(和用于引导的服务`$sanitize`)允许你以安全和不安全的方式呈现HTML。

让我们先来看看使用安全形式的例子(通常也应该如此！)，并且呈现相关HTML，小心的避免HTML最可能受到攻击的部分。在这种情况下你会使用`ng-bind-html`指令。

> `ng-bind-html`，`ng-bind-html-unsafe`以及linky过滤器都在`ngSanitize`模块中。因此在你的脚本依赖中需要包含`angular-sanitize.js`(或者`.min.js`)，然后添加一个`ngSanitize`模块依赖，在所有这些工作进行之前。

那么当我么在同样的`myUnsafeHTMLContent`中使用`ng-bind-html`指令时会发生什么呢？就像这样：

	<div ng-bind-html="myUnsafeHTMLContent "></div>

在这种情况下输出内容就像下面这样：

	an html _click here_ snippet

重要的是要注意这里的样式标记(设置字体颜色为蓝色的样式)，以及\<em\>标签上的`onmouseover`事件处理器都被AngularJS移除了。它们被视为不安全的信息，因而被弃用。

最终，如果你决定你确实像呈现`myUnsafeHTMLContent`的内容，无论你是真正相信`myUnsafeHTMLContent`的内容还是其他原因，那么你可以使用`ng-bind-html-unsafe`指令：

	<div ng-bind-html-unsafe="myUnsafeHTMLContent"></div>

那么这种情况下，输出的内容就像下面这样：

	an html _cl ick here_ snippet

此时文本颜色为蓝色(正如附加给p标签的样式)，并且click here还有一个注册给它的`onmouseover`指令。因此一旦你的鼠标从其他地方滑入click here这几个文本时，输出就为改变为:

	an html PWN3D! snippet

正如你可以看到的，显示中这是非常不安全的，因此大概你决定使用`ng-bind-html-unsafe`指令时你要绝对肯定这是你想要的。因为其他人可能很容易读取用户信息并发送到他/她的服务器中。

### Linky

目前`linky`过滤器也存在于`ngSanitize`模块中，并且基本上允许你将它添加到HTML内容中呈现并将现有的HTML转为锚点标记的链接。它的用法很简单，让我们来看一个例子：

	$scope.contents = 'Text with links: http://angularjs.org/ & mailto:us@there.org';

现在，如果你使用下面的方式来绑定数据：

	<div ng-bind-html="contents"></div>

这将导致数据会作为HTML内容打印在页面中，就像下面这样：

	Text with links: http://angularjs.org/ & mailto:us@there.org

接下来让我们看看如果我们使用`linky`过滤器会发生什么：

	<div ng-bind-html="contents | l inky"></div>

`linky`过滤器会通过在文本内容中的查找，给其中所有的URLs格式的文本添加一个\<a\>标签和一个`mailto`链接，从而最终展现给用户的HTML内容就编程下面这样了：

	Text with links: http://angularjs.org/ & us@there.org
