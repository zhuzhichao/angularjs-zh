#指令

对于指令, 你可以扩展HTML来以添加声明性语法来做任何你喜欢做的事情. 通过这样做, 你可以替换一些特定于你的应用程序的通用的\<div\>s和\<span\>s元素和属性的实际意义. 它们都带有Angular提供的基础功能, 但是你可以创建特定于应用程序的你自己想做的事情.

首先我们要复习以下指令API以及它在Angular启动和运行生命周期里是如何运作的. 从那里, 我们将使用这些只是来创建一个指令类型. 在本将完成时我们将学习到如何编写指令的单元测试和使它们运行得更快.

但是首先, 我们来看看一些使用指令的语法说明.

## 目录

- [指令和HTML验证](#指令和html验证)
- [API预览](#api预览)
	- [为你的指令命名](#为你的指令命名)
	- [指令定义对象](#指令定义对象)
	- [编译和链接功能](#编译和链接功能)
	- [作用域](#作用域)
	- [操作DOM元素](#操作dom元素)
	- [控制器](#控制器)
- [小结](#小结)

##指令和HTML验证

在本书中, 我们已经使用了Angular内置指令的`ng-directive-name`语法. 例如`ng-repeat`, `ng-view`和`ng-controller`. 这里, `ng`部分是Angular的命名空间, 并且dash之后的部分便是指令的名称.

虽然我们喜欢这个方便输入的语法, 但是在大部分的HTML验证机制中它不是有效的. 为了支持这些, Angular指令允许你以几种方式调用任意的指令. 以下在表6-1中列出的语法, 都是等价的并能够让你偏爱的[首选的]验证器正常工作

Table 6-1 HTML Validation Schemes
```html
<table>
	<thead>
		<tr>
			<th>Validator</th>
			<th>Format</th>
			<th>Example</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>none</td>
			<td>namespace-name</td>
			<td>ng-repeat=<i>item in items</i></td>
		</tr>
		<tr>
			<td>XML</td>
			<td>namespace:name</td>
			<td>ng:repeat=<i>item in items</i></td>
		</tr>
		<tr>
			<td>HTML5</td>
			<td>data-namespace-name</td>
			<td>data-ng-repeat=<i>item in items</i></td>
		</tr>
		<tr>
			<td>xHTML</td>
			<td>x-namespace-name</td>
			<td>x-ng-repeat=<i>item in items</i></td>
		</tr>
	</tbody>
</table>
```
由于你可以使用任意的这些形式, [AngularJS文档](http://docs.angularjs.org/)中列出了一个驼峰式的指令, 而不是任何这些选项. 例如, 在`ngRepeat`标题下你可以找到`ng-repeat`. 稍后你会看到, 在你定义你自己的指令时你将会使用这种命名格式.

如果你不适用HTML验证器(大多数人都不使用), 你可以很好的使用在目前你所见过的例子中的命名空间-指令[namespace-directive]语法

##API预览

下面是一个创建任意指令伪代码模板
```js
	var myModule = angular.module(...);

	myModule.directive('namespaceDirectiveName', function factory(injectables) {
		var directiveDefinitionObject = {
			restrict: string,
			priority: number,
			template: string,
			templateUrl: string,
			replace: bool,
			transclude: bool,
			scope: bool or object,
			controller: function controllerConstructor($scope, $element, $attrs, $transclude){...},
			require: string,
			link: function postLink(scope, iElement, iAttrs) {...},
			compile: function compile(tElement, tAttrs, transclude){
				return: {
					pre: function preLink(scope, iElement, iAttrs, controller){...},
					post: function postLink(scope, iElement, iAttrs, controller){...}
				}
			}
		};
		return directiveDefinitionObject;
	});
```
有些选项是互相排斥的, 它们大多数都是可选的, 并且它们都有有价值的详细说明:

当你使用每个选项时, 表6-2提供了一个概述.

Table 6-2 指令定义选项
```html
<table>
	<thead>
		<tr>
			<th>Property</th>
			<th>Purpose</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>restrict</td>
			<td>声明指令可以作为一个元素, 属性, 类, 注释或者任意的组合如何用于模板中</td>
		</tr>
		<tr>
			<td>priority</td>
			<td>设置模板中相对于其他元素上指令的执行顺序</td>
		</tr>
		<tr>
			<td>template</td>
			<td>指令一个作为字符串的内联模板. 如果你指定一个模板URL就不要使用这个模板属性.</td>
		</tr>
		<tr>
			<td>templateUrl</td>
			<td>指定通过URL加载的模板. 如果你指定了字符串的内联模板就不需要使用这个.</td>
		</tr>
		<tr>
			<td>replace</td>
			<td>如果为true, 则替换当前元素. 如果为false或者未指定, 则将这个指令追加到当前元素上.</td>
		</tr>
		<tr>
			<td>transclude</td>
			<td>让你将一个指令的原始自节点移动到心模板位置内.</td>
		</tr>
		<tr>
			<td>scope</td>
			<td>为这个指令创建一个新的作用域而不是继承父作用域.</td>
		</tr>
		<tr>
			<td>controller</td>
			<td>为跨指令通信创建一个发布的API.</td>
		</tr>
		<tr>
			<td>require</td>
			<td>需要其他指令服务于这个指令来正确的发挥作用.</td>
		</tr>
		<tr>
			<td>link</td>
			<td>以编程的方式修改生成的DOM元素实例, 添加事件监听器, 设置数据绑定.</td>
		</tr>
		<tr>
			<td>compile</td>
			<td>以编程的方式修改一个指令的DOM模板的副本特性, 如同使用`ng-repeat`时. 你的编译函数也可以返回链接函数来修改生成元素的实例.</td>
		</tr>
	</tbody>
</table>
```
下面让我们深入细节来看看.

###为你的指令命名

你可以用模块的指令函数为你的指令创建一个名称, 如下所示:
```js
	myModule.directive('directiveName', function factory(injectables){...});
```
虽然你可以使用任何你喜欢的名字命名你的指令, 该符号会选择一个前缀命名空间标识你的指令, 同时避免与可能包含在你的项目中的外部指令冲突.

你当然不希望它们使用一个`ng-`前缀, 因为这可能与Angular自带的指令相冲突. 如果你从事于SuperDuper MegaCorp, 你可以选择一个super-, superduper-, 或者甚至是superduper-megacorp-, 虽然你可能选择第一个选项, 只是为了方便输入.

正如前面所描述的, Angular使用一个标准化的指令命名机制, 并且试图有效的在模板中使用驼峰式的指令命名方式来确保在5个不同的友好的验证器中正常工作. 例如, 如果你已经选择了`super-`作为你的前缀, 并且你在编写一个日期选择(datepicker)组件, 你可能将它命名为`superDatePicker`. 在模板中, 你可以像这样来使用它: `super-date-picker`, `super:date-picker`, `data-super-date-picker`或者其他多样的形式.

###指令定义对象

正如前面提到的, 在指令定义中大多数的选项都是可选的. 实际上, 这里并没有硬性的要求必须选择哪些选项, 并且你可以构造出许多有利于指令的子集参数. 让我们来逐步讨论这些选项是做什么的.

####restrict

`restrict`属性允许你指定你的指令声明风格--也就是说, 它是否能够用于作为元素名称, 属性, 类[className], 或者注释. 你可以根据表6-3来指定一个或多个声明风格, 只需要使用一个字符串来表示其中的每一中风格:

Table 6-3 指令声明用法选项
```html
<table>
	<thead>
		<tr>
			<th>Character</th>
			<th>Declaration style</th>
			<th>Example</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>E</td>
			<td>element</td>
			<td>&lt;my-menu title=<i>Products</i>&gt;&lt;/my-menu&gt;</td>
		</tr>
		<tr>
			<td>A</td>
			<td>attribute</td>
			<td>&lt;div my-menu=<i>Products</i>&gt;&lt;/div&gt;</td>
		</tr>
		<tr>
			<td>C</td>
			<td>class</td>
			<td>&lt;div class=my-menu:<i>Products</i>&gt;&lt;/div&gt;</td>
		</tr>
		<tr>
			<td>M</td>
			<td>comment</td>
			<td>&lt;!--directive:my-menu Products--&gt;</td>
		</tr>
	</tbody>
</table>
```
如果你希望你的指令用作一个元素或者一个属性, 那么你应该传递`EA`作为`restrict`字符串.

如果你忽略了`restrict`属性, 则默认为`A`, 并且你的指令只能用作一个属性(属性指令).

如果你计划支持IE8, 那么基于attribute-和class-的指令就是你最好的选择, 因为它需要额外的努力来使新元素正常工作. 可以查看Angular文档来详细了解这一点.

####Priorities

在你有多个指令绑定在一个单独的DOM元素并要确定它们的应用顺序的情况下, 你可以使用`priority`属性来指定应用的顺序. 数值高的首先运行. 如果你没有指定, 则默认的priority为0.

很难发生需要设置优先级的情况. 一个需要设置优先级例子是`ng-repeat`指令. 当重复元素时, 我们希望Angular在应用指令之前床在一个模板元素的副本. 如果不这么做, 其他的指令将会应用到标准的模板元素上而不是我们所希望在应用程序中重复我们的元素.

虽然它(proority)不在文档中, 但是你可以搜索Angular资源中少数几个使用`priority`的其他指令. 对于`ng-repeat`, 我们使用优先级值为1000, 这样就有足够的优先级处理优先处理它.

####Templates

当创建组件, 挂件, 控制器一起其他东西时, Angular允许你提供一个模板替换或者包裹元素的内容. 例如, 如果你在视图中创建一组tab选项卡, 可能会呈现出如图6-1所示视图.

![tab](figure/tab.png)

图6-1 tab选项卡视图

并不是一堆\<div\>, \<ul\>\<li\>和\<a\>元素, 你可以创建一个\<tab-set\>和\<tab\>指令, 用来声明每个单独的tab选项卡的结构. 然后你的HTML可以做的更好来表达你的模板意图. 最终结果可能看起来像这样:
```html
	<tab-set>
		<tab title="Home">
			<p>Welcome home!</p>
		</tab>
		<tab title="Preferences">
			<!-- preferences UI goes here -->
		</tab>
	</tab-set>
```
你还可以给title绑定一个字符串数据, 通过在\<tab\>或者\<tab-set\>上绑定控制器处理tab选项内容. 它不仅限于用在tabs上--你还可以用于菜单, 手风琴, 弹窗, dialog对话框或者其他任何你希望以这种方式实现的地方.

你可以通过`template`或者`templateUrl`属性来指定替换的DOM元素. 使用`template`通过字符串来设置模板内容, 或者使用`templateUrl`来从服务器的一个文件上来加载模板. 正如你在接下来的例子中会看到, 你可以预先缓存这些模板来减少GET请求, 这有利于提高应用的性能.

让我们来编写一个dumb指令: 一个\<hello\>元素, 只是用于使用\<div\>Hi there\</div\>来替换自身. 在这里, 我们将设置`restrict`来允许元素和设置`template`显示我们所希望的东西. 由于默认的行为只将内容追加到元素中, 因此我们将设置`replace`属性为true来替换原来的模板:
```js
	var appModule = angular.module('app', []);
	appModule.directive('hello', function(){
		return {
			restrict: 'E',
			template: '<div>Hi there</div>',
			replace: true
		};
	});
```
在页面中我们可以像这样使用它:
```html
	<html lang="en" ng-app="app">
	...
	<body>
		<hello></hello>
	</body>
	...
```
将它载入到浏览器中, 我们会看到"Hi there".

如果你查看页面的源代码, 在页面上你仍然会看到\<hello\>\</hello\>, 但是如果你查看生成的源代码(在Chrome中, 你可以在"Hi there"上右击然后选择审查元素), 你会看到:
```html
	<body>
		<div>Hi there</div>
	</body>
```
\<hello\>\</hello\>被模板中的\<div\>替换了.

如果你从指令定义中移除`replace: true`, 那么你会看到\<hello\>\<div\>Hi there\</div\>\</hello\>.

通常你会希望使用`templateUrl`而不是`template`, 因为输入HTML字符串并不是那么有趣. `template`属性通常有利于非常小的模板. 使用templateUrl`同样非常有用, 可以设置适当的头来使模板可缓存. 我们可以像下面这样重写我们的`hello`指令:
```js
	var appModule = angular.module('app', []);
	appModule.directive('hello', function(){
		return {
			restrict: 'E',
			templateUrl: 'helloTemplate.html',
			replace: true
		};
	});
```
在`helloTemplate.html`中, 你只需要输入:
```html
	<div>Hi there</div>
```
如果你使用Chrome浏览器, 它的"同源策略"会组织Chrome从`file://`中加载这些模板, 并且你会得到一个类似"Origin null is not allowed by Access-Control-Allow-Origin."的错误. 那么在这里, 你有两个选择:

+ 通过服务器来加载应用
+ 在Chrome中设置一个标志. 你可以通过在命令行中使用`chrome --allow-file-access-from-files`命令来运行Chrome做到这一点.

这将会通过`templateUrl`加载这些文件, 然而, 这会让你的用户要等待到指令加载. 如果你希望在首页加载模板, 你可以在一个`script`标签中将它作为这个页面的一部分包含进来, 就像这样:
```html
	<script type="text/ng-template" id="helloTemplateInline.html">
		<div>Hi there</div>
	</script>
```
这里的id属性很重要, 因为这是Angular用来存储模板的URL键. 稍候你将会使用这个id在指令的`templateUrl`中指定要插入的模板.

这个版本能够很好的载入而不需要服务器, 因为没有必要的`XMLHttpRequest`来获取内容.

最后, 你可以越过`$http`或者以其他机制来加载你自己的模板, 然后将它们直接设置在Angular中称为`$templateCache`的对象上. 我们希望在指令运行之前缓存中的这个模板可用, 因此我们将通过module上的run函数来调用它.
```js
	var appModule = angular.module('app', []);

	appModule.run(function($templateCache){
		$templateCache.put('helloTemplateCached.html', '<div>Hi there</div>');
	});

	appModule.directive('hello', function(){
		return {
			restrict: 'E',
			templateUrl: 'helloTemplateCached.html',
			replace: true;
		};
	});
```
你可能希望在产品中这么做, 仅仅作为一个减少所需的GET请求数量的技术. 你可以运行一个脚本将所有的模板合并到一个单独的文件中, 并在一个新的模块中加载它, 然后你就可以从你的主应用程序模块中引用它.

####Transclusion

除了替换或者追加内容, 你还可以通过`transclude`属性将原来的内容移到新模板中. 当设置为true时, 指令将删除原来的内容, 但是在你的模板中通过一个名为`ng-transclude`的指令重新插入来使它可用. 

我们可以使用transclusion来改变我们的示例:
```js
	appModule.directive('hello', function() {
		return {
			template: '<div>Hi there <span ng-transclude></span></div>',
			transclude: true
		};
	});
```
像这样来应用它:
```html
	<div hello>Bob</div>
```
你会看到: "Hi there Bob."

###编译和链接功能

虽然插入模板是有用的, 任何指令真正有趣的工作发生在它的`compile`和它的`link`函数中.

`compile`和`link`函数被指定为Angular用来创建应用程序实际视图的后两个阶段. 让我们从更高层次来看看Angular的初始化过程, 按一定的顺序:

**Script loads**

Angular加载和查找`ng-app`指令来判定应用程序界限.

**Compile phase(阶段)**

在这个阶段, Angular会遍历DOM节点以确定所有注册在模板中的指令. 对于每一个指令, 然后基于指令的规则(`template`,`replace`,`transclude`等等)转换DOM, 并且如果它存在就调用`compile`函数. 它的返回结果是一个编译过的`template`函数, 这将从所有的指令中调用`link`函数来收集.

**Link phase(阶段)**

创建动态的视图, 然后Angular会对每个指令运行一个`link`函数. `link`函数通常在DOM或者模型上创建监听器. 这些监听器用于视图和模型在所有的时间里都保持同步.

因此我们必须在编译阶段处理模板的转换, 同时在链接阶段处理在视图中修改数据. 按照这个思路, 指令中的`compile`和`link`函数之间主要的区别是`compile`函数处理模板自身的转换, 而`link`函数处理在模型和视图之间创造一个动态的连接. 作用域挂接到编译过的`link`函数正是在这个第二阶段, 并且通过数据绑定将指令变成活动的.

出于性能的考虑, 者两个阶段才分开的. `compile`函数仅在编译阶段执行一次, 而`link`函数会被执行多次, 对每个指令实例. 例如, 让我们来说说你上面使用的`ng-repeat`指令. 你并不想小勇`compile`, 这回导致在每次`ng-repeat`重复时都产生一个DOM遍历的操作. 相反, 你会希望一次编译, 然后链接.

虽然你毫无疑问的应该学习编译和链接之间的不同, 以及每个功能, 你需要编写的大部分的指令都不需要转换模板; 你还会编写大部分的链接函数.

让我们再看看每个语法来比较一下, 我们有:
```js
	compile: function compile(tElement, tAttrs, transclude) {
		return {
			pre: function preLink(scope, iElement, iAttrs, controller) {...},
			post: function postLink(scope, iElement, iAttrs, controller) {...}
		}
	}
```
以及链接:
```js
	link: function postLink(scope, iElement, iAttrs) {...}
```
注意这里有一点不同的是`link`函数获得了一个作用域的访问, 而`compile`没有. 这是因为在编译阶段期间, 作用域并不存在. 然而你有能力从`compile`函数返回`link`函数. 这些`link`函数能够访问到作用域.

还要注意的是`compile`和`link`都会获得一个到它们对应的DOM元素和这些元素属性[attributes]列表的引用. 这里的一点区别是`compile`函数是从模板中获得模板元素和属性, 并且会获取到`t`前缀. 而`link`函数使用模板创建的视图实例中获得它们的, 它们会获取到`i`前缀.

这种区别只存在于当指令位于其他指令中制造模板副本的时候. `ng-repeat`就是一个很好的例子.
```html
	<div ng-repeat="thing in things">
		<my-widget config="thing"></my-widget>
	</div>
```
这里, `compile`函数将只被调用一次, 而`link`函数在每次复制`my-widget`时都会被调用一次--等价于元素在things中的数量. 因此, 如果`my-widget`需要到所有`my-widget`副本(实例)中修改一些公共的东西, 为了提升效率, 正确的做法是在`compile`函数中处理. 

你可能还会注意到`compile`函数好哦的了一个`transclude`属性函数. 这里, 你还有机会以编写一个函数以编程的方式transcludes内容, 对于简单的的基于模板不足以transclusion的情况.

最后, `compile`可以返回一个`preLink`和`postLink`函数, 而`link`仅仅指向一个`postLink`函数. `preLink`, 正如它的名字所暗示的, 它运行在编译阶段之后, 但是会在指令链接到子元素之前. 同样的, `postLink`会运行在所有的子元素指令被链接之后. 这意味着如果你需要改变DOM结构, 你将在`posyLink`中处理. 在`preLink`中处理将会混淆流程并导致一个错误.

###作用域

你会经常希望从指令中访问作用域来监控模型的值并在它们改变时更新UI, 同时在外部时间造成模型改变时通知Angular. 者时最常见的, 当你从jQuery, Closure或者其他库中包裹一些非Angular组件或者实现简单的DOM事件时. 然后将Angular表达式作为属性传递到你的指令中来执行. 

这也是你期望使用一个作用域的原因之一, 你可以获得三种类型的作用域选项:

1. 从指令的DOM元素中获得**现有的作用域**.
2. 创建一个**新作用域**, 它继承自你闭合的控制器作用域. 这里, 你见过能够访问树上层作用域中的所有值. 这个作用域将会请求这种作用域与你DOM元素中其他任意指令共享它并被用于与它们通信.
3. 从它的父层**隔离出来的作用域**不带有模型属性. 当你在创建可重用的组件而需要从父作用域中隔离指令操作时, 你将会希望使用这个选项.

你可以使用下面的语法来创建这些作用域类型的配置:
```html
<table>
	<thead>
		<tr>
			<th>Scope Type</th>
			<th>Syntax</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>existing scope</td>
			<td>scope: false(如果不指定将使用这个默认值)
		</tr>
		<tr>
			<td>new scope</td>
			<td>scope: true</td>
		</tr>
		<tr>
			<td>isolate scope</td>
			<td>scope: { /* attribute names and binding style */ }</td>
		</tr>
	<tbody>
</table>
```
当你创建一个隔离的作用域时, 默认情况下你不需要访问父作用域中模型中的任何东西. 然而, 你也可以指定你想要的特定属性传递到你的指令中. 你可以认为是吧这些属性名作为参数传递给函数的.

注意, 虽然隔离的作用域不就成模型属性, 但它们仍然是其副作用域的成员. 就像所有其他作用域一样, 它们都有一个`$parent`属性引用到它们的父级.

你可以通过传递一个指令属性名的映射的方式从父作用域传递特定的属性到隔离的作用域中. 这里有三种合适的方式从父作用域中传递数据. 我们称这些传递数据不同的方式为"绑定策略". 你也可以可选的指定一个局部别名给属性名称.

以下是没有别名的语法:
```js
	scope: {
		attributeName1: 'BINDING_STRATEGY',
		attributeName2: 'BINDING_STRATEGY',...
	}
```
以下是使用别名的方式:
```js
	scope: {
		attributeAlias: 'BINDING_STRATEGY' + 'templateAttributeName',...
	}
```
绑定策略被定义为表6-4中的符号:

表6-4 绑定策略
```html
<table>
	<thead>
		<tr>
			<th>Symbol</th>
			<td>Meaning</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>@</td>
			<td>将属性作为字符串传递. 你也可以通过在属性值中使用插值符号{{}}来从闭合的作用域中绑定数据值.</td>
		</tr>
		<tr>
			<td>=</td>
			<td>使用你的指令的副作用域中的一个属性绑定数据到属性中.</td>
		</tr>
		<tr>
			<td>&</td>
			<td>从父作用域中传递到一个函数中, 以后调用.</td>
		</tr>
	</tbody>
</table>
```
这些都是相当抽象的概念, 因此让我们来看一个具体的例子上的变化来进行说明. 比方说我们希望创建一个`expander`指令在标题栏被点击时显示额外的内容.

收缩时它看起来如图6-2所示.

![6-2](figure/6-2.png)

图6-2 Expander in closed state

展开时它看起来如图6-3所示.

![6-3](figure/6-3.png)

图6-3 Expander in open state

我们会编写如下代码:
```html
	<div ng-controller="SomeController">
		<expander class="expander" expander-title="title">
			{{text}}
		</expander>
	</div>
```
标题(Cliked me to expand)和文本(Hi there folks...)的值来自于闭合的作用域中. 我们可以像下面这样来设置一个控制器:
```js
	function SomeController($scope) {
		$scope.title = 'Clicked me to expand';
		$scope.text = 'Hi there folks, I am the content that was hidden but is now shown.';
	}
```
然后我们可以来编写指令:
```js
	angular.module('expanderModule', [])
		.directive('expander', function(){
			return {
				restrict: 'EA',
				replace: true,
				transclude: true,
				scope: { title: '=expanderTitle'},
				template: '<div>' +
						'<div class="title" ng-click="toggle()">{{title}}</div>' +
						'<div class="body" ng-show="showMe" ng-transclude></div>' +
						'</div>',
				link: function(scope, element, attris){
					scope.showMe = false;
					scope.toggle = function toggle(){
						scope.showMe = !scope.showMe;
					}
				}
			}
		});
```
然后编写下面的样式:
```css
	.expander {
		border: 1px solid black;
		width: 250px;
	}
	.expander > .title {
		background-color: black;
		color: white;
		padding: .1em .3em;
		cursor: pointer;
	}
	.expander > .body {
		padding: .1em .3em;
	}
```
接下来让我们来看看指令中的每个选项是做什么的, 在表6-5中.

表6-5 Functions of elements
```html
<table>
	<thead>
		<tr>
			<th>FunctionName</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>restrict: EA</td>
			<td>一个元素或者属性都可以调用这个指令. 也就是说, \<expander ...\>...\</expander\>与\<div expander...\>...\</div\>是等价</td>
		</tr>
		<tr>
			<td>replace:true</td>
			<td>使用我们提供的模板替换原始元素</td>
		</tr>
		<tr>
			<td>transclude:true</td>
			<td>将原始元素的内容移动到我们所提供的模板的另外一个位置.</td>
		</tr>
		<tr>
			<td>scope: {title: =expanderTitle}</td>
			<td>创建一个称为`title`的局部作用域, 将父作用域的属性数据绑定到声明的`expanderTitle`属性中. 这里, 我们重命名title为更方便的expanderTitle. 我们可以编写`scope: { expanderTitle: '='}`, 那么在模板中我们就要使用`expanderTitle`了. 但是在其他指令也有一个`title`属性的情况下, 在API中消除title的歧义和只是重命名它用于在局部使用是有意义的. 请注意, 这里自定义指令也使用了相同的驼峰式命名方式作为指令名.</td>
		</tr>
		<tr>
			<td>template: \<'div'\>+</td>
			<td>声明这个指令要插入的模板. 注意我们使用了`ng-click`和`ng-show`来显示和隐藏自身并使用`ng-transclude`声明了原始内容会去哪里. 还要注意的是transcluded的内容能够访问父作用域, 而不是指令闭合中的作用域.</td>
		</tr>
		<tr>
			<td>link...</td>
			<td>设置`showMe`模型来检测expander的展开/关闭状态, 同时定义在用于点击`title`这个div的时候调用定义的`toggle()`函数.</td>
		</tr>
	</tbody>
</table>
```
如果我们像使用更多有意义的东西来在模板中定义`expander title`而不是在模型中, 我们还可以使用传递通过在作用域声明中使用`@`符号传递一个字符串风格的属性, 就像下面这样:
```js
	scope: { title: '@expanderTitle'},
```
在模板中我们就可以实现相同的效果:
```html
	<expander class="expander" expander-title="Click mr to expand">
		{{text}}
	</expander>
```
注意, 对于@策略我们仍然可以通过使用插入法将title数据绑定到我们的控制器作用域中:
```html
	<expander class="expander" expander-title="{{title}}">
		{{text}}
	</expander>
```
###操作DOM元素

传递给指令的`link`和`compile`函数的`iElement`和`tElement`是包裹原生DOM元素的引用. 如果你已经加载了jQuery库, 你也可以使用你已经习惯使用的jQuery元素.

如果你没有使用jQuery, 你也可以使用Angular内置的被称为jqLite的包装器. 它提供了一个jQuery的子集便于我们在Angular中创建任何东西. 对于多数应用程序, 你都可以单独使用这些API做任何你需要做的事情.

如果你需要直接访问原生的DOM元素你可以通过使用`element[0]`访问对象的第一个元素来获得它.

你可以在Angular文档的`angular.element()`查看它所支持的API的完整列表--你可以用这个函数创建你自己的jqLite包装的DOM元素. 它包含像`addClass()`, `bind()`, `find()`, `toggleClass()`等等其他方法. 其次, 其中大多数有用的核心方法都来自于jQuery, 但是它的代码亮更少.

对于其他的jQuery API, 元素在Angular中都有指定的函数. 这些都是存在的, 无论你是否使用完整的jQuery库.

Table 6-6. Angular specific functions on an element
```html
<table>
	<thead>
		<tr>
			<th>Function</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>controller(name)</td>
			<td>当你需要直接与一个控制器通信时, 这个函数会返回附加到元素上的控制器. 如果没有现有的元素, 它会遍历DOM元素并查找最近的父控制器. name参数是可选的, 它是用于指定相同元素上其他指令名称的. 如果提供这个参数, 它会从相应的指令中返回控制器. 这个名字应该与所有指令一样使用一个驼峰式的格式. 也就是说, 使用`ngModle`来替换`ng-model`的形式.</td>
		</tr>
		<tr>
			<td>injector()</td>
			<td>获取当前元素或者它的父元素的注入器. 它还允许你访问在这些元素上定义的所依赖的模块.</td>
		</tr>
		<tr>
			<td>scope()</td>
			<td>返回当前元素或者它最近的父元素的作用域.</td>
		</tr>
		<tr>
			<td>inheritedData()</td>
			<td>正如jQuery的`data()`函数, `inheritedData()`会在一个封闭的方式中设置和获取数据. 此外还能够从当前元素获取数据, 它也会遍历DOM元素并查找值.</td>
		</tr>
	</tbody>
</table>
```
这里有一个例子, 让我们重新定义之前的expander例子而不使用`ng-show`和`ng-click`. 它看起来像下面这样:
```js
	angular.module('expanderModule', [])
		.directive('expander', function(){
			return {
				restrict: 'EA',
				replace: true,
				transclude: true,
				scope: { title: '=expanderTitle' },
				template: '<div>' +
						'<div class="title">{{title}}</div>' +
						'<div class="body closed" ng-transclude></div>' +
						'</div>',
						
				link: function(scope, element, attrs) {
					var titleElement = angular.element(element.children().eq(0));
					var bodyElement = angular.element(element.children().eq(1));
					
					titleElement.bind('click', toggle);
					
					function toggle() {
						bodyElement.toggleClass('closed');
					}
				}
			}
		});
```
这里我们从模板中移除了`ng-click`和`ng-show`. 相反的时, 当用户单击expander的title时执行所定义的行为, 我们将从title元素创建一个jqLite元素, 然后它绑定一个click事件并将`toggle()`函数作为它的回调函数. 在`toggle()`函数中, 我们在expander的body元素上调用`toggleClass()`来添加或者移除一个被称为`closed`的class(HTML类名), 这里我们给元素设置了一个值为`display: none`的类, 像下面这样:
```css
	.closed {
		display: none;
	}
```
	
###控制器

当你有相互嵌套的指令需要相互通信时, 你可以通过控制器做到这一点. 比如一个\<menu\>可能需要知道它自身内部的\<menu-item\>元素它才能适当的显示或者隐藏它们. 同样的对于一个\<tab-set\>也需要知道它的\<tab\>元素, 或者一个\<grid-view\>要知道它的\<grid-element\>元素.

正如前面所展示的, 创建一个API用于在指令之间沟通, 你可以使用控制器属性的语法声明一个控制器作为一个指令的一部分:

	controller: function controllerConstructor($scope, $element, $attrs, $transclude)
	
这个控制器函数就是依赖注入, 因此这里列出的参数都是潜在的可用并且全部都是可选的--它们可以按照任意顺序列出. 它们也仅仅只是可用服务的一个子集.

其他的指令也可以使用`require`属性语法将这个控制器传递给它们. 完整的`require`的形式看起来像:

	require: '^?directiveName'
	
关于`require`字符串参数的说明可以在表6-7中找到.

Table 6-7. Options for required controllers

<table>
	<thead>
		<tr>
			<th>Option</th>
			<th>Usage</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>directiveName</td>
			<td>这个指令驼峰式命名规范应该是来自于控制器. 因此如果我们的\<my-menu-item\>s指令需要在它的父元素\<my-menu\>上找到一个控制器, 我们需要将它编写为`myMenu`.</td>
		</tr>
		<tr>
			<td>^</td>
			<td>默认情况下, Angular会从同一元素的命名指令中获取控制器. 加入可选的^符号表示总是遍历DOM树来以查找指令. 对于\<my-menu\>示例, 我们需要添加这个符号; 最终的字符就是`\^myMenu`.</td>
		</tr>
		<tr>
			<td>?</td>
			<td>如果你所需要的控制器没有找到, Angular将抛出一个异常信息来告诉你遇到了什么问题. 添加一个?符号给字符串就是说这个控制器时可选的并且如果没有找到控制器它不应该抛出一个异常. 虽然者听起来不可能, 但是如果我们希望让\<my-menu-item\>不需要使用一个\<my-menu\>容器, 我们可以将这个添加给最终所需要的字符串?\^myMenu.</td>
		</tr>
	</tbody>
</table>

例如, 让我们重写我们的expander指令用于一组称为"手风琴"的组件, 它可以确保当你打开一个expander时, 其他的都会自动关闭. 它看起来如图6-4所示.

![6-4](figure/accordion.png)

图 6-4. Accordion component in multiple states

首先, 让我们编写处理手风琴菜单的accordion指令. 这里我们将添加我们的控制器构造器方法来处理手风琴:

	appModule.directive('accordion', function() {
		return {
			restrict: 'EA',
			replace: true,
			transclude: true,
			template: '<div ng-transclude></div>',
			controller: function() {
				var expanders = [];
				this.gotOpened = function(selectedExpander) {
					angular.forEach(expanders, function(expander){
						if(selectedExpander != expander) {
							expander.showMe = false;
						}
					});
				}
				
				this.addExpander = function(expander) {
					expanders.push(expander);
				}
			}
		}
	});
	
这里我们定义了一个`addExpander()`函数给expanders便于调用它来注册自身实例. 我们也创建了一个`gotOpened()`函数给expanders便于调用, 因而让accordion的控制器可以知道它能够去关闭任何其他展开的expanders.

在expander指令自身中, 我们将从它的父元素扩展它所需要的accordion控制器并在适当的时间里调用`addExpander()`和`gotOpened()`方法.

	appModule.directive('expander', function(){
		return {
			restrict: 'EA',
			replace: true,
			transclude: true,
			require: '^?accordion',
			scope: { title: '=expanderTitle' }
			template: '<div>' +
					'<div class="title" ng-click="toggle()">{{title}}</div>' +
					'<div class="body" ng-show="showMe" ng-transclude></div>' +
					'</div>',
			link: function(scope, element, attrs, accordionController) {
				scope.showMe = false;
				accordionController.addExpander(scope);
				
				scope.toggle = function toggle() {
					scope.showMe = !scope.showMe;
					accordionController.gotOpened(scope);
				}
			}
		}
	});

注意在手风琴指令的控制器中我们创建了一个API, 通过它可以让expander可以相互通信.

然后我们可以编写一个模板来使用这些指令, 最后生成的结果整如图6-4所示.

	<body ng-controller="SomeController">
		<accordion>
			<expander class="expander" ng-repeat="expander in expanders" expander-title="expander.title">
			{{expander.text}}
			</expander>
		</accordion>
	</body>

当然接下是对应的控制器:

	function SomeController($scope){
		$scope.expanders = [
			{title: 'Click me to expand',
			 text: 'Hi there folks, I am the content that was hidden but is now shown.'},
			{title: 'Click this',
			 text: 'I am even better text than you have seen previously'},
			{title: 'No, click me!',
			 text: 'I am text should be seen before seeing other texts'}
		];
	}
	
##小结

正如我们所看到的, 指令允许我们扩展HTML的语法并让很多应用程序按照我们声明的意思工作. 指令使重用(代码重用/组件复用)变得轻而易举--从使用`ng-model`和`ng-controller`配置你的应用程序, 到处理模板的任务的像`ng-repeat`和`ng-view`指令, 再到前几年被限制的可复用的组件像数据栅格, 饼图, 工具提示和选项卡等等.
