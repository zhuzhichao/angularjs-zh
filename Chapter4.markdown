#分析一个AngularJS应用程序

在第2章中, 我们已经讨论了一些AngularJS常用的功能, 然后在第3章讨论了该如何结构化开发应用程序. 现在, 我们不再继续深单个的技术点, 第4章将着眼于一个小的, 实际的应用程序进行讲解. 我们将从一个实际有效的应用程序中感受一下我们之前已经讨论过的(示例)所有的部分.

我们将每次介绍一部分, 然后讨论其中有趣和关联的部分, 而不是讨论完整应用程序的前端和核心, 最后在本章的后面我们会慢慢简历这个完整的应用程序.

## 目录

- [应用程序](#应用程序)
- [模型, 控制器和模板之间的关系](#模型-控制器和模板之间的关系)
- [模型](#模型)
- [控制器, 指令和服务](#控制器-指令和服务)
	- [服务](#服务)
	- [指令](#指令)
	- [控制器](#控制器)
- [模板](#模板)
- [测试](#测试)
	- [单元测试](#单元测试)
	- [脚本测试](#脚本测试)

##应用程序

Guthub是一个简单的食谱管理应用, 我们设计它用于存储我们超级美味的食谱, 同时展示AngularJS应用程序的各个不同的部分. 这个应用程序包含以下内容:

+ 一个两栏的布局
+ 在左侧有一个导航栏
+ 允许你创建新的食谱
+ 允许你浏览现有的食谱列表

主视图在左侧, 其变化依赖于URL, 或者食谱列表, 或者单个食谱的详情, 或者可添加新食谱的编辑功能, 或者编辑现有的食谱. 我们可以在图4-1中看到这个应用的一个截图:

![Guthub](figure/4-1.png)

Figure 4-1. Guthub: A simple recipe management application

这个完整的应用程序可以在我们的Github中的`chapter4/guthub`中得到.

##模型, 控制器和模板之间的关系

在我们深入应用程序之前, 让我们来花一两段文字来讨论以下如何将标题中的者三部分在应用程序中组织在一起工作, 同时来思考一下其中的每一部分.

`model`(模型)是真理. 只需要重复这句话几次. 整个应用程序显示什么, 如何显示在视图中, 保存什么, 等等一切都会受模型的影响. 因此你要额外花一些时间来思考你的模型, 对象的属性将是什么, 以及你打算如何从服务器获取并保存它. 视图将通过数据绑定的方式自动更新, 所以我们的焦点应该集中在模型上.

`controller`保存业务逻辑: 如何获取模型, 执行什么样的操作, 视图需要从模型中获取什么样的信息, 以及如何将模型转换为你所想要的. 验证职责, 使用调用服务器, 引导你的视图使用正确的数据, 大多数情况下所有的这些事情都属于控制器来处理.

最后, `template`代表你的模型将如何显示, 以及用户将如何与你的应用程序交互. 它主要约束以下几点:

+ 显示模型
+ 定义用户可以与你的应用程序交互的方式(点击, 文本输入等等)
+ 应用程序的样式, 并确定何时以及如何显示一些元素(显示或隐藏, hover等等)
+ 过滤和格式化数据(包括输入和输出)

要意识到在Angular中的模型-视图-控制器涉及模式中模板并不是必要的部分. 相关, 视图是模板获取执行被编译后的版本. 它是一个模板和模型的组合.

任何类型的业务逻辑和行为都不应该进入模板中; 这些信息应该被限制在控制器中. 保持模板的简单可以适当的分离关注点, 并且可以确保你只使用单元测试的情况下就能够测试大多数的代码. 而模板必须使用场景测试的方式来测试.

但是, 你可能会问, 在哪里操作DOM呢? DOM操作并不会真正进入到控制器和模板中. 它会存在于Angular的指令中(有时候也可以通过服务来处理, 这样可以避免重复的DOM操作代码). 我们会在我们的GutHub的示例文件中涵盖一个这样的例子.

废话少说, 让我们来深入探讨一下它们.

##模型

对于应用程序我们要保持模型非常简单. 这一有一个菜谱. 在整个完整的应用程序中, 它们是一个唯一的模型. 它是构建一切的基础.

每个菜谱都有下面的属性:

+ 一个用于保存到服务器的ID
+ 一个名称
+ 一个简短的描述
+ 一个烹饪说明
+ 是否是一个特色的菜谱
+ 一个成份数组, 每个成分的数量, 单位和名称

就是这样. 非常简单. 应用程序的中一切都基于这个简单的模型. 下面是一个让你食用的示例菜谱(如图4-1一样):
```js
	{
		'id': '1',
		'title': 'Cookies',
		'description': 'Delicious. crisp on the outside, chewy' +
			' on the outside, oozing with chocolatey goodness' +
			' cookies. The best kind',
		'ingredients': [
			{
				'amount': '1',
				'amountUnits': 'packet',
				'ingredientName': 'Chips Ahoy'
			}
		],
		'instructions': '1. Go buy a packet of Chips Ahoy\n'+
			'2. Heat it up in an oven\n' +
			'3. Enjoy warm cookies\n' +
			'4. Learn how to bake cookies from somewhere else'
	}
```
下面我们将会看到如何基于这个简单的模型构建更复杂的UI特性.

##控制器, 指令和服务

现在我们终于可以得到这个让我们牙齿都咬到肉里面去的美食应用程序了. 首先, 我们来看看代码中的指令和服务, 以及讨论以下它们都是做什么的, 然后我们我们会看看这个应用程序需要的多个控制器.

###服务
```js
	//this file is app/scripts/services/services.js

	var services = angular.module('guthub.services', ['ngResource']);

	services.factory('Recipe', ['$resource', function(){
		return $resource('/recipes/:id', {id: '@id'});
	}]);

	services.factory('MultiRecipeLoader', ['Recipe', '$q', function(Recipe, q){
		return function(){
			var delay = $q.defer();
			Recipe.query(function(recipes){
				delay.resolve(recipes);
			}, function(){
				delay.reject('Unable to fetch recipes');
			});
			return delay.promise;
		};
	}]);

	services.factory('RecipeLoader', ['Recipe', '$route', '$q', function(Recipe, $route, $q){
		return function(){
			var delay = $q.defer();
			Recipe.get({id: $route.current.params.recipeId}, function(recipe){
				delay.resolve(recipe);
			}, function(){
				delay.reject('Unable to fetch recipe' + $route.current.params.recipeId);
			});
			return delay.promise;
		};
	}]);
```
首先让我们来看看我们的服务. 在33页的"使用模块组织依赖"小节中已经涉及到了服务相关的知识. 这里, 我们将会更深一点挖掘服务相关的信息.

在这个文件中, 我们实例化了三个AngularJS服务.

有一个菜谱服务, 它返回我们所调用的Angular Resource. 这些是RESTful资源, 它指向一个RESTful服务器. Angular Resource封装了低层的`$http`服务, 因此你可以在你的代码中只处理对象.

注意单独的那行代码 - `return $resource` - (当然, 依赖于`guthub.services`模型), 现在我们可以将`recipe`作为参数传递给任意的控制器中, 它将会注入到控制器中. 此外, 每个菜谱对象都内置的有以下几个方法:

+ Recipe.get()
+ Recipe.save()
+ Recipe.query()
+ Recipe.remove()
+ Recipe.delete()

> 如果你使用了`Recipe.delete`方法, 并且希望你的应用程序工作在IE中, 你应该像这样调用它: `Recipe[delete]()`. 这是因为在IE中`delete`是一个关键字.

对于上面的方法, 所有的查询众多都在一个单独的菜谱中进行; 默认情况下`query()`返回一个菜谱数组.

`return $resource`这行代码用于声明资源 - 也给了我们一些好东西:

1. 注意: URL中的id是指定的RESTFul资源. 它基本上是说, 当你进行任何查询时(`Recipe.get()`), 如果你给它传递一个id字段, 那么这个字段的值将被添加早URL的尾部.

也就是说, 调用`Recipe.get{id: 15})将会请求/recipe/15.

2. 那第二个对象是什么呢? {id: @id}吗? 是的, 正如他们所说的, 一行代码可能需要一千行解释, 那么让我们举一个简单的例子.

比方说我们有一个recipe对象, 其中存储了必要的信息, 并且包含一个id.

然后, 我们只需要像下面这样做就可以保存它:
```js
	//Assuming existingRecipeObj has all the necessary fields,
	//including id(say 13)
	var recipe = new Recipe(existingRecipeObj);
	recipe.$save();
```
这将会触发一个POST请求到`/recipe/13`.

`@id`用于告诉它, 这里的id字段取自它的对象中同时用于作为id参数. 这是一个附加的便利操作, 可以节省几行代码.

在`apps/scripts/services/services.js`中有两个其他的服务. 它们两个都是加载器(Loaders); 一个用于加载单独的食谱(RecipeLoader), 另一个用于加载所有的食谱(MultiRecipeLoader). 这在我们连接到我们的路由时使用. 在核心上, 它们两个表现得非常相似. 这两个服务如下:

1. 创建一个`$q`延迟(deferred)对象(它们是AngularJS的promises, 用于链接异步函数).
2. 创建一个服务器调用.
3. 在服务器返回值时resolve延迟对象.
4. 通过使用AngularJS的路由机制返回promise.

> **AngularJS中的Promises**
>
> 一个promise就是一个在未来某个时刻处理返回对象或者填充它的接口(基本上都是异步行为). 从核心上讲, 一个promise就是一个带有`then()`函数(方法)的对象.
>
>让我们使用一个例子来展示它的优势, 假设我们需要获取一个用户的当前配置:

```js
	var currentProfile = null;
	var username = 'something';

	fetchServerConfig(function(){
		fetchUserProfiles(serverConfig.USER_PROFILES, username, 
			function(profiles){
				currentProfile = profiles.currentProfile;	
		});	
	});
```
> 对于这种做法这里有一些问题:
>
> 1. 对于最后产生的代码, 缩进是一个噩梦, 特别是如果你要链接多个调用时.
> 
> 2. 在回调和函数之间错误报告的功能有丢失的倾向, 除非你在每一步中处理它们.
>
> 3. 对于你想使用`currentProfile`做什么, 你必须在内层回调中封装其逻辑, 无论是直接的方式还是使用一个单独分离的函数.
>
> Promises解决了这些问题. 在我们进入它是如何解决这些问题之前, 先让我们来看看一个使用promise对同一问题的实现.

```js
	var currentProfile = fetchServerConfig().then(function(serverConfig){
		return fetchUserProfiles(serverConfig.USER_PROFILES, username);
	}).then(function{
		return profiles.currentProfile;
	}, function(error){
		// Handle errors in either fetchServerConfig or
		// fetchUserProfile here
	});
```
> 注意其优势:
>
> 1. 你可以链接函数调用, 因此你不会产生缩进带来的噩梦.
>
> 2. 你可以放心前一个函数调用会在下一个函数调用之前完成.
>
> 3. 每一个`then()`调用都要两个参数(这两个参数都是函数). 第一个参数是成功的操作的回调函数, 第二个参数是错误处理的函数.
> 4. 在链接中发生错误的情况下, 错误信息会通过错误处理器传播到应用程序的其他部分. 因此, 任何回调函数的错误都可以在尾部被处理.
>
> 你会问, 那什么是`resolve`和`reject`呢? 是的, `deferred`在AngularJS中是一种创建promises的方式. 调用`resolve`来满足promise(调用成功时的处理函数), 同时调用`reject`来处理promise在调用错误处理器时的事情.

当我们链接到路由时, 我们会再次回到这里.

###指令

我们现在可以转移到即将用在我们应用程序的指令上来. 在这个应用程序中将有两个指令:

`butterbar`

这个指令将在路由发生改变并且页面仍然还在加载信息时处理显示和隐藏任务. 它将连接路由变化机制, 基于页面的状态来自动控制显示或者隐藏是否使用哪个标签.

`focus`

这个`focus`指令用于确保指定的文本域(或者元素)拥有焦点.

让我们来看一下代码:
```js
	// This file is app/scripts/directives/directives.js

	var directives = angular.module('guthub.directives', []);

	directives.directive('butterbar', ['$rootScope', function($rootScope){
		return {
			link: function(scope, element attrs){
				element.addClass('hide');

				$rootScope.$on('$routeChangeStart', function(){
					element.removeClass('hide');
				});

				$rootScope.$on('$routeChangeSuccess', function(){
					element.addClass('hide');
				});
			}
		};
	}]);

	directives.dirctive('focus',function(){
		return {
			link: function(scope, element, attrs){
				element[0].focus();
			}
		};
	});
```
上面所述的指令返回一个对象带有一个单一的属性, link. 我们将在第六章深入讨论你可以如何创建你自己的指令, 但是现在, 你应该知道下面的所有事情:

1. 指令通过两个步骤处理. 在第一步中(编译阶段), 所有的指令都被附加到一个被查找到的DOM元素上, 然后处理它. 任何DOM操作否发生在编译阶段(步骤中). 在这个阶段结束时, 生成一个连接函数.

2. 在第二步中, 连接阶段(我们之前使用的阶段), 生成前面的DOM模板并连接到作用域. 同样的, 任何观察者或者监听器都要根据需要添加, 在作用域和元素之前返回一个活动(双向)绑定. 因此, 任何关联到作用域的事情都发生在连接阶段.

那么在我们指令中发生了什么呢? 让我们去看一看, 好吗?

`butterbar`指令可以像下面这样使用:

	<div butterbar>My loading text...</div>

它基于前面隐藏的元素, 然后添加两个监听器到根作用域中. 当每次一个路由开始改变时, 它就显示该元素(通过改变它的class[className]), 每次路由成功改变并完成时, 它再一次隐藏`butterbar`.

另一个有趣的事情是注意我们是如何注入`$rootScope`到指令中的. 所有的指令都直接挂接到AngularJS的依赖注入系统, 因此你可以注入你的服务和其他任何需要的东西到其中.

最后需要注意的是处理元素的API. 使用jQuery的人会很高兴, 因为他直到使用的是一个类似jQuery的语法(addClass, removeClass). AngularJS实现了一个调用jQuery的自己, 因此, 对于任何AngularJS项目来说, jQuery都是一个可选的依赖项. 如果你最终在你的项目中使用完整的jQuery库, 你应该直到它使用的是它自己内置的jQlite实现.

第二个指令(focus)简单得多. 它只是在当前元素上调用`focus()`方法. 你可以用过在任何input元素上添加`focus`属性来调用它, 就像这样:

	<input type="text" focus></input>

当页面加载时, 元素将立即获得焦点.

###控制器

随着指令和服务的覆盖, 我们终于可以进入控制器部分了, 我们有五个控制器. 所有的这些控制器都在一个单独的文件中(`app/scripts/controllers/controllers.js`), 但是我们会一个个来了解它们. 让我们来看第一个控制器, 这是一个列表控制器, 负责显示系统中所有的食谱列表.
```js
	app.controller('ListCtrl', ['scope', 'recipes', function($scope, recipes){
		$scope.recipes = recipes;
	}]);
```
注意列表控制器中最重要的一个事情: 在这个控制器中, 它并没有连接到服务器和获取是食谱. 相反, 它只是使用已经取得的食谱列表. 你可能不知道它是如何工作的. 你可能会使用路由一节来回答, 因为它有一个我们之前看到`MultiRecipeLoader`. 你只需要在脑海里记住它.

在我们提到的列表控制器下, 其他的控制器都与之非常相似, 但我们仍然会逐步指出它们有趣的地方:
```js
	app.controller('ViewCtrl', ['$scope', '$location', 'recipe', function($scope, $location, recipe){
			$scope.recipe = recipe;

			$scope.edit = function(){
				$location.path('/edit/' + recipe.id);
			};
	}]);
```
这个视图控制器中有趣的部分是其编辑函数公开在作用域中. 而不是显示和隐藏字段或者其他类似的东西, 这个控制器依赖于AngularJS来处理繁重的任务(你应该这么做)! 这个编辑函数简单的改变URL并跳转到编辑食谱的部分, 你可以看见, AngularJS并没有处理剩下的工作. AngularJS识别已经改变的URL并加载响应的视图(这是与我们编辑模式中相同的食谱部分). 来看一看!

接下来, 让我们来看看编辑控制器:
```js
	app.controller('EditCtrl', ['$scope', '$location', 'recipe', function($scope, $location, recipe){
		$scope.recipe = recipe;

		$scope.save = function(){
			$scope.recipe.$save(function(recipe){
				$location.path('/view/' + recipe.id);
			});
		};

		$scope.remove = function(){
			delete $scope.recipe;
			$location.path('/');
		};
	}]);
```
那么在这个暴露在作用域中的编辑控制器中新的`save`和`remove`方法有什么.

那么你希望作用域内的`save`函数做什么. 它保存当前食谱, 并且一旦保存好, 它就在屏幕中将用户重定向到相同的食谱. 回调函数是非常有用的, 一旦你完成任务的情况下执行或者处理一些行为.

有两种方式可以在这里保存食谱. 一种是如代码所示, 通过执行$scope.recipe.$save()方法. 这只是可能, 因为`recipe`是一个通过开头部分的RecipeLoader返回的资源对象.

另外, 你可以像这样来保存食谱:
```js
	Recipe.save(recipe);
```
`remove`函数也是很简单的, 在这里它会从作用域中移除食谱, 同时将用户重定向到主菜单页. 请注意, 它并没有真正的从我们的服务器上删除它, 尽管它很再做出额外的调用.

接下来, 我们来看看New控制器:
```js
	app.controller('NewCtrl', ['$scope', '$location', 'Recipe', function($scope, $location, Recipe){
		$scope.recipe = new Recipe({
			ingredents: [{}]
		});

		$scope.save = function(){
			$scope.recipe.$save(function(recipe){
				$location.path('/view/' + recipe.id);
			});
		};
	}]);
```
New控制器几乎与Edit控制器完全一样. 实际上, 你可以结合两个控制器作为一个单一的控制器来做一个练习. 唯一的主要不同是New控制器会在第一步创建一个新的食谱(这也是一个资源, 因此它也有一个`save`函数).其他的一切都保持不变.

最后, 我们还有一个Ingredients控制器. 这是一个特殊的控制器, 在我们深入了解它为什么或者如何特殊之前, 先来看一看它:
```js
	app.controller('Ingredients', ['$scope', function($scope){
		$scope.addIngredients = function(){
			var ingredients = $scope.recipe.ingredients;
			ingredients[ingredients.length] = {};
		};

		$scope.removeIngredient = function(index) {
			$scope.recipe.ingredients.splice(index, 1);
		};
	}]);
```
到目前为止, 我们看到的所有其他控制器斗鱼UI视图上的相关部分联系着. 但是这个Ingredients控制器是特殊的. 它是一个子控制器, 用于在编辑页面封装特定的恭喜而不需要在外层(父级)来处理. 有趣的是要注意, 由于它是一个字控制器, 继承自作用域中的父控制器(在这里就是Edit/New控制器). 因此, 它可以访问来自父控制器的`$scope.recipe`.

这个控制器本身并没有什么有趣或者独特的地方. 它只是添加一个新的成份到现有的食谱成份数组中, 或者从食谱的成份列表中删除一个特定的成份.

那么现在, 我们就来完成最后的控制器. 唯一的JavaScript代码块展示了如何设置路由:
```js
	// This file is app/scripts/controllers/controllers.js

	var app = angular.module('guthub', ['guthub.directives', 'guthub.services']);

	app.config(['$routeProvider', function($routeProvider){
		$routeProvider.
			when('/', {
				controller: 'ListCtrl',
				resolve: {
					recipes: function(MultiRecipeLoader) {
						return MultiRecipeLoader();
					}
				},
				templateUrl: '/views/list.html'
			}).when('/edit/:recipeId', {
				controller: 'EditCtrl',
				resolve: {
					recipe: function(RecipeLoader){
						return RecipeLoader();
					}
				},
				templateUrl: '/views/recipeForm.html'
			}).when('/view/:recipeId', {
				controller: 'ViewCtrl',
				resolve: {
					recipe: function(RecipeLoader){
						return RecipeLoader();
					}
				},
				templateUrl: '/views/viewRecipe.html'
			}).when('/new', {
					controller: 'NewCtrl',
					templateUrl: '/views/recipeForm.html'
			}).otherwise({redirectTo: '/'});
	}]);
```
正如我们所承诺的, 我们终于到了解析函数使用的地方. 前面的代码设置Guthub AngularJS模块, 路由以及参与应用程序的模板.

它挂接到我们已经创建的指令和服务上, 然后指定不同的路由指向应用程序的不同地方.

对于每个路由, 我们指定了URL, 它备份控制器, 加载模板, 以及最后(可选的)提供了一个`resolve`对象.

这个`resolve`对象会告诉AngularJS, 每个resolve键需要在确定路由正确时才能显示给用户. 对我们来说, 我们想要加载所有的食谱或者个别的配方, 并确保在显示页面之前服务器要响应我们. 因此, 我们要告诉路由提供者我们的食谱, 然后再告诉他如何来取得它.

这个环节中我们在第一节中定义了两个服务, 分别时`MultiRecipeLoader`和`RecipeLoader`. 如果`resolve`函数返回一个AngularJS promise, 然后AngularJS会智能在获得它之前等待promise解决问题. 这意味着它将会等待到服务器响应.

然后将返回结果传递到构造函数中作为参数(与来自对象字段的参数的名称一起作为参数).

最后, `otherwise`函数表示当没有路由匹配时重定向到默认URL.

> 你可能会注意到Edit和New控制器两个路由通向同一个模板URL, `views/recipeForm.html`. 这里发生了什么呢? 我们复用了编辑模板. 依赖于相关的控制器, 将不同的元素显示在编辑食谱模板中.

完成这些工作之后, 现在我们可以聚焦到模板部分, 来看看控制器如何挂接到它们之上, 以及如何管理现实给最终用户的内容.

##模板

让我们首先来看看最外层的主模板, 这里就是`index.html`. 这是我们单页应用程序的基础, 同时所有其他的视图也会装在到这个模板的上下文中:
```html
	<!DOCTYPE html>
	<html lang="en" ng-app="guthub">
	<head>
		<title>Guthub - Create and Share</title>
		<script src="scripts/vendor/angular.min.js"></script>
		<script src="scripts/vendor/angular-resource.min.js"></script>
		<script src="scripts/directives/directives.js"></script>
		<script src="scripts/services/services.js"></script>
		<script src="scripts/controlers/controllers.js"></script>
		<link rel="stylesheet" href="styles/bootstrap.css">
		<link rel="stylesheet" href="styles/guthub.css">
	</head>
	<body>
		<header>
			<h1>Guthub</h1>
		</header>
		<div butterbar>Loading...</div>

		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span2">
					<!-- Sidebar -->
					<div class="focus"><a href="/#/new">New Recipe</a></div>
					<div><a href="/#/">Recipe List</a></div>
				</div>
				<div class="span10">
					<div ng-view></div>
				</div>
			</div>
		</div>
	</body>
	</html>
```
注意前面的模板中有5个有趣的元素, 其中大部分你在第2章中都已经见过了. 让我们逐个来看看它们:

`ng-app`

我们设置了`ng-app`模块为Guthub. 这与我们在`angular.module`函数中提供的模块名称相同. 这便是AngularJS如何知道两个挂接在一起的原因.

`script`标签

这表示应用程序在哪里加载AngularJS. 这必须在所有使用AngularJS的JS文件被加载之前完成. 理想的情况下, 它应该在body的底部完成(\</body\>之前).

`Butterbar`

我们第一次使用自定义指令. 在我们定义我们的`butterbar`指令之前, 我们希望将它用于一个元素, 以便在路由改变时显示它, 在成功的时候隐藏它(loading...处理). 需要突出显示这个元素的文本(在这里我们使用了一个非常烦人的"Loading...").

链接的`href`值

`href`用于链接到我们单页应用程序的各个页面. 追它们如何使用#字符来确保页面不会重载的, 并且相对于当前页面. AngularJS会监控URL(只要页面没有重载), 然后在需要的时候起到神奇的作用(或者通常, 将这个非常烦人的路由管理定义为我们路由的一部分).

`ng-view`

这是最后一个神奇的杰作. 在我们的控制器一节, 我们定义了路由. 作为定义的一部分, 每个路由表示一个URL, 控制器关联路由和一个模板. 当AngularJS发现一个路由改变时, 它就会加载关联的模板, 并将控制器添加给它, 同时替换`ng-view`为该模板的内容.

有一件引人注目的事情是这里缺少`ng-controller`标签. 大部分应用程序某种程度上都需要一个与外部模板关联的MainController. 其最常见的位置是在body标签上. 在这种情况下, 我们并没有使用它, 因为完整的外部模板没有AngularJS内容需要引用到一个作用域.

现在我们来看看与每个控制器关联的单独的模板, 就从"食谱列表"模板开始:
```html
	<!-- File is chapter4/guthub/app/view/list.html -->
	<h3>Recipe List</h3>
	<ul class="recipes">
		<li ng-repeat="recipe in recipes">
			<div><a ng-href="/#/view/{{recipe.id}}">{{recipe.title}}</a></div>
		</li>
	</ul>
```
是的, 它是一个非常无聊(普通)的模板. 这里只有两个有趣的点. 第一个是非常标准的`ng-repeat`标签用法. 他会获得作用域内的所有食谱并重复检出它们.

第二个是`ng-href`标签的用法而不是`href`属性. 这是一个在AngularJS加载期间纯粹无效的空白链接. `ng-href`会确保任何时候都不会给用户呈现一个畸形的链接. 总是会使用它在任何时候使你的URLs都是动态的而不是静态的.

当然, 你可能感到奇怪: 控制器在哪里? 这里没有`ng-controller`定义, 也确实没有Main Controller定义. 这是路由映射发挥的作用. 如果你还记得(或者往前翻几页), `/`路由会重定向到列表模板并且带有与之关联的ListController. 因此, 当引用任何变量或者类似的东西时, 它都在List Controller作用域内部.

现在我们来看一些有更多实质内容的东西: 视图形式.
```html
	<!-- File is chapter4/guthub/app/views/viewRecipe.html -->
	<h2>{{recipe.title}}</h2>

	<div>{{recipe.decription}}</div>

	<h3>Ingredients</h3>
	<span ng-show="recipe.ingredients.length == 0">No Ingredients</span>
	<ul class="unstyled" ng-hide="recipe.ingredients.length == 0">
		<li ng-repeat="ingredient in recipe.ingredients">
			<span>{{ingredient.amount}}</span>
			<span>{{ingredient.amountUnits</span>
			<span>{{ingredient.ingredientName}}</span>
		</li>
	</ul>

	<h3>Instructions</h3>
	<div>{{recipe.instructions}}</div>

	<form ng-submit="edit()" class="form-horizontal">
		<div class="form-actions">
			<button class="btn btn-primary">Edit</button>
		</div>
	</form>
```
这是另一个不错的, 很小的包含模板. 我们将提醒你注意三件事, 虽然不会按照它们所出现的顺序.

第一个就是非常标准的`ng-repeat`. 食谱(recipes)再次出现在View Controller作用域中, 这是用过在页面现实给用户之前通过`resolve`函数加载的. 这确保用户查看它时也面不是一个破碎的, 未加载的状态.

接下来一个有趣的用法是使用`ng-show`和`ng-class`(这里应该是`ng-hide`)来设置模板的样式. `ng-show`标签被添加到\<i\>标签上, 这是用来显示一个星号标记的icon. 现在, 这个星号标记只在食谱是一个特色食谱的时候才显示(例如通过`recipe.featured`布尔值来标记). 理想的情况下, 为了确保适当的间距, 你需要使用一个空白的空格图标, 并给这个空格图标绑定`ng-hide`指令, 然后同归同样的AngularJS表达式`ng-show`来显示. 这是一个常见的用法, 显示一个东西并在给定的条件下来隐藏.

`ng-class`用于添加一个类(CSS类)给\<h2\>标签(在这种情况下就是"特色")当食谱是一个特色食谱时. 它添加了一些特殊的高亮来使标题更加引人注目.

最后一个需要注意的时表单上的`ng-submit`指令. 这个指令规定在表单被提交的情况下调用`scope`中的`edit()`函数. 当任何没有关联明确函数的按钮被点击时机会提交表单(这种情况下便是Edit按钮). 同样, AngularJS足够智能的在作用域中(从模块,路由,控制器中)在正确的时间里引用和调用正确的方法.

> **上面这段解释与原书代码有一些差别, 读者自行理解. 原书作者暂未给出解答.**

现在我们可以来看看我们最后的模板(可能目前为止最复杂的一个), 食谱表单模板:
```html
	<!-- file is chapter4/guthub/app/views/recipeForm.html -->
	<h2>Edit Recipe</h2>
	<form name="recipeForm" ng-submit="save()" class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="title">Title:</label>
			<div class="controls">
				<input ng-model="recipe.title" class="input-xlarge" id="title" focus required>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="description">Description:</label>
			<div class="controls">
				<textarea ng-model="recipe.description" class="input-xlarge" id="description"></textarea>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="ingredients">Ingredients:</label>
			<div class="controls">
				<ul id="ingredients" class="unstyled" ng-controller="IngredientsCtrl">
				<li ng-repeat="ingredient in recipe.ingredients">
					<input ng-model="ingredient.amount" class="input-mini">
					<input ng-model="ingredient.amountUnits" class="input-small">
					<input ng-model="ingredient.ingredientName">
					<button type="button" class="btn btn-mini" ng-click="removeIngredient($index)"><i class="icon-minus-sign"></i> Delete </button>
				</li>
				<button type="button" class="btn btn-mini" ng-click="addIngredient()"><i class="icon-plus-sign"></i> Add </button>
			</ul>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="instructions">Instructions:</label>
			<div class="controls">
				<textarea ng-model="recipe.instructions" class="input-xxlarge" id="instructions"></textarea>
			</div>
		</div>

		<div class="form-actions">
			<button class="btn btn-primary" ng-disabled="recipeForm.$invalid">Save</button>
			<button type="button" ng-click="remove()" ng-show="!recipe.id" class="btn">Delete</button>
		</div>
	</form>
```
不要惊慌. 它看起来像很多代码, 并且它时一个很长的代码, 但是如果你认真研究以下它, 你会发现它并不是非常复杂. 事实上, 其中很多都是很简单的, 比如重复的显示可编辑输入字段用于编辑食谱的模板:

+ `focus`指令被添加到第一个输入字段上(`title`输入字段). 这确保当用户导航到这个页面时, 标题字段会自动聚焦, 并且用户可以立即开始输入标题信息.

+ `ng-submit`指令与前面的例子非常相似, 因此我们不会深入讨论它, 它只是保存是食谱的状态和编辑过程的结束信号. 它会挂接到Edit Controller中的`save()`函数.

+ `ng-model`指令用于将不同的文本输入框和文本域绑定到模型中.

在这个页面更有趣的一方面, 并且我们建议你花一点之间来了解它的便是配料列表部分的`ng-controller`标签. 让我们花一分钟的事件来了解以下这里发生了什么.

我们看到了一个显示配料成份的列表, 并且容器标签关联了一个`ng-controller`. 这意味着这个`\<ul\>`标签是Ingredients Controller的作用域. 但是这个模板实际的控制器是什么呢, 是Edit Controller? 事实证明, Ingredients Controller是作为Edit Controller的子控制器创建的, 从而继承了Edit Controller的作用域. 这就是为什么它可以从Edit Controller访问食谱对象(recipe object)的原因.

此外, 它还增加了一个`addIngredient()`方法, 这是通过处理高亮的`ng-click`使用的, 那么就只能在`\<ul\>`标签作用域内访问. 那么为什么你需要这么做呢? 因为这是分离你担忧的最好的方式. 为什么Edit Controller需要一个`addIngredients()`方法, 问99%的模板都不会关心它. 因为如此精确你的子控制器和嵌套控制器是很不错的, 它可以包含任务并循序你分离你的业务逻辑到更多的可维护模块中.

+ 另外一个控制器便是我们在这里想要深入讨论的表单验证控制. 它很容易在AngularJS中设置一个特定的表单字段为"必填项". 只需要简单的添加required标签到输入框上(与前面的代码中的情况一样). 但是现在你要怎么对它.

为此, 我们先跳到保存按钮部分. 注意它上面有一个`ng-disabled`指令, 这换言之就是`recipeForm.$invalid`. 这个`recipeForm`是我们已经声明的表单名称. AngularJS增加了一些特殊的变量(`$valid`和`$invalid`只是其中两个)允许你控制表单的元素. AngularJS会查找到所有的必填元素并更新所对应的特殊变量. 因此如果我们的Recipe Title为空, `recipeForm.$invalid`就会被这只为true(`$valid`就为false), 同时我们的保存(Save)按钮就会立刻被禁用.

我们还可以给一个文本输入框设置最大和最小长度(输入长度), 以及一个用于验证一个输入字段的正则表达式模式. 另外, 这里还有只在满足特定条件时用于显示特定错误消息的高级用法. 让我们使用一个很小的分支例子来看看:
```html
	<form name="myForm">
		User name: <input type="text" name="userName" ng-model="user.name" ng-minlength="3">
		<span class="error" ng-show="myForm.userName.$error.minlength">Too Short!</span>
	</form>
```
在前面的这个例子中, 我们添加了一要求: 用户名至少是三个字符(通过使用`ng-minlength`指令). 现在, 表单范围内会关心每个命名输入框的填充形式--在这个例子中我们只有一个`userName`--其中每个输入框都会有一个`$error`对象(这里具体的还包括什么样的错误或者没有错误: `required`, `minlength`, `maclength`或者模式)和一个`$valid`标签来表示输入框本身是否有效.

我们可以利用这个来选择性的将错误信息显示给用户, 这根据不用的输入错误类型来显示, 正如我们上面的实例所示.

跳回到我们原来的模板中--Recipe表单模板--在这里的ingredients repeater里面还有另外一个很好的`ng-show`高亮的用法. 这个Add Ingredient按钮只会在最后的一个配料的旁边显示. 着可以通过在一个repeater元素范围内调用一个`ng-show`并使用特殊的`$last`变量来完成.

最后我们还有最后的一个`ng-click`, 这是附加的第二个按钮, 用于删除该食谱. 注意这个按钮只会在食谱尚未保存的时候显示. 虽然通常它会编写一个更有意义的`ng-hide="recipe.id", 有时候它会使用更有语义的`ng-show="!recipe.id". 也就是说, 如果食谱没有一个id的时候显示, 而不是在食谱有一个id的时候隐藏.

##测试

随着控制器部分, 我们已经推迟向你显示测试部分了, 但你知道它会即将到来, 不是吗? 在这一节, 我们将会涵盖你已经编写部分的代码测试, 以及涉及你要如何编写它们.

###单元测试

第一个, 也是非常重要的一种测试是单元测试. 对于控制器(指令和服务)的测试你已经开发和编写的正确的结构, 并且你可能会想到它们会做什么.

在我们深入到各个单元测试之前, 让我们围绕所有我们的控制器单元测试来看看测试装置:
```js
	describle('Controllers', function() {
		var $scope, ctrl;
		//you need to include your module in a test
		beforeEach(module('guthub'));
		beforeEach(function() {
			this.addMatchers({
				toEqualData: function(expected) {
					return angular.equals(this.actual, expected);
				}
			});
		});

		describle('ListCtrl', function() {....});
		// Other controller describles here as well
	});
```
这个测试装置(我们仍然使用Jasmine的行为方式来编写这些测试)做了几件事情:

1. 创建一个全局(至少对于这个测试规范是这个目的)可访问的作用域和控制器, 所以我们不用担心每个控制器会创建一个新的变量.

2. 初始化我们应用程序所用的模块(在这里是Guthub).

3. 添加一个我们称之为`equalData`的特殊的匹配器. 这基本上允许我们在资源对象(就像食谱)通过`$resource`服务和调用RESTful来执行断言(测试判断).

> 记得在任何我们处理在`ngRsource`上返回对象的断言时添加一个称为`equalData`特殊匹配器. 这是因为`ngRsource`返回对象还有额外的方法在它们失败时默认希望调用equal方法.

这个装置到此为止, 让我们来看看List Controller的单元测试:
```js
	describle('ListCtrl', function(){
		var mockBackend, recipe;
		// _$httpBackend_ is the same as $httpBackend. Only written this way to diiferentiate between injected variables and local variables
		breforeEach(inject(function($rootScope, $controller, _$httpBackend_, Recipe) {
			recipe = Recipe;
			mockBackend = _$httpBackend_;
			$scope = $rootScope.$new();
			ctrl = $controller('ListCtrl', {
				$scope: $scope,
				recipes: [1, 2, 3]
			});
		}));

		it('should have list of recipes', function() {
			expect($scope.recipes).toEqual([1, 2, 3]);
		});
	});
```
记住这个List Controller只是我们最简单的控制器之一. 这个控制器的构造器只是接受一个食谱列表并将它保存到作用域中. 你可以编写一个测试给它, 但它似乎有一点不合理(我们还是这么做了, 因为这个测试很不错).

相反, 更有趣的是MulyiRecipeLoader服务方面. 它负责从服务器上获取食谱列表并将它作为一个参数传递(当通过`$route`服务正确的连接时).
```js
	describe('MultiRecipeLoader', function() {
		var mockBackend, recipe, loader;
		// _$httpBackend_ is the same as $httpBackend. Only written this way to differentiate between injected variables and local variables. 

		beforeEach(inject(function(_$httpBackend_, Recipe, MultiRecipeLoader) {
			recipe = Recipe;
			mockBackend = _$httpBackend_;
			loader = MultiRecipeLoader;
		}));

		it('should load list of recipes', function() { 
			mockBackend.expectGET('/recipes').respond([{id: 1}, {id: 2}]);

			var recipes;

			var promise = loader(); promise.then(function(rec) {
				recipes = rec;
			});

			expect(recipes).toBeUndefined( ) ;

			mockBackend. f lush() ;

			expect(recipes).toEqualData([{id: 1}, {id: 2}]); });
	});
	// Other controller describes here as well
```
在我们的测试中, 我们通过挂接到一个模拟的`HttpBackend`来测试MultiRecipeLoader. 这来自于测试运行时所包含的`angular-mocks.js`文件. 只需将它注入到你的`beforeEach`方法中就足以让你设置预期目的. 接下来, 我们进行了一个更有意义的测试, 我们期望设置一个服务器的GET请求来获取recipes, 浙江返回一个简单的数组对象. 然后使用我们新的自定义的匹配器来确保正确的返回数据. 注意在模拟backend中的`flush()`调用, 这将告诉模拟Backend从服务器返回响应. 你可以使用这个机制来测试控制流程和查看你的应用程序在服务器返回一个响应之前和之后是如何处理的.

我们将跳过View Controller, 因为它除了在作用域中添加一个`edit()`方法之外基于与List Controller一模一样. 这是非常简单的测试, 你可以在你的测试中注入`$location`并检查它的值.

现在让我们跳到Edit Controller, 其中有两个有趣的点我们进行单元测试. 一个是类似我们之前看到过的`resolve`函数, 并且可以以同样的方式测试. 相反, 我们现在想看看我们可以如和测试`save()`和`remove()`方法. 让我们来看看对于它们的测试(假设我们的测试工具来自于前面的例子):
```js
	describle('EditController', function() {
		var mockBackend, location;
		beforeEach(inject($rootScope, $controller, _$httpBackend_, $location, Recipe){
			mockBackend = _$httpBackend_;
			location = $location;
			$scope = $rootScope.$new();

			ctrl = $controller('EditCtrl', {
				$scope: $scope,
				$location: $location,
				recipe: new Recipe({id: 1, title: 'Recipe'});
			});
		}));

		it('should save the recipe', function(){
			mockBackend.expectPOST('/recipes/1', {id: 1, title: 'Recipe'}).respond({id: 2});

			// Set it to something else to ensure it is changed during the test
			location.path('test');

			$scope.save();
			expect(location.path()).toEqual('/test');

			mockBackend.flush();

			expect(location.path()).toEqual('/view/2');
		});

		it('should remove the recipe', function(){
			expect($scope.recipe).toBeTruthy();
			location.path('test');

			$scope.remove();

			expect($scope.recipe).toBeUndefined();
			expect(location.path()).toEqual('/');
		});
	});
```
在第一个测试用, 我们测试了`save()`函数. 特别是, 我们确保在我们的对象保存时首先创建一个到服务器的POST请求, 然后, 一旦服务器响应, 地址就改变到新的持久对象的视图食谱页面.

第二个测试更简单. 我们进行了简单的检测以确保在作用域中调用`remove()`方法的时候移除当前食谱, 然后重定向到用户主页. 这可以很容易通过注入`$location`服务到我们的测试中并使用它.

其余的针对控制器的单元测试遵循非常相似的模式, 因此在这里我们跳过它们. 在他们的底层中, 这些单元测试依赖于一些事情:

+ 确保控制器(或者更可能是作用域)在结束初始化时达到正确的状态

+ 确认经行正确的服务器调用, 以及通过作用域在服务器调用期间和完成后去的正确的状态(通过在单元测试中使用我们的模拟后端服务)

+ 利用AngularJS的依赖注入框架着手处理元素以及控制器对象用于确保控制器会设置正确的状态.

###脚本测试

一旦我们对单元测试很满意, 我们可能禁不住的往后靠一下, 抽根雪茄, 收工. 但是AngularJS开发者不会这么做, 直到他们完成了他们的脚本测试(场景测试). 虽然单元测试确保我们的每一块JS代码都按照预期工作, 我们也要确保模板加载, 并正确的挂接到控制器上, 以及在模板重点击做正确的事情.

这正是AngularJS带给你的脚本测试(场景测试), 它允许你做以下事情:

+ 加载你的应用程序
+ 浏览一个特定的页面
+ 随意的点击周围和输入文本
+ 确保发生正确的事情

所以, 脚本测试如何在我们的"食谱列表"页面工作? 首先, 在我们开始实际的测试之前, 我们需要做一些基础工作.

对于该脚本测试工作, 我们需要一个工作的Web服务器以准备从Guthub应用上接受请求, 同时将允许我们从它上面存储和获取一个食谱列表. 随意的更改代码以使用内存中的食谱列表(移除`$resource`食谱并只是将它转换为一个JSON对象), 或者复用和修改我们前面章节向你展示的Web服务器, 或者使用Yeoman!

一旦我们有了一个服务器并运行起来, 同时服务于我们的应用程序, 然后我们就可以编写和运行下面的测试:
```js
	describle('Guthub App', function(){
		it('should show a list of recipes', function(){
			browser().navigateTo('/index.html');
			//Our Default Guthub recipes list has two recipes
			expect(repeater('.recipes li').count()).toEqual(2);
		});
	});
```
