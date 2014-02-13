# 第二章 Angular应用程序剖析

不像典型的库, 你需要挑选你喜欢的功能, 在Angular中所有的东西都被设计成一个用于协作的套件. 在本章中我们将涵盖Angular中所有的基本构建块, 这样你就可以理解如何将它们组合在一起. 这些块都将在后面的章节中有更详细的讨论.

## 目录

- [启用Angular](#启用Angular)
	- [加载脚本](#加载脚本)
	- [使用ng-app声明Angular的界限](#使用ng-app声明angular的界限)
- [模型/视图/控制器](#模型视图控制器)
- [模板和数据绑定](#模板和数据绑定)
	- [显示文本](#显示文本)
	- [表单输入](#表单输入)
	- [无侵入JavaScript的一些话](#无侵入javascript的一些话)
	- [列表, 表格和其他重复的元素](#列表-表格和其他重复的元素)
	- [隐藏与显示](#隐藏与显示)
	- [CSS类和样式](#css类和样式)
	- [src和href属性注意事项](#src和href属性注意事项)
	- [表达式](#表达式)
	- [分离用户界面(UI)和控制器职责](#分离用户界面ui和控制器职责)
	- [使用作用域发布模型数据](#使用作用域发布模型数据)
	- [使用$watch监控模型变化](#使用watch监控模型变化)
	- [watch()中的性能注意事项](#watch中的性能注意事项)
- [使用模块组织依赖](#使用模块组织依赖)
- [使用过滤器格式化数据](#使用过滤器格式化数据)
- [使用路由和$location更新视图](#使用路由和location更新视图)
	- [index.html](#indexhtml)
	- [list.html](#listhtml)
	- [detail.html](#detailhtml)
	- [controllers.js](#controllersjs)
- [对话服务器](#对话服务器)
- [使用指令更新DOM](#使用指令更新dom)
	- [index.html](#indexhtml-1)
	- [controller.js](#controllerjs)
- [验证用户输入](#验证用户输入)
- [小结](#小结)

##启用Angular

任何应用程序都必须做两件事来启用Angular:

1. 加载`angular.js`库
2. 使用`ng-app`指令来告诉Angular它应该管理哪部分DOM

###加载脚本

加载库很简单, 与加载其他任何JavaScript库遵循同样的规则. 你可以从Google的内容分发网络(CDN)中载入脚本, 就像这样:
```html
    <script src="http://ajax.google.com/ajax/libs/angularjs/1.0.4/angular.min.js"></script>
```
推荐使用Google的CDN. Google的服务器很快, 并且这个脚本是跨应用程序缓存的. 这意味着, 如果你的用户有多个应用程序使用Angular, 那么他将只需要下载脚本一次. 此外, 如果用户访问过其他使用Google CDN连接Angular的站点, 那么他在访问你的站点时就不需要再次下载该脚本.

如果你更喜欢本地主机(或者其他的方式), 你也可以这样做. 只需要在`src`中指定正确的地址.

###使用ng-app声明Angular的界限

> 原文是Boundaries, 意思是声明应用程序的作用域, 即Angular应用程序的作用范围.

`ng-app`指令用于让你告诉Angular你期望它管理页面的哪部分. 如果你在创建一个完全的Angular应用程序, 那么你应该在`<html>`标签中包含`ng-app`部分, 就像这样:
```html
    <html ng-app>
    …
    </html>
```
这会告知Angular要管理页面中的所有DOM元素. 

如果你有一个现有的应用程序, 要求使用其他的技术来管理DOM, 例如Java或者Rails, 你可以通过将它放置在页面的一些元素例如`<div>`中来告诉Angular只需要管理页面的一部分即可.
```html
    <html>
    …
        <div ng-app>
        …
        </div>
    …
    </html>
```
###模型/视图/控制器

在第一章中, 我们提到Angular支持模型/视图/控制器的应用程序设计风格. 虽然在设计你的Angular应用程序时有很大的灵活性, 但是总是别有一番风味的:

+ 模型包含代表你的应用程序当前状态的数据
+ 视图显示数据
+ 控制器管理你的模型和视图之间的关系

你需要使用对象属性的方式创建模型, 或者只包含原始类型的数据. 这里并没有特定的模型变量. 如果你希望给用户显示一些文本, 你可以使用一个字符串, 就像这样:
```js
    var someText = 'You have started your journey';
```
你可以通过编写一个模板作为HTML页面, 并从模型中合并数据的方式来创建视图. 正如我们已经看过的, 你可以在DOM中插入一个占位符, 然后再像这样设置它的文本:
```html
    <p>{{someText}}</p>
```
我们调用这个双大括号语法来插入值, 它将插入新的内容到一个现有的模版中.

控制器就是你编写用于告诉Angular哪些对象和原始值构成你的模型的类, 通过将这些对象或者原始值分配给`$scope`对象传递到控制器中.
```js
    function TextController($scope){
        $scope.someText = someText;
    }
```
把他们放在一起, 我们得到如下代码:
```html
    <html ng-app>
    <body ng-controller="TextController">
        <p>{{someText}}</p>
        
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.1/angular.min.js"></script>
        
        <script>
            function TextController($scope){
                $scope.someText = 'You have started your journey';
            }
        </script>
    </body>
    </html>
```
将它载入到浏览器中, 你就会看到

> 'You have started you journey'

虽然这个原始风格的模型工作在简单的情况下, 然而大多数的应用程序你都希望创建一个模型对象来包裹你的数据. 我们将创建一个信息模型对象, 并用它来存储我们的`someText`. 因此不是这样的:
```js
    var someText = 'You have started your journey';
```
你应该这样编写:
```js
    var messages = {};
    messages.someText = 'You have started your journey';
    function TextController($scope){
        $scope.messages = messages;
    }
```
然后在你的模板中这样使用:
```html
    <p>{{messages.someText}}</p>
```
正如我们后面会看到, 当我们讨论`$scope`对象时, 像这样创建一个模型对象将有利于防止从`$scope`对象的原型中继承的意外行为.

我们正在讨论的这些方法从长远看来能够帮助你, 在上面的例子中, 我们在全局作用域中创建了`TextController`. 虽然这是一个很好的例子, 但是正确定义一个控制器的做法应该是将它作为模块的一部分, 它给你的应用程序部分提供了一个命名空间. 更新之后的代码看起来应该是下面这样.
```html
    <html  ng-app="myApp">
    <body ng-controller="TextController">
        <p>{{someText.message}}</p>
        
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.1/angular.min.js"></script>
        
        <script>
            var myAppModule = angular.module('myApp',[]);
            
            myAppModule.controller('TextController', function($scope){
                var someText = {};
                someText.message = 'You have started your journey';
                $scope.someText = someText;
            });
        </script>
    </body>
    </html>
```
在这个版本中, 我们声明模块中`ng-app`元素的名称为`myApp`. 然后我们调用Angular对象创建了一个名为myApp的模块, 然后调用模块的`controller`方法并将我们的控制器函数传递给它.

一会儿我们就会知道为什么, 以及如何获取所有的模块. 但是现在, 只需要记住将所有的信息都保存在全局的命名空间中是一件好事, 并且这也是我们使用模块的机制.

##模板和数据绑定

在Angular应用程序中模板只是HTML文档, 就像我们从从服务端载入或者定义在`<script>`标签中的任何其他静态资源一样. 在你的模板中定义用户界面, 可以使用标准的HTML加Angular指令来定义你所需要的UI组件.

一旦进入浏览器中, Angular就会进入到你的整个应用程序中通过合并模板和数据的方式来解析这些模板. 在第一章中我们已经在购物车应用中看过了显示一个项目列表的例子.
```html
    <div ng-repeat="item in items">
        <span>{{item.title}}</span>
        ...
    </div>
```
这里, 它只是外层`<div>`的一个副本, 里面所有的一切, 都一一对应`items`数组中的每个元素.

那么这些数据从哪里来? 在我们的购物车例子中, 在我们的代码中我们只将它定义为一个数组. 对于你开始创建一个UI并希望测试它是如何工作的, 这是非常合适的. 然而大多数的应用程序, 将使用一些服务器上的持久性数据. 在浏览器中你的应用程序连接你的服务器, 用户在页面上请求他们所需要的一切, 然后Angular将它[请求的数据]与你的模板合并.

基本的运作流程看起来像这样:

1. 用户请求你的应用程序的第一个页面
2. 用户浏览器发出一个HTTP请求连接到你的服务器, 然后加载包含模板的*index.html*页面
3. Angular载入到页面中, 等到页面完全加载, 然后查询定义在模板范围内的`ng-app`
4. Angular遍历模板并查询指令和绑定. 这将导致注册事件监听器和DOM操作, 以及从服务器上获取初始数据. 这项工作的最终结果是展示应用程序并将模板作为DOM转换为视图.
5. 连接到你的服务器加载你需要展示给用户所需的附加数据.

第1步至第3步是每个Angular应用程序的标准. 第4步和第5步对你来说是可选的. 这些步骤可以同步或者异步发生. 出于性能的考虑, 你应用程序所需的数据在第一个视图中[首屏]显示给用户, 可以减少并避免重复的请求HTML模板.

通过使用Angular组织你的应用程序, 你可以在你的应用程序中分离模板和数据. 这样做的结果是这些模板是可以缓存的. 在第一次载入之后, 实质上浏览器中就只需要请求新的数据了. 正如JavaScript, 图片, CSS以及其他资源, 缓存这些模板可以给你的应用程序提供更好的性能.

###显示文本

你可以使用`ng-bind`指令在你UI的任何地方显示和更新文本. 它有两种等价的形式. 一种是我们见过的双花括号形式:
```html
    <p>{{greeting}}</p>
```
然后就是一个被称为`ng-bind`的基于属性的指令:
```html
    <p ng-bind="greeting"><p>
```
这两者的输出是等价的. 如果模型中的变量`greeting`设置为"Hi, there", Angular将生成这样的HTML:
```html
    <p>Hi, there</p>
```
浏览器将显示"Hi, There".

那么为什么你会使用上面的另外一种形式? 我们创建的双括号插入值的语法读起来更加自然并且只需要更少的输入. 虽然两种形式产生相同的输出, 但使用双花括号语法, 加载你应用程序的第一个页面`index.html`时, 在Angular替换花括号中的数据之前, 用户可能会看到一个未渲染的模板. 随后的视图将不会经历这一点.

原因是浏览器加载HTML页面, 渲染它, 直到那时Angular才可能准备解析它们.

好消息是你仍然可以在大多数模板中使用`{{ }}`. 然而, 在你的`index.html`页面中绑定数据, 应该使用`ng-bind`. 这样, 直到数据加载完你的用户将什么也看不到.

###表单输入

在Angular中处理表单元素是很简单的. 正如我们见过的几个例子, 你可以使用`ng-model`属性绑定到你的模型属性元素上. 这适用于所有标准的表单元素, 例如文本输入框, 单选按你, 复选框等等.  我们可以像这样绑定一个复选框到一个属性:
```html
    <form controller="SomeController">
        <input type="checkbox" ng-model="youCheckedIt">
    </form>
```
这意味着:

1. 当用户选择复选框, `SomeController`的`$scope`中一个名为`youCheckedIt`的属性将变成true. 取消选择时使`youCheckedIt`变成false.
2. 如果你在`SomeController`中设置`$scope.youCheckedIt`为true, 这个复选框在UI中会被自动选择. 设置它为false则取消选择.

现在我想说的是我们真正想要的是, 当用户做了一些什么事情时作出响应. 对于文本输入框元素, 你使用`ng-change`属性来指定一个控制器方法, 那么无论什么时候用户改变输入框的值时, 这个控制器方法都应该被调用. 让我们做一个简单的计算器来帮助用户自己理解他们需要多少钱才能得到某些东西:
```html
    <form ng-controller="StartUpController">
        Starting: <input ng-change="computeNeeded()" ng-model="funding.startingEstimate">
        Recommendation: {{funding.needed}}
    </form>
```
对于我们这个简单的例子, 让我们只设置输出用户预算十倍的值. 我们还将设置一个默认为0的值来开始:
```js
    function StartUpController($scope){
    
        $scope.funding = { startingEstimate: 0 };
        
        $scope.computeNeeded = function(){
            $scope.funding.needed = $scope.funding.startingEstimate * 10;
        };
        
    }
```
然而, 前面的代码中有一个潜在的策略问题. 问题是当用于在文本输入框中输入时我们只是重新计算了所需的金额. 如果这个输入框只在用户在这个特定的输入框中输入时更新, 这工作得很好. 但是如果其他的输入框也在模型中绑定了这个属性会怎样呢? 如果它从服务器获取数据来更新又会怎样?

无论这个字段如何更新, 我们要使用一个名为`$watch()`的`$scope`函数[$scope对象的方法]. 我们将在本章的后面详细讨论`watch`方法. 基本的用法是, 可以调用`$watch()`并给他传递一个监控表达式和一个用于响应表达式变化的回调函数.

在这种情况下, 我们希望监控`funding.startEstimate`以及每当它改变时调用`computeNeeded()`. 然后我们使用这个方法重写了`StartUpController`.
```js
    function StartUpController($scope){
    
        $scope.funding = { startingEstimate: 0 };
        
        $scope.computeNeeded = function(){
            $scope.funding.needed = $scope.funding.startingEstimate * 10;
        };
        
        $scope.$watch('funding.startingEstimate',  $scope.computeNeeded);
        
    }
```
注意引号中的监控表达式. 是的, 它是一个字符串. 这个字符串是评估某些东西价格的Angular表达式. 表达式可以进行简单的运算和访问`$scope`对象的属性. 在本章的后面我们会涵盖更多关于表达式的信息.

你也可以监控一个函数返回值, 但是它并不会监控`funding.startingEstimate`, 因为它赋值为0, 并且0[初始值]不再会改变.

然后, 由于每当我们的`funding.statingEstimates`改变时`funding.needed`都会自动更新, 我们可以像这样编写一个更简单的模板.
```html
    <form ng-controller="StartUpController">
        Starting: <input ng-model="funding.startEstimate">
        Recommendation: {{funding.needed}}
    </form>
```
在某些情况下, 你并不希望每一个改变都发生响应, 相反, 你希望等到用户来告诉你它准备好了. 例如可能完成购买或者发送一个聊天记录.

如果你的表单中有一组输入框, 那么你可以在这个表单上使用`ng-submit`指令给它指定一个提交表单时的回调函数. 我们可以让用户通过点击一个按钮请求帮助他们启动应用的方式来扩展上面的例子:
```html
    <form ng-submit="requestFunding()" ng-controller="StartUpController">
        Starting: <input ng-change="computeNeeded()" ng-model="funding.startingEstimate">
        Recommendation: {{funding.needed}}
        <button>Fun my startup</button>
    </form>
```
```js
    function StartUpController($scope){
        $scope.computeNeeded = function(){
            $scope.funding.needed = $scope.funding.startingEstimate * 10;  
        };
        
        $scope.requestFunding = function(){
            window.alert("Sorry, please get more customers first.");
        };
    }
```
当尝试提交这个表单时, `ng-submit`指令也会自动阻止浏览器处理其默认的`POST`行为.

> 原文此处有错误, 表单提交的默认行为是`GET`.

在需要处理其他事件的情况下, 就像当你想要提供交互而不是提交表单一样, Angular提供了类似于浏览器原生事件属性的事件处理指令. 对于`onclick`, 你应该使用`ng-click`. 对于`ondblclick`你应该使用`ng-dblclick`等等.

我们可以尝试最后一次扩展我们的计算器启动应用, 使用一个重置按钮用于将输入框的值重置为0.
```html
    <form ng-submit="requestFunding()" ng-controller="StartUpController">
        Starting: <input ng-change="computeNeeded()" ng-model="funding.StartingEstimate">
        Recommendation: {{funding.needed}}
        <button>Fund my startup!</button>
        <button type="button" ng-click="reset()">Reset</button>
    </form>
    
    function StartUpController($scope){
    
        $scope.computeNeeded = function(){
            $scope.funding.needed = $scope.funding.startingEstimate * 10;
        };
        
        $scope.requestFunding = function(){
            window.alert("Sorry, please get more customers first");
        };
        
        $scope.reset = function(){
            $scope.funding.startingEstimate = 0;
        }
    
    }
```
###无侵入JavaScript的一些话

在你JavaScript开发生涯的某些时刻, 有人可能会告诉你, 你应该编写"无侵入的JavaScript", 在你的HTML中使用`click`, `mousedown`以及其他类似的内联事件处理程序是不好的. 那么他是正确.

无侵入的JavaScript思想已经有很多解释, 但是其编码风格的原理大致如下:

1. 不是每个人的浏览器都支持JavaScript. 让每个人都能够看到你所有的内容和使用你的应用程序, 而不需要在浏览器中执行代码.

2. 有些人使用的浏览器工作方式不同. 视障人员使用的屏幕阅读器和一些手机用户并不能使用网站的JavaScript.

3. JavaScript在不同的平台工作机制不一样. IE浏览器通常是罪魁祸首. 你需要根据浏览器的不同而使用不同的事件处理代码.

4. 这些事件处理程序引用全局命名空间中的函数. 当你尝试整合其他库中的同名函数时, 它会让你头疼.

5. 这些事件处理程序合并了结构和行为. 这使你的代码更加难以维护, 扩展和理解.

总体来看, 当你按照这种风格编写JavaScript代码, 一切都很好. 然而有一件事并不是好的, 那就是代码的复杂度和可读性. 并不是给元素声明事件处理程序不起作用, 你通常给这些元素分配了ID, 获得这些元素的引用, 并给它设置了事件处理的回调函数. 你可以发明一个结构只用于清晰的创造它们之间的关联, 但大多数应用程序结束于设置在各处的事件处理函数.

在Angular中, 我们决定重新审视这个问题.

在这些概念诞生以来世界就已经改变了. 第1点, 这类有趣的群体已经不再有了. 如果你运行的浏览器不支持JavaScript, 那么你应该去使用20世纪90年代创建的网站. 至于第2点, 现代的屏幕阅读器已经跟上来了. 随着RAIA语义标签的正确使用,  你可以创造易访问的富UI应用. 现在手机上运行JavaScript与也能台式机能相提并论了.

因此现在的问题是: 重新恢复内联技术来解决我们第3点和第4点的可读性和简洁性的问题吗? 

正如前面所提到的, 对于大多数的内联事件处理程序, Angular都有一个等价形式的`ng-eventhandler="expression"`来替代`click`, `mousedown`, `change`等事件处理程序. 当用户点击一个元素时, 如果你希望得到一个响应, 你只需要简单的使用`ng-click`这样的指令:
```html
    <div ng-click="doSomething()">…</div>
```
你的大脑里可能会说"不, 这样并不好"? 好消息是你可以放松下来. 这些指令不同于它们事件处理程序的前身(标准事件处理程序的原始形式):

+ 在每个浏览器中的行为一致. Angular会给你处理好差异.

+ 不会在全局命名空间操作. 你所指定的表达式仅仅能够访问元素控制器作用域内的函数和数据.

最后一点听起来可能有点神秘, 因此让我们来看一个例子. 在一个典型的应用程序中, 你会创建一个导航栏和一个随着你从导航栏选择不同菜单而变化的内容区. 我们可以这样编写它的框架:
```html
    <div class="navbar" ng-controller="NavController">
    …
        <li class="menu-item" ng-click="doSomething()">Something</li>
    …
    </div>
    
    <div class="contentArea" ng-controller="ContentAreaController">
    …
        <div ng-click="doSomething()">…</div>
    …
    <div>
```
这里当用户点击navbar中的`<li>`和conent区中的`<div>`时都会调用一个称为`doSomething()`的函数. 作为开发人员, 你设置该函数调用你的控制器中的代码引用. 它们可能是相同或者不同的函数:
```js
    function NavController($scope){
        $scope.doSomething = doA;
    }
    
    function ContentAreaController($scope){
        $scope.doSomething = doB;
    }    
```
这里, `doA()`和`doB()`函数可能时相同或者不同的, 取决于你给它们的定义.

现在我们还剩下第5点, 合并结构和行为. 这是一个有争议的话题, 因为你不能指出任何负面的结果, 但它与我们大脑里所想的合并表现职责和应用程序逻辑的行为非常类似. 当人们谈及关于标记结构和行为分离的时候, 这当然会有负面的影响.

如果我们的系统面临这种耦合问题时, 这里有一个简单的测试可以帮助我们找出来: 我们可以给我们的应用程序逻辑创建一个单元测试, 而不需要DOM的存在.

在Angular中, 是的, 我们可以在控制器中只编写包含业务逻辑的代码而不必引用DOM. 在我们之前编写的JavaScript中, 这个问题在事件处理程序中是不存在的. 注意, 在这里以及在这本书的其他地方, 目前我们所编写的控制器中, 都没有引用DOM和任何DOM事件处理程序. 你可以很轻松创建出这些不带DOM的控制器. 所有的元素定位和事件处理程序都发生在Angular中.

对于这个问题在编写单元测试时. 如果你需要DOM, 你在测试中创建它, 只会增加测试程序的复杂度. 当你的页面发生变化时, 你需要在你的测试中改变DOM, 这样只会带来更多的维护工作. 最后, 访问DOM是很慢的, 测试缓慢意味着反馈不会及时以及最终解析都是缓慢的. Angular的控制器测试并没有这些问题. 

因此你可以很轻松的声明事件处理程序的简单性和可读性, 毫无罪恶感的违反最佳实践.

###列表, 表格和其他重复的元素

最有用可能就是Angular指令, `ng-repeat`对于集合中的每一项都创建一次一组元素的一份副本. 你应该在你想创建列表问题的任何地方使用它.

比如说我们给老师编写一个学生花名册的应用程序. 我们可能从服务器获得学生的数据, 但是在这个例子中, 我们只在JavaScript将它定义为一个模型:
```js
    var students = [{name: 'Mary Contrary', id:'1'},
                    {name: 'Jack Sprat', id: '2'},
                    {name: 'Jill Hill', id: '3'}];
                    
    function StudentListController($scope){
        $scope.students = students;
    }
```
我们可以像下面这样来显示学生列表:
```html
    <ul ng-controller="">
        <li ng-repeat="student in students">
            <a href="/student/view/{{student.id}}">{{student.name}}</a>
        </li>
    </ul>
```
`ng-repeat`将会制作标签内所有HTML的副本, 包括标签内的东西. 这样, 我们将看到:

+ Mary Contrary
+ Jack Sprat
+ Jill Hill

分别链接到*/student/view/1, /student/view/2, /student/view/3*.

正如我们之前所见, 改变学生数组将会自动改变渲染列表. 如果我们做一些例如插入一个新的学生到列表的事情:
```js
    var students = [{name: 'Mary Contrary', id:'1'},
                    {name: 'Jack Sprat', id: '2'},
                    {name: 'Jill Hill', id: '3'}];
                    
    function StudentListController($scope){
        $scope.students = students;
        
        $scope.insertTom = function(){
            $scope.students.splice(1, 0, {name: 'Tom Thumb', id: '4'});
        };
    }
```
然后在模板中添加一个按钮来调用:
```html
    <ul ng-controller="">
        <li ng-repeat="student in students">
            <a href="/student/view/{{student.id}}">{{student.name}}</a>
        </li>
    </ul>
    <button ng-click="insertTom()">Insert</button>
```
现在我们可以看到:

+ Mary Contrary
+ Tom Thumb
+ Jack Sprat
+ Jill Hill

`ng-repeat`指令还通过`$index`给你提供了当前元素的索引, 如果是集合中第一个元素, 中间的某个元素, 或者是最后一个元素使用`$first`, `$middle`和`$last`会给你提供一个布尔值.

你可以想象使用`$index`来标记表格中的行. 给定一个这样的模板:
```html
    <table ng-controller="AlbumController">
        <tr ng-repeat="track in album">
            <td>{{$index + 1}}</td>
            <td>{{track.name}}</td>
            <td>{{track.duration}}</td>
        </tr>
    </table>
```
这是控制器:
```js
    var album = [{name: 'Southwest Serenade', duration: '2:34'},
                 {name: 'Northern Light Waltz', duration: '3:21'},
                 {name: 'Eastern Tango', duration: '17:45'}];
                 
    function AlbumController($scope){
        $scope.album = album;
    };
```
我们得到如下结果:

1. Southwest Serenade     2:34
2. Northern Light Waltz   3:21
3. Eastern Tango         17:45

###隐藏与显示

对于菜单, 上下文敏感的工具[*原文:context-sensitive tools*]以及其他许多情况, 显示和隐藏元素是一个关键的特性. 正如在Angular中, 我们基于模型的变化触发UI的改变, 以及通过指令将改变反映到UI中. 

这里, `ng-show`和`ng-hide`用于处理这些工作. 它们基于传递给它们的表达式提供显示和隐藏的功能. 即, 当你传递的表达式为true时`ng-show`将显示元素, 当为false时则隐藏元素. 当表达式为true时`ng-hide`隐藏元素, 为false时显示元素. 这取决于你使用哪个更能表达的你意图.

这些指令通过适当的设置元素的样式为`display: block`来显示元素, 设置样式为`display: none`来隐藏元素. 让我们看一个正在构建的Death Ray控制板的虚拟的例子:
```html
    <div ng-controller="DeathrayMenuController">
        <p><button ng-click="toggleMenu()">Toggle Menu</button></p>
        <ul ng-show="menuState.show">
            <li ng-click="stun()">Stun</li>
            <li ng-click="disintegrate()">Disintegrate</li>
            <li ng-click="erase()">Erase from history</li>
        </ul>
    </div>
```
```js
    function DeathrayMenuController($scope){
        $scope.menuState = {
        	show: false
        };
        
        $scope.toggleMenu = function(){
            $scope.menuState.show = !$scope.menuState.show;
        };
        
        // death ray functions left as exercise to reader
    };
```
###CSS类和样式

显而易见, 现在你可以在你的应用程序中通过使用{{ }}插值符号绑定数据的方式动态的设置类和样式. 甚至你可以在你的应用程序中组成匹配的类名. 例如, 你想根据条件禁用一些菜单, 你可以像下面这样从视觉上显示给用户.

有如下CSS:
```css
    .menu-disabled-true {
        color: gray;
    }
```
你可以使用下面的模板在你的DeathRay指示`stun`函数来禁用某些元素:
```html
    <div ng-controller="DeatrayMenuController">
        <ul>
            <li class="menu-disabled-{{isDisabled}}" ng-click="stun()">Stun</li>
            ...
        </ul>
    </div>
```
你可以通过控制器适当的设置`isDisabled`属性的值:
```js
    function DeathrayMenuController($scope){
        $scope.isDisabled = false;
        
        $scope.stun = function(){
            //stun the target, then disable menu to allow regeneration
            $scope.isDisabled = 'true';
        };
    }
```
`stun`菜单项的class将设置为`menu-disabled-`加`$scope.isDisabled`的值. 因为它初始化为false, 默认情况下结果为`menu-disabled-false`. 而此时这里没有与CSS规则匹配的元素, 则没有效果. 当`$scope.isDisabled`设置为true时, CSS规则将变成`menu-disabled-true`, 此时则调用规则使文本为灰色.

这种技术也同样适用于嵌入内联样式, 例如`style="{{some expression}}"`.

虽然想法很好, 但是这里有一个缺点就是它使用了一个水平分割线来组合你的类名. 虽然在这个例子中很容易理解, 但是它可能很快就会变得难以管理, 你必须不断的阅读你的模板和JavaScript来正确的创建你的CSS规则.

因此, Angular提供了`ng-class`和`ng-style`指令. 它们都接受一个表达式. 这个表达式的计算结果可以是下列之一:

+ 一个使用空格分割类名的字符串
+ 一个类名数组
+ 类名到布尔值的映射

让我们想象一下, 你希望在应用程序头部的一个标准位置显示错误和警告给用户. 使用`ng-class`指令, 你可以这样做:
```css
    .error {
        background-color: red;
    }
    .warning {
        background-color: yellow;
    }
```
```html
    <div ng-controller="HeaderController">
        ...
        <div ng-class="{error: isError, warning: isWarning}">{{messageText}}</div>
        ...
        <button ng-click="showError()">Simulate Error</button>
        <button ng-click="showWarning()">Simulate Warning</button>
    </div>
```
```js
    function HeaderController($scope){
        $scope.isError = false;
        $scope.isWarning = false;
        
        $scope.showError = function(){
            $scope.messageText = 'This is an error';
            $scope.isError = true;
            $scope.isWarning = false;
        };
        
        $scope.showWarning = function(){
            $scope.messageText = 'Just a warning. Please carry on';
            $scope.isWarning = true;
            $scope.isError = false;
        };
    }
```
你甚至可以做出更漂亮的事情, 例如高亮表格中选中的行. 比方说, 我们要构建一个餐厅目录并且希望高亮用户点击的那行.

在CSS中, 我们设置一个高亮行的样式:
```css
    .selected {
        background-color: lightgreen;
    }
```
在模版中, 我们设置`ng-class`为`{selected: $index==selectedRow}`. 当模型中的`selectedRow`属性匹配ng-repeat的`$index`时设置class为selected. 我们还设置一个`ng-click`来通知控制器用户点击了哪一行:
```html
    <table ng-controller="RestaurantTableController">
        <tr ng-repeat="restaurant in directory" ng-click="selectRestaurant($index)" ng-class="{selected: $index==selectedRow}">
            <td>{{restaurant.name}}</td>
            <td>{{restaurant.cuisine}}</td>
        </tr>
    </table>
```
在我们的JavaScript中, 我们只设置虚拟的餐厅和创建`selectRow`函数:
```js
    function RestuarantTableController($scope){
        $scope.directory = [{name: 'The Handsome Heifer', cuisine: 'BBQ'},
                            {name: 'Green\'s Green Greens', cuisine: 'Salads'},
                            {name: 'House of Fine Fish', cuisine: 'Seafood'}];
        $scope.selectRestaurant = function(row){
            $scope.selectedRow = row;
        };
    }
```
###`src`和`href`属性注意事项

当数据绑定给一个`<img>`或者`<a>`标签时, 像上面一样在`src`或者`href`属性中使用{{ }}处理路径将无法正常工作. 因为在浏览器中图片与其他内容是并行加载的, 所以Angular无法拦截数据绑定的请求.

对于`<img>`而言最明显的语法便是:
```html
    <img src="/images/cats/{{favoriteCat}}">
```
相反, 你应该使用`ng-src`属性并像下面这样编写你的模板:
```html
    <img ng-src="/images/cats/{{favoriteCat}}">
```
同样的道理, 对于`<a>`标签你应该使用`ng-href`:
```html
    <a ng-href="/shop/category={{numberOfBalloons}}">some text</a>
```
###表达式

表达式背后的思想是让你巧妙的在你的模板, 应用程序逻辑以及数据之间创建钩子而与此同时防止应用程序逻辑偷偷摸摸的进入模版中.

直到现在, 我们一直主要是引用原生的数据作为表达式传递给Angular指令. 但是其实这些表达式可以做更多的事情. 你可以处理简单的数学运算(+, -, /, *, %), 进行比较(==, !=, >, <, >=, <=), 执行布尔逻辑运算(&&, !!, !)以及按位运算(\^, &, |). 你可以调用暴露在控制器的`$scope`对象上的函数, 你还可以引用数据和对象表示法([], {}, …).

下面都是有效表达式的例子:
```html
    <div ng-controller="SomeController">
        <div>{{recompute() / 10}}<div>
        <ul ng-repeat="thing in things">
            <li ng-class="{highlight: $index % 4 >= threshold($index)}">
                {{otherFunction($index)}}
            </li>
        </ul>
    </div>
```
这里的第一个表达式`recompute() / 10`是有效的, 是在模板中设置逻辑很好的好例子, 但是应该避免这种方式. 保持视图和控制器之间的职责分离可以确保它们容易理解和测试.

虽然你可以使用表达式做很多事情, 它们由Angular自定义的解释器部分计算. 他们并不使用JavaScript的`eval()`执行, eval()有相当多的限制.

相反, 它们使用Angular自带的自定义解释器执行. 在里面, 你不会看到循环结构(for, while等等), 流程控制语句(if-else, throw)或者改变数据的运算符(++, --). 当你需要使用这些类型的运算时, 你应该在你的控制器中使用指令进行处理.

尽管表达式在很多方面比JavaScript更加严格, 但它们对`undefined`和`null`并不是很严格(更宽松). 模板只是简单的渲染一些东西, 并不会抛出一个`NullPointerException`的错误. 这样就允许你安全的使用模型而没有限制, 并且只要它们得到数据填充就让它们出现在用户界面中.

###分离用户界面(UI)和控制器职责

在你的应用程序中控制器有三个职责:

+ 在你的应用程序的模型中设置初试状态.[初始化应用程序]
+ 通过`$scope`暴露模型和函数到视图中.
+ 监控模型的改变并触发行为.

对于第一点第二点在本章的已经看过更多例子. 稍候我们会讨论最后一点. 然而, 控制器其概念上的目的, 是提供代码或者执行用户与视图交互愿望的逻辑. 

为了保持控制器的小巧和易于管理, 我们建议你针对视图的每一个区域创建一个控制器. 也就是说, 如果你有一个菜单则创建一个`MenuController`. 如果你有一个面包屑导航, 则编写一个`BreadcrumbController`, 等等.

你可能开始懂了, 但是需要明确的将控制器绑定到一个指定的DOM块中用于管理它们. 有两种主要的方式关联控制器与DOM节点, 一种方式是在模板中指定一个`ng-controller`属性, 另一种方式是通过`route`(路由)关联一个动态加载的DOM模板片段, 也称作视图.

我们将在本章的后面再讨论关于视图和路由的信息.

如果你的UI中有一个复杂的片段, 你可以通过创建嵌套的控制器, 通过继承树来共享模型和函数来保持你的代码间接性和可维护性. 嵌套控制器很简单, 你可以简单的在另一个DOM中分配一个控制器到一个DOM元素中做到这一点, 就像这样:
```html
    <div ng-controller="ParentController">
        <div ng-controller="ChildController">…</div>
    </div>
```
虽然我们将这个表达为控制器嵌套, 实际的嵌套发生在作用域中($scope对象中). 传递给嵌套控制器的`$scope`继承自父控制器的`$scope`原型, 这意味着传递给`ChildController`的`$scope`将有权访问传递给`ParentController`的`$scope`的所有属性.

###使用作用域发布模型数据

将`$scope`对象传递给我们的控制器便是我们将模型数据暴露给视图的机制. 可能你的应用程序中还有其他的数据, 但Angular中只能够通过scope访问它可以访问的模型部分的属性. 你可以认为scope就是作为一个上下文环境用于在你的模型中观察变化的.

我们已经看过了很多明确设置作用域的例子, 就像`$scope.count = 5`. 也有一些间接的方法在模板内设置其自身的模型. 你可以像下面这样做:

1. 通过表达式. 由于表达式运行在控制器的作用域关联的元素的上下文中, 在表达式中设置属性与在控制器的作用域中设置一个属性一样. 

也就是像这样:  
```html
    <button ng-click="count=3">Set count to three</button>
```
这样做也有相同的效果:
```html
    <div ng-controller="CountController">
        <button ng-click="setCount()">Set count to three</button>
    </div>
```
CountController定义如下:
```js
    function CountController($scope){
        $scope.setCount = function(){
            $scope.count = 3;
        }
    }
```
2. 在表单的输入框中使用`ng-model`. 在表达式中, 模型被指定为`ng-model`的参数也适用于控制器作用域范围. 此外, 这将在表单字段和你指定的模型之间创建一个双向数据绑定.

###使用$watch监控模型变化

所有scope函数中最常用的可能就是$watch了, 当你的模型部分发生变化时它会通知你. 你可以监控单个对象属性, 也可以监控计算结果(函数), 几乎所有的事物都可当作一个属性或者一个JavaScript运算能够被访问. 该函数的签名如下:
```js
    $watch(watchFn, watchAction, deepWatch);
```
每个参数的详细信息如下:

**watchFn**

> 这个参数是一个Angular字符串表达式或者是一个返回你所希望监控的模型当前值的函数. 这个表达式会被多次执行, 因此你需要确保它不会有副作用. 也就是说, 它可以被调用多次而不改变状态. 同样的原因, 监控表达式也应该是运算复杂度低的(执行简单的运算). 如果你传递一个字符串的表达式, 它将会对其调用的(执行的表达式)作用域中的有效对象求值.

**watchAction**

> 这是`watchFn`发生变化时会被调用的函数或者表达式. 在函数形式中, 它接受`watchFn`的新值, 旧值以及作用域的引用. 其签名就是`function(newValue, oldValue, scope)`.

**deepWatch**

> 如果设置为true, 这个可选的布尔参数用于告诉Angular检查所监控的对象中每一个属性的变化. 如果你希望监控数组的个别元素或者对象的属性而不是一个普通的值, 那么你应该使用它. 由于Angular需要遍历数组或者对象, 如果集合(数组元素/对象成员)很大, 那么计算的代价会非常高.

当你不再想收到变化通知时, `$watch`函数将返回一个注销监听器的函数.

如果我们像监控一个属性, 然后在稍后注销它, 我们将使用下面的方式:
```js
    ...
    var dereg = $scope.$watch('someModel.someProperty', callbackOnChange);
    ...
    dereg();
```
让我们回顾一下第一章中完整的购物车示例. 比方说, 当用户在他的购物车中添加了超出100美元的商品时, 我们希望申请10美元的优惠. 我们使用下面的模板:
```html
    <div ng-controller="CartController">
        <div ng-repeat="item in items">
            <span>{{item.title}}</span>
            <input ng-model="item.quantity">
            <span>{{item.price | currency}}</span>
            <span>{{item.price * item.quantity | currency}}</span>
        </div>
        <div>Total: {{totalCart() | currency}}</div>
        <div>Discount: {{bill.discount | currency}}</div>
        <div>Subtotal: {{subtotal() | currency}}</div>
    </div>
```
紧接着是`CartController`, 它看起来像下面这样:
```js
    function CartController($scope){
        $scope.bill = {};

        $scope.items = [
            {title: 'Paint pots', quantity: 8, price: 3.95},
            {title: 'Polka dots', quantity: 17, price: 12.95},
            {title: 'Pebbles', quantity: 5, price: 6.95}
        ];

        $scope.totalCart = function(){
            var total = 0;
            for (var i = 0, len = $scope.items.length; i < len; i++){
                total = total + $scope.items[i].price* $scope.items[i].quantity;
            }

            return total;
        };

        $scope.subtotal = function(){
            return $scope.totalCart() - $scope.bill.discount;
        };

        function calculateDiscount(newValue, oldValue, scope){
            $scope.bill.discount = newValue > 100 ? 10 : 0;
        }

        $scope.$watch($scope.totalCart, calculateDiscount);
    }
```
注意`CartController`的底部, 我们给用于计算所购买商品总价的`totalCart()`的值设置了一个监控. 每当这个值变化时, 监控都会调用`calculateDiscount()`, 并且会给discount(优惠项)设置一个适当的值. 如果总价为$100, 我们将设置优惠为$10. 否则, 优惠就为$0.

你可以看到这个展示给用户的例子如图2-1所示:

![use-$watch](figure/watch1.png)

图2-1 Shopping cart with discount

###watch()中的性能注意事项

前面例子会正确的执行, 但是这里有一个潜在的性能问题. 虽然并不明显, 如果你在`totalCart()`中设置一个调试断点, 你会发现在渲染页面时它被调用了6次. 虽然在这个应用程序中你从来没有注意到它, 但是在更多复杂的应用程序中, 运行它6次可能是一个问题.

为什么是6次? 其中3次我们可以很轻易的跟踪到, 因为它分别在下面三个过程中运行一次:

+ 在`{{totalCart() | currency}}`模板中
+ `subtotal()`函数中
+ `$watch()`函数中

然后是Angular再运行它们一次, 因而带给我们6次运行. Angular这样做是为了验证在你的模型中变化是否完全传播出去以及验证你的模型是否稳定. Angular通过检查一份所监控属性的副本与它们当前值比较来确认它们是否改变. 事实上, Angular也可以运行它多达十次来确保是否完全传播开. 如果发生这种情况, 你可能需要依赖循环来修复它.

虽然你现在会担心这个问题, 但是当你阅读完本书时它可能就不再是问题了. 然而Angular不得不在JavaScript中实现数据绑定, 我们一直与TC39的人共同努力实现一个底层的原生的`Object.observe()`. 一旦有了它, Angular将自动使用`Object.observe()`随时随地呈现给你一个原生效率的数据绑定.

> 译注: [TC39](http://www.ecma-international.org/memento/TC39.htm)

在下一章中你会看到, Angular有一个很好的Chrome调试扩展程序(Chrome插件)Batarang, 它将自动给你突出(高亮)昂贵的数据绑定(从性能的角度而言, 表示数据绑定的方式并不是较好的方式).

> 译注:
> 
> + [Batarang](https://chrome.google.com/webstore/detail/ighdmehidhipcmcojjgiloacoafjmpfk) - 这是一个Angular调试与性能监控工具.
> + [Batarang-Github](https://github.com/angular/angularjs-batarang)

现在我们知道了这个问题, 这里有一些方法可以解决它. 一种方式是在items数组变化时创建`$watch`并且只重新计`$scope`的total, discount和subtotal属性值.

做到这一点, 我们只需要使用这些属性更新模板:
```html
    <div>Total: {{bill.total | currency}}</div>
    <div>Discount: {{bill.discount | currency}}</div>
    <div>Subtotal: {{bill.subtotal | currency}}</div>
```
然后, 在JavaScript中, 我们要监控items数组, 以及调用一个函数来计算数组任意改变的总值:
```js
    function CartController($scope){
        $scope.bill = {};
        
        $scope.items = [
            {title: 'Paint pots', quantity: 8, price: 3.95},
            {title: 'Polka dots', quantity: 17, price: 12.95},
            {title: 'Pebbles', quantity: 5, price: 6.95}
        ];
        
        var calculateTotals = function(){
            var total = 0;
            for(var i = 0, len = $scope.items.length; i < len; i++){
                total = total + $scope.items[i].price * $scope.items[i].quantity;
            }
            
            $scope.bill.totalCart = total;
            $scope.bill.discount = total > 100 ? 10 : 0;
            $scope.bill.subtotal = total - $scope.bill.discount;
        };
        
        $scope.$watch('items', calculateTotals, true);
    }
```
注意这里`$watch`指定了一个`items`字符串. 这可能是因为`$watch`函数可以接受一个函数(正如我们之前那样)或者一个字符串. 如果传递一个字符串给`$watch`函数, 在`$scope`调用的作用域中它将被当作一个表达式.

这种策略在你的应用程序中可能工作得很好. 然而, 由我监控的是items数组, Angular将会制作一个副本以供我们进行比较. 对于一个较大的items清单, 如果我们在Angular每一次计算页面结果时只重新计算bill属性值, 它可能表现得更好. 我们可以通过创建一个`$watch`来做到这一点, 它带有只用于重新计算属性的`watchFn`函数. 就像这样:
```js
    $scope.$watch(function(){
        var total = 0;
        for(var i = 0, i < $scope.items.length; i++){
            total = total + $scope.items[i].price * $scope.items[i].quantity;
        };
        
        $scope.bill.totalCart = total;
        $scope.bill.discount = total > 100 ? 10 : 0;
        $scope.bill.subtotal = total - $scope.bill.discount;
    });
```
####多个监控

如果你想监控多个属性或者对象, 并且每当它们发生任何变化时都执行一个函数. 你有两个基本的选择:

+ 监控属性索引值.
+ 把它们放入数组或者对象总并且将传递的`deepWatch`设置为true.

> 译注: 原文中两个选项排列顺序颠倒. 译文中纠正了顺序并给出对应的信息.

在第一种情况下, 如果作用域中有一个对象拥有两个属性`a`和`b`, 并且希望在发生变化时执行`callMe()`函数, 你应该同时监控它们, 就像这样:
```js
    $scope.$watch('things.a + things.b', callMe(…));
```
当然, 属性`a`和`b`可能在不同的对象中, 只要你喜欢你也可以制作这个列表. 如果列表很长, 你可能更喜欢编写一个返回索引值的函数而不是依靠一个逻辑表达式.

在第二种情况下, 你可能希望监控`things`对象中的所有属性. 在这种情况下, 你可以这样做:
```js
    $scope.$watch('things' calMe(…), true);
```
这里, 通过将第三个参数设置为`true`来要求Angular遍历`things`对象的属性并在它们发生任何改变时调用`callMe()`. 这同样适用于数组, 只是这里是针对一个对象.

##使用模块组织依赖

在任何不平凡的应用程序中, 在你的代码领域中弄清楚如何组织功能职责通常都是一项艰巨的任务. 我们已经看到了控制器是如何到视图模板中给我们提供一个存放暴露正确数据和函数的区域. 但是我们在哪里安置支持应用程序的的其他代码呢? 最明显的方式就是将它们放置在控制器中的函数中.

对于小型应用程序和目前我们所见过的例子,这种方式工作得很好, 但是在实际的应用程序中将很快变得难以管理. 控制器将成为堆积一切以及我们需要做任何事情的垃圾场. 它们可能很难理解, 也可能很难改变(难以维护).

引入模块. 在你的应用程序功能区, 它们提供了一种组织依赖的方式, 以及一种自解决依赖的机制(也称为依赖注入[*第一章中已经介绍了什么是依赖注入*]). 一般情况下, 我们称之为依赖关系服务, 它们给我们的应用程序提供特殊服务.

比如, 如果在我们的购物网站中控制器需要从服务器获取一个出售项目列表, 我们需要一些对象--让我们称之为`Items`--注意这里是从服务器获取的项目. 反过来, `Items`对象, 需要一些方式通过XHR或者WebSockets与服务器上的数据库通信.

不适用模块处理看起来像这样:
```js
    function ItemsViewController($scope){
        // 向服务器发起请求
        ...
        
        // 进入Items对象解析响应
        ...
        
        // 在$scope中设置Items数组以便视图可以显示它
    }
```
然而这确实能够工作, 但是它存在一些潜在的问题.

+ 如果一些其他的控制器还需要从服务器获取`Items`, 那我们现在要复制这个代码. 这造成了维护的负担, 如果我们现在要构造模式或者其他的变化, 我们必须在好几个地方更新这个代码.
+ 考虑到其他因素, 如服务器验证, 解析复杂度等等, 这也是很难推断控制器对象职责界限的原因, 代码也很难阅读.
+ 对这段代码进行单元测试, 我们需要一台实际运行的服务器或者使用XMLHttpRequest打补丁返回模拟数据. 运行服务器进行测试将导致测试很慢, 配置它很痛苦, 它通常展示了测试中的碎片. 而打补丁的方式解决了速度和碎片问题, 但是这意味着你必须记住在测试中清理任何不定对象, 这样就带来了额外的复杂度和脆弱性, 因为它迫使你指定准确的线上版本的数据格式(每当格式变化时都需要更新测试).

对于模块和从它们哪里获取的依赖注入, 我们就可以编写更简洁的控制器, 像这样:

    function ShoppingController($scope, Items){
        $scope.items = Items.query();
    }
        
现在你可能会问自己, '当然, 这看起来很酷, 但是这个Items从哪里来?'. 前面的代码假设我们已经定义了作为服务的`Items`.

服务是一个单独的对象(单例对象), 它执行必要的任务来支持应用程序的功能. Angular自带了很多服务, 例如`$location`, 用于与浏览器中的地址交互, `$route`, 用于基于位置(URL)的变化切换视图, 以及`$http`用于与服务器通信.

你可以也应该创建你自己的服务去处理应用程序所有的特殊任务. 在需要它们时服务可以共享给任何控制器. 因此, 当你需要跨控制器通信和共享状态时使用它们是一个很好的机制. Angular绑定的服务都以`$`开头, 所以你也能够命名它们为任何你喜欢的东西, 这是一个很好的主意, 以避免使用`$`开头带来的命名冲突问题.

你可以使用模块对象的API来定义服务. 这里有三个函数用于创建通用服务, 它们都有不同层次的复杂性和能力:
```html
<table>
    <thead>
        <tr>
            <th>Function</th>
            <th>定义(Defines)</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>provider(name, Object/constructor())</td>
            <td>一个可配置的服务, 带有复杂的创建逻辑. 如果你传递一个对象, 它应该有一个名为`$get`的函数, 用于返回服务的实例. 否则, Angular会假设你传递了一个构造函数, 当调用它时创建实例.</td>
        </tr>
        <tr>
            <td>factory(name, $get Function())</td>
            <td>一个不可配置的服务也带有复杂的创建逻辑. 你指定一个函数, 当调用时, 返回服务实例. 你可以认为这和<code>provider(name, { $get: $getFunction()})</code>一样</td>
        </tr>
        <tr>
            <td>service(name, constructor())</td>
            <td>一个不可配置的服务, 其创建逻辑简单. 就像<code>provider</code>的构造函数选项, Angular调用它来创建服务实例.</td>
        </tr>                
    </tbody>
</table>
```
我们稍后再来看`provider()`的配置选项, 现在我们先来使用`factory()`讨论前面的Items例子. 我们可以像这样编写服务:
```js
    // Create a module to support our shopping views.
    var shoppingModule = angular.module('ShoppingModule', []);
    
    // Set up service factory to create our Items interface to the server-side database
    shoppingModule.factory('Items', function(){
        var items = {};
        items.query = function(){
            // In real apps, we'd pull this data from the server…
            return [
                {title: 'Paint pots', description: 'Pots full of paint', price: 3.95},
                {title: 'Polka dots', description: 'Dots with polka', price: 2.95},
                {title: 'Pebbles', description: 'Just little rocks', price: 6.95}
            ];
        };
        
        return items;
    });
```
当Angular创建`ShoppingController`时, 它会将`$scope`和我们刚才定义的新的Items服务传递进来. 这是通过参数名称匹配完成的. 也就是说, Angular会看到我们的`ShoppingController`类的函数签名, 并通知它(控制器)发现一个Items对象. 由于我们定义Items为一个服务, 它会知道从哪里获取它.

以字符串的形式查询这些依赖结果意味着作为参数注入的函数就像控制器的构造函数一样是顺序无关的. 并不是必须这样:
```js
    function ShoppingController($scope, Items){...}    
```
我们也可以这样编写:
```js
    function ShoppingController(Items, $scope){...}
```
依然和我们所希望的功能一样.

为了在模板中使用它, 我们需要告诉`ng-app`指令我们的模块名称, 就像下面这样:
```html
    <html ng-app="ShoppingModule">
```
为了完成这个例子, 我们可以这样实现模板的其余部分:
```html
    <body ng-controller="ShoppingController">
        <h1>Shop!</h1>
        <table>
            <tr ng-repeat="item in items">
                <td>{{item.title}}</td>
                <td>{{item.description}}</td>
                <td>{{item.price | currency}}</td>
            </tr>
        </table>
    </body>
```
应用的返回结果看起来如图2-2所示:

![use-module](figure/useModule.png)

图2-2 Shop items

###我们需要多少模块?

作为服务本身可以有依赖关系, Module API允许你在的依赖中定义依赖关系.

在大多数应用程序中, 创建一个单一的模块将所有的代码放入其中并将所有的依赖也放在里面足以很好的工作. 如果你使用来自第三方库的服务或者指令, 它们自带有其自身的模块. 由于你的应用程序依赖它们, 你可以引用它们作为你的应用程序的依赖.

举个例子, 如果你要包含(虚构的)模块SnazzyUIWidgets和SuperDataSync, 应用程序的模块声明看起来像这样:
```js
    var appMod = angular.module('app', ['SnazzyUIWidgets', 'SuperDataSync']);
```
##使用过滤器格式化数据

过滤器允许你在模板中使用插值方式声明如何转换数据并显示给用户. 使用过滤器的语法如下:

    {{expression | filterName : parameter1 : … parameterN }}
    
其中表达式是任意的Angular表达式, `filterName`是你想使用的过滤器名称, 过滤器的参数使用冒号分割. 参数自身也可以是任意有效的Angular表达式.

Angular自带了几个过滤器, 像我们已经看到的currency:

    {{12.9 | currency}}
    
这段代码显示如下:

> $12.9

你不仅限于使用绑定的过滤器(Angular内置的), 你可以简单的编写你自己的过滤器. 例如, 如果我们想创建一个过滤器来让标题的首字母大写, 我们可以像下面这样做:
```js
    var homeModule = angular.module('HomeModule', []);
    homeModule.filter('titleCase', function(){
        var titleCaseFilter = function(input){
            var words = input.split(' ');
            for(var i = 0; i < words.length; i++){
                words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
            }
            
            return words.join(' ');
        };
        return titleCaseFilter;
    });
```
有一个像这样的模板:
```html
    <body ng-app="HomeModule" ng-controller="HomeController">
        <h1>{{pageHeading | titleCase}}</h1>
    </body>
```
然后通过控制器插入`pageHeading`作为一个模型变量:
```js
    function HomeController($scope){
        $scope.pageHeading = 'behold the majesty of you page title';
    }
```
我们会看到如图2-3所示的东西:

![titleCase](figure/titleCase.png)

图2-3 Title case filter

##使用路由和$location更新视图

尽管Ajax从技术上讲是单页应用程序(理论上它们仅仅在第一次请求时加载HTML页面, 然后只需在DOM中更新区块), 我们通常会有多个子页面视图用于适当的显示给用户或者隐藏.

我们可以使用Angular的`$route`服务来给我们管理这个场景. 让你指定路由, 对于浏览器指向给定的URL, Angular将加载并显示一个模板, 并且实例化一个控制器给模板提供上下文环境.

通过调用`$routeProvider`服务的功能作为配置块来在你的应用程序中创建视图. 就像这样的伪代码:
```js
    var someModule = angular.module('someModule', [… Module dependencies …]);
    someModule.config(function($routeProvider){
        $routeProvider.
            when('url', {controller: aController, templateUrl: '/path/to/template'}).
            when(…other mappings for your app …).
            … 
            otherwise(…what to do if nothing else matches…);
    });
```
上面的代码表示当浏览器的URL变化为指定的URL时, Angular将从`/path/to/template`中加载模板, 并使用`aController`关联这个模板的根元素(就像我们输入`ng-controller=aController`).

在最后一行调用`otherwise()`用于告诉路由如果没有其他的匹配则跳到哪里.

让我们来使用一下. 我们正在构建一个email应用程序将轻松的战胜Gmail, Hotmail以及其他的. 我们暂且称它为A-mail. 现在, 让我们从简单的开始. 我们的首屏中显示一个包括日期, 标题以及发送者的邮件信息列表. 当你点击一个信息, 它应该向将邮件的正文信息显示给你.

> 由于浏览器的安全限制, 如果你想自己测试这些代码, 你需要在一个Web服务器进行而不是使用`file://`. 如果你安装了Python, 你可以在你的工作目录通过执行`python -m SimpleHTTPServer 8888`来使用这些代码.

对于主模板, 我们会做一点不同的东西. 而不是将所有的东西都放在首屏来加载, 我们只会创建一个用于放置视图的布局模板. 我们会持续在视图中放置视图, 比如菜单. 在这种情况下, 我们只需要显示一个标题包含应用的名称. 然后使用`ng-view`指令来告诉Angular我们希望视图出现在哪里.

###*index.html*
```html
    <html ng-app="AMail">
        <head>
            <script src="js/angular.js"></script>
            <script src="js/angular-route.js"></script>
            <script src="js/controllers.js"></script>
        </head>
        <body>
            <h1>A-Mail</h1>
            <div ng-view></div>
        </body>
    </html>
```
由于我们的视图模板将被插入到刚刚创建的容器中, 我们可以把它们编写为局部的HTML文档. 对于邮件列表, 我们将使用`ng-repeat`来遍历信息列表并将它们渲染到一个表格中.

###*list.html*
```html
    <table>
        <tr>
            <td><strong>Sender</strong></td>
            <td><strong>Subject</strong></td>
            <td><strong>Date</strong></td>
        </tr>
        <tr ng-repeat="message in messages">
            <td>{{message.sender}}</td>
            <td><a href='#/view/{{message.id}}'>{{message.subject}}</a></td>
            <td>{{message.date}}</td>
        </tr>
    </table>
```
注意这里我们打算让用户通过点击主题将他导航到详细信息中. 我们将URL数据绑定到`message.id`上, 因此点击一个`id=1`的消息将使用户跳转到`/#/view/1`. 我们将通过url进行导航, 也称为深度链接, 在详细信息视图的控制器中, 让特定的消息对应一个详情视图.

为了创建消息的详情视图, 我们将创建一个显示单个message对象属性的模板.

###*detail.html*
```html
    <div><strong>Subject:</strong> {{message.subject}}</div>
    <div><strong>Sender:</strong> {{message.sender}}</div>
    <div><strong>Date:</strong> {{message.date}}</div>
    <div>
        <strong>To:</strong>
        <span ng-repeat="recipient in message.recipients">{{recipient}}</span>
    </div>
    <div>{{message.message}}</div>
    <a href="#/">Back to message list</a>
```
现在, 将这些模板与一些控制器关联起来, 我们将配置`$routeProvider`与URLs来调用控制器和模板.

###*controllers.js*
```js
    //Create a module for our core AMail services
    var aMailServices = angular.module('AMail', ['ngRoute']);
    
    //Set up our mappings between URLs, tempaltes. and  controllers
    function emailRouteConfig($routeProvider){
        $routeProvider.
        when('/', {
            controller: ListController,
            templateUrl: 'list.html'
        }).
        // Notice that for the detail view, we specify a parameterized URL component by placing a colon in front of the id
        when('/view/:id', {
            controller: DetailController,
            templateUrl: 'detail.html'
        }).
        otherwise({
            redirectTo: '/'
        });
    };
    
    //Set up our route so the AMailservice can find it
    aMailServices.config(emailRouteConfig);
    
    //Some take emails
    messages = [{
        id: 0, sender: 'jean@somecompany.com',
        subject: 'Hi there, old friend',
        date: 'Dec 7, 2013 12:32:00',
        recipients: ['greg@somecompany.com'],
        message: 'Hey, we should get together for lunch somet ime and catch up. There are many things we should collaborate on this year.'
    },{
        id: 1, sender: 'maria@somecompany.com',
        subject : 'Where did you leave my laptop?' ,
        date: 'Dec 7, 2013 8:15:12',
        recipients: ['greg@somecompany.com'],
        message: 'I thought you were going to put it in my desk drawer. But i t does not seem to be there. '
    },{
        id: 2, sender: 'bill@somecompany.com',
        subject: 'Lost python',
        date: 'Dec 6, 2013 20:35:02',
        recipients: ['greg@somecompany.com'],
        message: "Nobody panic, but my pet python is missing from her cage. She doesn't move too fast, so just call me if you see her."
    }];

    // Publish our messages for the list template

    function ListController($scope){
        $scope.messages = messages;
    }

    //Get the message id fron the route (parsed from the URL) and use it to find the right message object.
    function DetailController($scope, $routeParams){
        $scope.message = messages[$routeParams.id];
    }
```
我们已经创建了一个带有多个视图的应用程序的基本结构. 我们通过改变URL来切换视图. 这意味着用户也能够使用前进和后退按钮进行工作. 用户可以在我们的应用程序中添加书签和邮件链接, 即使只有一个真正的HTML页面.

##对话服务器

好了, 闲话少说. 实际的应用程序通常与真正的服务器通讯. 移动应用和新兴的Chrome桌面应用程序可能有些例外, 但是对于其他的一切, 你是否希望它持久保存云端或者与用户实时交互, 你可能希望你的应用程序与服务器通信.

对于这一点Angular提供了一个名为`$http`的服务. 它有一个抽象的广泛的列表使得它能够很容易与服务器通信. 它支持普通的HTTP, JSONP以及CORS. 还包括防止JSON漏洞和XSRF的安全协议. 它让你很容易转换请求和数据响应, 甚至还实现了简单的缓存. 

比方说, 我们希望从服务器检索购物站点的商品而不是我们的内存中模拟. 编写服务器的信息超出了本书的范围, 因此让我们想象一下我们已经创建了一个服务, 当你构造一个`/product`查询时, 它返回一个JSON形式的产品列表.

给定一个响应, 看起来像这样:
```json
    [
        {
            "id": 0,
            "title": "Paint pots",
            "description": "Pots full of paint",
            "price": 3.95
        },
        {
            "id": 1,
            "title": "Polka dots",
            "description": "Dots with that polka groove",
            "price": 12.95
        },
        {
            "id": 2,
            "title": "Pebbles",
            "description": "Just little rocks, really",
            "price": 6.95
        }
        … etc …     
    ]
```
我们可以这样编写查询:
```js
    function ShoppingController($scope, $http){
        $http.get('/products').success(function(data, status, headers, config){
            $scope.items = data;
        });
    }
```
然后像这样在模板中使用它:
```html
    <body ng-controller="ShoppingController">
        <h1>Shop!<h1>
        <table>
            <tr ng-repeat="item in items">
                <td>{{item.title}}</td>
                <td>{{item.description}}</td>
                <td>{{item.price | currency}}</td>
            </tr>
        </table>
    </body>
```
正如我们之前所学习到的, 从长远来看我们将这项工作委托到服务器通信服务上可以跨控制器共享是明智的. 我们将在第5章来看这个结构和全方位的讨论`$http`函数.

##使用指令更新DOM

指令扩展HTML语法, 也是将行为与DOM转换的自定义元素和属性关联起来的方式. 通过它们, 你可以创建复用的UI组件, 配置你的应用程序, 做任何你能想到在模板中要做的事情.

你可以使用Angular自带的内置指令编写应用, 但是你可能会希望运行你自己所编写的指令的情况. 当你希望处理浏览器事件和修改DOM时, 如果无法通过内置指令支持, 你会知道是时候打破指令规则了. 你所编写的代码在指令中, 不是在控制器中, 服务中, 也不是应用程序的其他地方.

与服务一样, 通过module对象的API调用它的`directive()`函数来定义指令, 其中`directiveFunction`是一个工厂函数用于定义指令的功能(特性).
```js
	var appModule = angular.module('appModule', [...]);
	appModule.directive('directiveName', directiveFunction);
```
编写指令工厂函数是很深奥的, 因此在这本书中我们专门顶一个完整的一章. 吊吊你的胃口, 不过, 我们先来看一个简单的例子.

HTML5中有一个伟大的称为`autofocus`的新属性, 将键盘的焦点放到一个input元素. 你可以使用它让用户第一时间通过他们的键盘与元素交互而不需要点击. 这是很好的, 因为它可以让你声明指定你希望浏览器做什么而无需编写任何JavaScript. 但是如果你希望将焦点放到一些非input元素上, 像链接或者任何`div`上会怎样? 如果你希望它也能工作在不支持HTML5中会怎样? 我们可以使用一个指令做到这一点.
```js
	var appModule = angular.module('app', []);
	
	appModule.directive('ngbkFocus', function(){
		return {
			link: function(scope, elements, attrs, controller){
				elements[0].focus();
			}
		};
	});
```
这里, 我们返回指令配置对象带有指定的link函数. 这个link函数获取了一个封闭的作用域引用, 作用域中的DOM元素, 传递给指令的任意属性数组, 以及DOM元素的控制器, 如果它存在. 这里, 我们仅仅只需要获取元素并调用它的`focus()`方法.

然后我们可以像这样在一个例子中使用它:

###*index.html*
```html
	<html lang="en" ng-app="app">
		...include angular and other scripts...
		<body ng-controller="SomeController">
			<button ng-click="clickUnfocused()">
				Not focused
			</button>
			<button ngbk-focus ng-click="clickFocused()">
				I'm very focused!
			</button>
			<div>{{message.text}}</div>
		</body>
	</html>
```
###*controller.js*
```js
	function SomeController($scope) {
		$scope.message = { text: 'nothing clicked yet' };

		$scope.clickUnfocused = function() {
			$scope.message.text = 'unfocused button clicked';
		};

		$scope.clickFocused = function {
			$scope.message.text = 'focus button clicked';
		}
	}

	var appModule = angular.module('app', ['directives']);
```
当载入页面时, 用户将看到标记为"I'm very focused!"按钮带有高亮焦点. 敲击空格键或者回车键将导致点击并调用`ng-click`, 将设置div的文本为"focus button clicked". 在浏览器中打开这个页面, 我们将看到如图2-4所示的东西:

![foucsed](figure/custom-directive.png)

图2-4 Foucs directive

##验证用户输入

Angular带有几个适用于单页应用程序的不错的功能来自动增强`<form>`元素. 其中之一个不错的特性就是Angular让你在表单内的input中声明验证状态, 并允许在整组元素通过验证的情况下才提交.

例如, 如果我们创建一个登录表单, 我们必须输入一个名称和email, 但是有一个可选的年龄字段, 我们可以在他们提交到服务器之前验证多个用户输入. 如下加载这个例子到浏览器中将显示如图2-5所示:

![valid](figure/signup.png)

图2-5. Form validation

我们还希望确保用户在名称字段输入文本, 输入正确形式的email地址, 以及他可以输入一个年龄, 它才是有效的.

我们可以在模板中做到这一点, 使用Angular的`<form>`扩展和各个input元素, 就像这样:
```html
	<h1>Sign Up</h1>
	<form name='addUserForm'>
		<div>First name: <input ng-model='user.first' required></div>
		<div>Last name: <input ng-model='user.last' required></div>
		<div>Email: <input type='email' ng-model='user.email' required></div>
		<div>Age: <input type='number' ng-model='user.age' ng-maxlength='3' ng-minlength='1'></div>
		<div><button>Submit</button></div>
	</form>
```
注意, 在某些字段上我们使用了HTML5中的`required`属性以及`email`和`number`类型的input元素来处理我们的验证. 这对于Angular来说是很好的, 在老式的不支持HTML5的浏览中, Angular将使用形式相同职责的指令.

然后我们可以通过改变引用它的形式来添加一个控制器处理表单的提交.
```html
	<form name='addUserForm' ng-controller="AddUserController">
```
在控制器里面, 我们可以通过一个称为`$valid`的属性来访问这个表单的验证状态. 当所有的表单input通过验证的时候, Angular将设置它($valid)为true. 我们可以使用`$valid`属性做一些时髦的事情, 比如当表单还没有完成时禁用提交按钮.

我们可以防止表单提交进入无效状态, 通过给提交按钮添加一个`ng-disabled`.
```html
	<button ng-disabled='!addUserForm.$valid'>Submit</button>
```
最后, 我们可能希望控制器告诉用户她已经添加成功了. 我们的最终模板看起来像这样:
```html
	<h1>Sign Up</h1>
	<form name='addUserForm' ng-controller="AddUserController">
		<div ng-show='message'>{{message}}</div>
		<div>First name: <input name='firstName' ng-model='user.first' required></div>
		<div>Last name: <input ng-model='user.last' required></div>
		<div>Email: <input type='email' ng-model='user.email' required></div>
		<div>Age: <input type='number' ng-model='user.age' ng-maxlength='3'
		ng-min='1'></div>
		<div><button ng-click='addUser()'
		ng-disabled='!addUserForm.$valid'>Submit</button></div>
	</form>
```
接下来是控制器:
```js
	function AddUserController($scope) {
		$scope.message = '';

		$scope.addUser = function () {
			// TODO for the reader: actually save user to database...
			$scope.message = 'Thanks, ' + $scope.user.first + ', we added you!';
		};
	}
```
##小结

在前两章中, 我们看到了Angular中所有最常用的功能(特性). 对每个功能的讨论, 许多额外的细节信息都没有覆盖到. 在下一章, 我们将让你通过研究一个典型的工作流程了解更多的信息.
