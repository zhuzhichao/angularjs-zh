# 第一章 AngularJS简介

我们创造惊人的基于Web的应用程序的能力是令人难以置信的，但是创建这些应用程序时所涉及的复杂性也是让人不可思议的。我们的 Angular 团队希望减轻我们在参与开发 AJAX 应用程序时的痛苦。在 Google，我们曾经在构建像Gmail、Maps 、Calendar 以及其他大型Web应用程序时经历了最痛苦的教训。我想我们也许能够利用这些经验来帮助其他开发人员。

我们希望在编写 Web 应用程序时感觉更像是第一次我们编写了几行代码然后站在后面惊讶于它所发生的事情。我们希望编码的过程感觉更像是创造而不是试图满足 Web 浏览器的奇怪的内部运行工作。

与此同时，我们还希望我们所面对的工作环境来帮助我们作出设计选择，使应用程序的创建变得很简单并且从一开始就让人们很容易理解它，并且希望伴随着应用程序的不断成长，正确的设计选择会让我们的应用程序易于测试, 扩展和维护。

我们试图在 Angular 这个框架中做到这些。我们也为我们已经取得的成果感到非常高兴。这很大程度上归功于 Angular 开源社区中每个成员的出色工作和相互帮助，同时也教会了我们很多东西。我们希望你也加入到我们的社区中来，并帮助我们一起努力让 Angular 变得更好。

你可以在我们的 [Github 主页](https://github.com/shyamseshadri/angularjs-book)的仓库中查看那些较大的或者较复杂的例子和代码片段，你也可以拉取分支以及自行研究这些代码。

## 目录

- [概念](#概念)
	- [客户端模板](#客户端模板) 
	- [模型/视图/控制器(MVC)](#模型视图控制器MVC)
	- [数据绑定](#数据绑定)
	- [依赖注入](#依赖注入)
	- [指令](#指令)
- [示例：购物车](#示例购物车)
- [小结](#小结)


## 概念

在你将使用的 Angular 构建应用的过程中有几个核心的概念。事实上，任何这些概念并不是我们新发明的。相反，我们从其他开发环境借鉴了大量成功的做法(经验)，然后使用包含 HTML ，浏览器以及许多其他 Web 标准的方式实现了它。

### 客户端模板

多页面的 Web 应用程序都是通过装配和连接服务器上数据来创建 HTML ，然后将构建完成的 HTML 页面发送到浏览器中。大多数的单页应用程序--也就是我们所知道的 AJAX 应用程序--从某种程度上讲，它的这一点一直做的很好。然而 Angular 以不同的方式实现了将模板和数据推送到浏览器中来装配它们。然后服务器角色只是为模板提供静态资源以及为模板适当地提供数据。

让我们来看一个例子，在 Angular 中如何在浏览器中组装模板和数据。按照惯例我们使用一个 Hello，World 的例子，但是注意这里并不是编写一个 "Hello，World" 的单一字符串，而是将问候 "Hello" 作为我们稍候可能会改变的数据来构建。

针对这个例子，我们在 `hello.html` 中来创建我们的模板：

	<html ng-app>
	<head>
		<script src="angular.js"></script>
		<script src="controller.js"></script>
	</head>
	<body>
		<div ng-controller="HelloController">
			<p>{{greeting.text}}, World</p>
		</div>
	</body>
	</html>    

接下来我们将逻辑编写在 `controllers.js` 中：

	function HelloController($scope){
		$scope.greeting = {text: 'Hello'};
	}

最后将我们 `hello.html` 载入任意浏览器中，我们将看到如图1-1所示的信息：

![Hello](figure/hello.png)

图1-1 Hello World

> 译注：你也可以自行修改 `controllers.js` 中的数据来查看效果。

与现在我们使用广泛的大多数方法相比，这里有一些有趣的事情需要注意：

+ HTML 并没有类(class 属性)或者 IDs 来标识在哪里添加事件监听器。

+ 当 `HelloController` 设置 `greeting.text` 为 `Hello` 时，我们并没有注册任何事件监听器或者编写任何回调函数。

+ `HelloController` 只是一个很普通的 JavaScript 类，并且它并没有继承任何 Angular 所提供的信息。

+ `HelloController` 获取了它所需要的 `$scope` 对象，我们无需创建它。

+ 我们并没有自己手动的调用 `HelloController` 的构造函数，在这里暂时也不打算弄清楚什么时候调用它。

接下来我们会看到更多与传统开发方式之间的差异，但是现在我们应该清楚：Angular 应用程序的结构与过去类似的应用程序是完全不同的。

那么为什么我们会做出这些设计选择以及 Angular 是如何工作的呢？接下来先让我们来看看 Angular 从其他地方借鉴的一些好的思想(概念/经验)。

### 模型/视图/控制器(MVC)

MVC 应用程序结构是20世纪70年代作为 Smalltalk 的一部分引入的。从 Smalltalk 开始，MVC 在几乎每一个涉及用户界面的桌面应用程序开发环境中都变得流行起来。无论你是使用 C++ ，Java ，还是 Object-C ，都可以找到使用 MVC 的场景。然而，直到最近，MVC 的思想才开始在国外的 Web 开发中应用。

MVC 背后的核心思想是你可以在你的代码中清晰的分离数据管理(模型)，应用程序逻辑(控制器)以及给用户呈现数据(视图)。

视图会从模型中获取数据来显示给用户。当用户通过点击或者输入操作与应用程序进行交互时，控制器就通过修改数据模型来响应用户的操作。最后，被修改的模型会通知视图它已经发生了变化，因此视图可以更新它所显示的信息。 

在 Angular 应用程序中，视图就是文档对象模型 (DOM) ，控制器是 JavaScript 类，最后模型中的数据便是存储在对象中的属性(属性值)。

> JavaScript 并没有类的概念，这里的意思就是用构造函数的方式来处理控制器部分，其他地方所提及的 JavaScript 类的概念读者需要自行甄别。

我们认为 MVC 的灵活性主要主要表现在以下几方面。首先，它给你提供了一个只能的模型用于告诉在哪里存储什么样的数据，因此你不需要每次都重新构造它。当其他人参与到你的项目中合作开发时，便能够即时理解你已经编写好的部分，因为他们会知道你使用了 MVC 结构来组织你的代码。也许最重要的是，它给你提供了一个极大的好处，是你的程序更易于扩展，维护和测试。

**译注**

1. MVC 是软件工程中的一种软件架构模式 - [MVC](http://zh.wikipedia.org/wiki/MVC)。
2. Smalltalk 是一门面向对象的程序设计语言 - [Smalltalk](http://zh.wikipedia.org/wiki/Smalltalk)。

### 数据绑定

之前常见的 AJAX 单页应用程序，像 Rails ，PHP 或者 JSP 平台都是在通过在将页面发送给用户显示之前将数据合并到 HTML 字符串中来帮助我们创建用户界面 (UI)。

像 jQuery 这样的库也是将模型扩展到客户端并让我们使用类似的风格，但是它只有单独更新部分 DOM 的能力，而不能更新整个页面。在 AngularJS 中，我们将数据合并到 HTML 模板的字符串中，然后通过在一个占位元素中设置 `innerHTML` 将返回的结果插入到我们的目标 DOM 元素中。

这一切都工作得很好，但是当你希望插入新的数据到用户界面 (UI) 中时，或者基于用户的输入改变数据，你需要做一些相当不平凡的工作以确保你的数据变为正确的状态，无论数据是在用户界面中 (UI) 还是 JavaScript 属性中。

但是如果我们可以不用编写代码就能处理好所有这些工作会怎样呢？如果我们可以只需声明用户界面的哪些部分对应哪些 JavaScript 属性并且让它们自动同步又会怎样呢？这种编程风格被称为数据绑定。由于 MVC 可以在我们编写视图和模型时减少代码，因此我们将它引入到了 Angular 中。大部分将数据从一处迁移到另一处的工作都会自动完成。

下面来看看这一行为，我们继续使用前面的 "Hello World" 的例子，但是我们会让它"动"起来。原来是一旦 HelloController 设置了其模型 `greeting.text` 的值，它便不再会改变。首先让我们通过添加一个根据用户输入改变 `greeting.text` 值的文本输入框来改变这个例子，让它能够"动"起来。

下面是新的模板：

	<html ng-app>
	<head>
		<script src="angular.js"></script>
		<script src="controllers.js"></script>
	</head>
	<body>
		<div ng-controller="HelloController">
			<input ng-model="greeting.text" />
			<p>{{greeting.text}}, World</p>
		</div>
	</body>
	</html>

`HelloController` 控制器可以保持不变。

将它载入到浏览器中，我们将看到如图1-2所示屏幕截图：

![Hello with data binding](figure/hello2.png)

图1-2 应用程序的默认状态

如果我们使用 'Hi' 文本替换输入框中的 'Hello' ，我们将就会看到如图1-3所示截图：

![Hi](figure/hello3.png)

图1-3 改变文本框值之后的应用程序

我们并没有在输入字段上注册一个改变值的事件监听器，我们有一个将会自动更新的 UI。同样的情况也适用于来自服务器端的改变。在我们的控制器中，我们可以构造一个服务器端的请求，获取响应，然后设置 `$scope.greeting.text` 等于它返回的值。Angular 会自动更新文本输入框和双大括号中的 text 字段为该返回值。

### 依赖注入

之前我们提到过，在 `HelloController` 中有很多东西都可以重复，在这里我们并没有编写。例如，`$scope` 对象会将数据绑定自动传递给我们；我们不需要通过调用任何函数来创建它。我们只是通过将它放置在 `HelloController` 的构造器中来请求它。

正如我们将在后面的章节中会看到，`$scope` 并不是我们唯一可以访问的东西。如果我们希望将数据绑定到用户浏览器的 URL 地址中，我们可以通过将数据绑定植入我们控制器的 `$location` 中来访问管理数据。就像这样：

	function HelloController($scope, $location){
		$scope.greeting = {text: 'Hello'};
		//use $location for something good here...
	}

这个神奇的效果是通过 Angular 的依赖注入系统实现的。依赖注入让我们遵循这种开发风格，而不是创建依赖，我们的类只需要知道它需要什么。

这个效果遵循一个被称为得墨忒耳定律的设计模式，也被称作最少知识原则。由于我们的 `HelloController` 的工作只是设置 greeting 模型的初试状态，这种模式会告诉你无需担心其他的事情，例如 `$scope` 是如何创建的，或者在哪里可以找到它。

这个特性并不只是通过 Angular 框架创建的对象才有。你最终创建的任何对象和服务也可以以同样的方式注入。

### 指令

Angular 最好的部分之一就是你可以编写你的模板如同 HTML 一样。之所以可以这样做，是因为在这个框架的核心部分我们已经包含了一个强大的 DOM 解析引擎，它允许你扩展 HTML 的语法。

我们已经在我们的模板中看到了一些不属于 HTML 规范的新属性。例如包含在双花括号中的数据绑定，用于指定哪个控制器对应哪部分视图的 `ng-controller` ，以及将输入框绑定到模型部分的 `ng-model` 。我们称之为 HTML 扩展指令。

Angular 自带了许多指令以帮助你定义应用程序的视图。后面我们就会看到更多的指令。这些指令可以定义用来帮助我们定义常见的视图作为模板。它们可以用于声明设置你的应用程序如何工作或者创建可复用的组件。

你并不仅限于使用 Angular 自带的指令。你也可以编写你自己的扩展 HTML 模板来做你想做的任何事。

## 示例：购物车

接下来让我们来看一个较大的例子，它展示了更多的 Angular 的能力。想象一下，我们要创建一个购物应用程序。在应用程序的某个地方，我们需要展示用户的购物车并允许他编辑。接下来我们直接跳到那部分。

	<html ng-app>
	<head>
	<title>Your Shopping Cart</title>
	</head>
	<body ng-controller="CartController">
		<h1>Your Order</h1>
		<div ng-repeat="item in items">
			<span>{{item.title}}</span>
			<input ng-model="item.quantity" />
			<span>{{item.price | currency}}</span>
			<span>{{item.price * item.quantity | currency}}</span>
			<button ng-click="remove($index)">Remove</button>
		</div>
		<script src="angular.js"></script>
		<script>
		function CartController($scope){
			$scope.items = [
				{title: 'Paint pots', quantity: 8, price: 3.95},
				{title: 'Polka dots', quantity: 17, price: 12.95},
				{title: 'Pebbles', quantity: 5, price: 6.95}
			];

			$scope.remove = function(index){
				$scope.items.splice(index, 1);
			}
		}
		</script>
	</body>
	</html>

最终用户界面截屏如图1-4所示：

![shopping-cart](figure/shopping-cart.png)

图1-4 购物车用户界面

下面是关于这里发生了什么的简短参考。本书其余的部分提供了更深入的讲解。

让我们从顶部开始：

	<html ng-app>

`ng-app` 属性告诉 Angular 它应该管理页面的哪一部分。由于我们把它放在 `<html>` 元素中，这就会告诉 Angular 我们希望它管理整个页面。这往往也是你所希望的，但是如果你是在现有应用程序中集成 Angular 并使用其他方式管理页面，那么你可能希望把它放在应用程序中的某个 `<div>` 元素中。

	<body ng-controller="CartController">

在 Angular 中，你用于管理页面某个区域的 JavaScript 类被称为控制器。通过在 `body` 标签中包含一个控制器，那么说明我的这个 `CartController` 将会管理 `<body>` 和 `</body>` 之间的所有东西。

	<div ng-repeat="item in items">

`ng-repeat` 的意思就是给被称为 `items` 数组中的每个元素都复制一次当前 `<div>` 里面的 DOM 结构。在每一个复制的 div 副本中，我们都会给当前元素设置一个名为 `item` 的属性，这样我们就可以在模板中使用它。你可以看到，结果返回的三个 `<div>` 中的每一个都包含产品的标题，数量，单价，总价以及一个用于移除当前条目的按钮。

	<span>{{item.title}}</span>


正如我们在 "Hello, World" 例子中所示，数据绑定通过 `{{ }}` 让我们将一个变量的值插入到页面某部分中并保持数据同步。完整的表达式 `{{item.title}}` 会以迭代的方式检索当前 item ，然后将该 item 的 title 属性的内容插入到 DOM 中。

	<input ng-model="item.quantity">

`ng-model` 在输入字段和 `item.quantity` 的值之间定义并创建了数据绑定行为。

`<span>` 中的 `{{ }}` 设置了一个单项关联，它的意思就是"在这里插入一个值"。我们就是想要这个效果，但是应用程序也需要知道用户什么时候改变了商品数量，以便它可以改变选购商品的总价。

因此我们通过使用 `ng-model` 来同步模型中的变化。`ng-model` 声明了将 `item.quantity` 的值插入到文本域中，每当用户输入一个新的值时它也会自动更新 `item.quantity` 的值。

	<span>{{item.price | currency}}</span>
	<span>{{item.price * item.quantity | currency}}</span>


我们还希望单价和总价被格式化为美元形式。Angular 自带了一个被称为过滤器的特性来让我们转换文本，在这里我们捆绑了一个被称为 `currency` 的过滤器用于给我们处理这里的美元格式操作。在下一章我们将会看到更多的过滤器。

	<button ng-click="remove($index)">Remove</button>

这允许用户通过点击产品旁边的 `remove` 按钮来从他们的购物车中移除所选择的商品条目。在这里我们设置它点击这个按钮时调用 `remove()` 函数。我们还给它传递了一个 `$index` 参数，这个参数包含了它在 `ng-repeat` 中的索引值，这样我们就会知道哪一项将会被移除。

	function CartController($scope)

这个 `CartContoller` 用于整个管理购物车应用的逻辑。这会告诉 Angular ，在这里它要给控制器传递一个叫做 `$scope` 的参数。这个 `$scope` 用于帮助我们在用户界面中将数据绑定到元素中。

	$scope.items = [
		{title: 'Paint pots', quantity: 8, price: 3.95},
		{title: 'Polka dots', quantity: 17, price: 12.95},
		{title: 'Pebbles', quantity: 5, price: 6.95}
	];

通过定义 `$scope.items` ，我创建一个虚拟数据哈希表[数组]来表示用户的购物车。我们希望它可以用于 UI 中的数据绑定，因此将它添加到 `$scope` 中。

当然，真正的购物车应用不可能只是在内存中工作，它需要访问服务器中正确存储的数据。我们将在后面的章节中讨论这些。

	$scope.remove = function(index){
		$scope.items.splice(index, 1);
	}

我们还希望将 `remove()` 函绑定在 UI 中使用，因此我们也将它添加到 `$scope` 中。对于这个内存版本的购物车，`remove()` 函数可以即时从数组中删除项目。由于 `<div>` 列表是通过 `ng-repeat` 创建的数据绑定，所以当项目消失时列表会自动收缩。记住，每当用户在 UI 界面上点击一个 Remove 按钮时，`remove()` 函数就会被调用。

##小结

我们已经看到了 Angular 最基本的用法以及一些非常简单的例子。本书后面的部分将专注于介绍这个框架所提供的更多功能。
