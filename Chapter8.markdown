#备忘与诀窍

目前为止，之前的章节已经覆盖了Angular所有功能结构中的大多数，包括指令,服务,控制器,资源以及其它内容.但是我们知道有时候仅仅阅读是不够的.有时候，我们并不在乎那些功能机制是如果运行的,我们仅仅想知道如何用AngularJS去做实现一个具体功能。

在这一章中，我么视图给出完整的样例代码,并且对这些样例代码仅仅给出少量的信息和解释，这些代码解决是我们在大多数Web应用中碰到的通用问题.这些代码没有具体的先后次序,你尽可以跳到你关心的小节先睹为快或者按着本书内容顺序阅读,选那种阅读方式由你决定.

这一章中我们将要给出代码样例包括以下这些：

1、封装一个jQuery日期选择器(DatePicker)
2、团队成员列表应用:过滤器和控制器之间的通信
3、AngularJS中的文件上传
4、使用socket.IO
5、一个简单的分页服务.
6、与服务器后端的协作

##封装一个jQuery日期选择器

这个样例代码文件可以在我们GitHub页面的chatper8/datepicker目录中找到.

在我们开始实际代码之前，我们不得不做出一个设计决策：我们的这个组件的外观显示和交互设计应该是什么样子,假设我们想定义的的日期选择器在HTML里面使用像以下代码这样:

    <input datepicker ng-model="currentDate" select="updateMyText(date)" ></input>
    
也就是说我们想修改input输入域,通过给她添加一个叫datepicker的属性,来给它添加一些更多的功能(就像这样:它的数据值绑定到一个model上,当一个日期被选择的时候,输入域能被提醒修改).那么我们如何做到这一点哪?

我们将要复用现存的功能组件:jQueryUI的datepicker(日期选择器),而不是我们从头自己构建一个日期选择器.我们只需要把它接入到AngularJS上并且理解它提供的钩子(hooks):

    angular.module('myApp.directives', [])
        .directive('datepicker', function() {
            return {
            // Enforce the angularJS default of restricting the directive to
            // attributes only
            restrict: 'A',
            // Always use along with an ng-model
            require: '?ngModel',
            scope: {
                // This method needs to be defined and
                // passed in to the directive from the view controller
                select: '&' // Bind the select function we refer to the
                            // right scope
            },
            link: function(scope, element, attrs, ngModel) {
                if (!ngModel) return;

                var optionsObj = {};
                optionsObj.dateFormat = 'mm/dd/yy';
                var updateModel = function(dateTxt) {
                    scope.$apply(function () {
                        // Call the internal AngularJS helper to
                        // update the two-way binding
                        ngModel.$setViewValue(dateTxt);
                    });
                };
                
                optionsObj.onSelect = function(dateTxt, picker) {
                    updateModel(dateTxt);
                    if (scope.select) {
                        scope.$apply(function() {
                            scope.select({date: dateTxt});
                        });
                    }
                };

                ngModel.$render = function() {
                    // Use the AngularJS internal 'binding-specific' variable
                    element.datepicker('setDate', ngModel.$viewValue || '');
                };
                element.datepicker(optionsObj);
            }
        };
    });

    上面代码中的大多数都非常简单直接,但是我们还是来看一下其中一些较重要的部分.

###ng-model

我们可以得到一个ng-model属性,这个属性的值将会被传入到指令的链接函数中.`ng-model`(这个属性对于这个指令运行是必须的,因为指令定义中的`require`属性定义--见上代码)这个属性帮助我们定义属性和绑定到`ng-model`上的(js)对象在指令的上下文中的行为机制.这儿有两点你需要注意一下:

`ngModel.$setViewValue(dateTxt)`

当Angular外部某些事件(在这个示例中就是jQueryUI日期选择器组件中某日期被选定的事件)发生时,上面这条语句会被调用.这样就可以通知AngularJS更新模型对象.这种语句一般是在某个DOM事件发生时被调用.

`ngModel.$render`

这是`ng-model`的另外一部分.这个可以协调Angular在模型对象发生变化时如何更新视图.在我们的示例中,我们仅仅给jQueryUI日期选择器传递了发生了改变的日期值.

###绑定select函数

(结合代码理解本小节内容-译者注)取代使用属性值然后用它计算成作用域对象(scope)的一个字符串属性的做法(在这个案例中，嵌套在指令内部的函和对象不是可直接操作的),我们使用了函数方法绑定("&"作用域对象绑定-注意看上面scope对象定义部分代码-译者注).这就在scope作用域对象上建立了一个叫select的新方法.这个方法函数有一个参数,参数是一个对象.这个对象中的每个键必须匹配使用了该指令HTML元素中的一个确定参数.这个键的值将会作为传递给函数的参数.这个特性添加的优势在于解耦：实现控制器时不需要知道DOM和指令的相关细节.这种回调函数仅仅根据指定参数执行他的动作,而且不需要知道绑定和刷新的的细节.

###调用select函数

注意我们给datepicker传递了一个`optionsObj`参数,这个参数对象有一个onSelect函数属性.jQueryUI组件(此处就是指datepicker)负责调用onSelect方法,这通常在AngularJS的执行上下文环境之外发生.当然,当像onSelect这样的函数被调用的时候,AngularJS不会得到通知提示.让AngularJS知道它需要对数据做一些操作是我们应用程序员的责任.我们如何来完成这个任务?通过使用`scope.$apply`.

现在我们可以很容易地在·scope.$apply·范围之外调用`$setViewValue`和`scope.select`方法,或者仅通过`scope.$apply`调用.但是前面那两步(范围之外地)的任何一步发生异常都会静悄悄地被丢弃.但是如果异常是在scope.$apply函数内部发生,就会被AngularJS捕捉.

##示例代码的其它部分

为了完成这个示例，让我看一下我们的控制器代码，然后让页面正常地跑起来:

    var app = angular.module('myApp', ['myApp.directives']);
        app.controller('MainCtrl', function($scope) {
        $scope.myText = 'Not Selected';
        $scope.currentDate = '';
        $scope.updateMyText = function(date) {
            $scope.myText = 'Selected';
        };
    });

非常简单的代码.我们声明了一个控制器，设置了一些作用于对象($scope)变量,然后创建了一个方法(`updateMyText`),这个方法后来将会被用来绑定到datepicker的`on-select`事件上.下一步补上HTML代码:

    <!DOCTYPE html>
    <html ng-app="myApp">
        <head lang="en">
            <meta charset="utf-8">
            <title>AngularJS Datepicker</title>
            <script
                src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js">
            </script>
            <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js">
            </script>
            <script
                src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.3/
                     angular.min.js">
            </script>
            <link rel="stylesheet"
                  href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
            <script src="datepicker.js"></script>
            <script src="app.js"></script>
        </head>
        <body ng-controller="MainCtrl">
            <input id="dateField"
                   datepicker
                   ng-model="$parent.currentDate"
                   select="updateMyText(date)">
            <br/>
            {{myText}} - {{currentDate}}
        </body>
    </html>
    
注意HTML元素中的select属性是如果被声明的.在作用域对象(scope)的范围内没有"date"这个值.但是因为我们已经在指令绑定过程中装配了我们的函数.AngularJS现在就知道这个函数将会有一个参数,参数名称将会是"date".这个也就是当datepicker组件的onSelect事件绑定函数被调用时我们定义的的那个对象将会传入这个参数.

对于`ng-model`,我们定义用`$parent.currentDate`取代了`currentDate`.为什么?因为我们的指令创建了一个隔离的作用域以便于做`select`函数绑定这件事.这将使得`currentDate`不再被`ng-model`所即使我们设定了它.所以我们不得不显式地告诉AngularJS:需要引用的`currentDate`变量不是在隔离作用域内，而是在父作用域内.

做到这一步,我们可以在浏览器内加载这个示例代码,你将会看到一个文本框,当你点击的时候,将会弹出一个jQueryUI日期选择器.选定日期后,显示文本"Not Selected"将会被更新为"Selected",选择日期也刷新显示,输入框内的日期也会被更新.

##"小组成员列表"应用：数据过滤与控制器之间的通信

在这个示例中,我们将同时处理很多事情,但是其中只有两个新技术点:

1、怎样使用数据过滤器--特别是已简洁的方式使用--和重复指令(ng-repeat)一起用.
2、怎样在没有共同继承关系的控制器之间通信.

这个示例应用本身非常简单.其中只有数据，这些数据是各种体育运动队的成员列表.其中包含的运动有篮球、足球(橄榄球式的不是英式足球那种)和曲棍球.对于这些运动队的成员,我们有他们的姓名、所在城市、运动种类以及所在团队是不是主力团队.

我们想做的是显示这个成员列表，在其左边显示过滤器，当过滤器数据发生变化的时候，成员列表数据做出相应的刷新.我们将要构建两个控制器.一个用来保存列表成员数据,另外一个运行过滤机制.我们将要使用一个服务来为列表控制器和过滤控制器之中的过滤数据变化做通信.

想让我们看看这个服务,它将用来驱动整个应用：

    angular.module('myApp.services', []).
        factory('filterService', function() {
            return {
                activeFilters: {},
                searchText: ''
            };
        });
        
哇哦,你也许会问：这就是全部?嗯，是的.我们此处所写代码基于这样一个事实:AngularJS 服务是单例模式的(这个以小写s打头的单例(singleton)是作用域scope内的单例,而不是那种全局可见且可读写的那种.)当你声明了一个过滤服务,我们就授权在整个应用范围内只有一个过滤服务的实例对象.

接下来，我们里完成使用过滤服务作为过滤控制器和列表控制器之间的通信渠道的其它代码.两个控制器都可以绑定到它(过滤服务)上,而且两个都可以在它(过滤服务)更新时，读取它的成员属性.这两个控制器实际上都简单得要死,因为在其中除了简单的赋值,基本没做什么别的.

    var app = angular.module('myApp', ['myApp.services']);
    app.controller('ListCtrl', function($scope, filterService) {
        $scope.filterService = filterService;
        $scope.teamsList = [{
                id: 1, name: 'Dallas Mavericks', sport: 'Basketball',
                city: 'Dallas', featured: true
            }, {
                id: 2, name: 'Dallas Cowboys', sport: 'Football',
                city: 'Dallas', featured: false
            }, {
                id: 3, name: 'New York Knicks', sport: 'Basketball',
                city: 'New York', featured: false
            }, {
                id: 4, name: 'Brooklyn Nets', sport: 'Basketball',
                city: 'New York', featured: false
            }, {
                id: 5, name: 'New York Jets', sport: 'Football',
                city: 'New York', featured: false
            }, {
                id: 6, name: 'New York Giants', sport: 'Football',
                city: 'New York', featured: true
            }, {
                id: 7, name: 'Los Angeles Lakers', sport: 'Basketball',
                city: 'Los Angeles', featured: true
            }, {
                id: 8, name: 'Los Angeles Clippers', sport: 'Basketball',
                city: 'Los Angeles', featured: false
            }, {
                id: 9, name: 'Dallas Stars', sport: 'Hockey',
                city: 'Dallas', featured: false
            }, {
                id: 10, name: 'Boston Bruins', sport: 'Hockey',
                city: 'Boston', featured: true
            }
        ];
    });
    app.controller('FilterCtrl', function($scope, filterService) {
        $scope.filterService = filterService;
    });
    
你也想知道:那部分代码会很复杂？AngularJS确实使这个示例整体非常简单,接下来我们所需要去做的就是把所有这一期在模版中整合在一起：

    <!DOCTYPE html>
    <html ng-app="myApp">
    <head lang="en">
        <meta charset="utf-8">
        <title>Teams List App</title>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js">
        </script>
        <script
            src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.3/angular.min.js">
        </script>
        <link rel="stylesheet"
            href="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.1.1/
            css/bootstrap.min.css">
        <script
            src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.1.1/
            bootstrap.min.js">
        </script>
        <script src="services.js"></script>
        <script src="app.js"></script>
    </head>
    <body>
    <div class="row-fluid">
        <div class="span3" ng-controller="FilterCtrl">
            <form class="form-horizontal">
                <div class="controls-row">
                    <label for="searchTextBox" class="control-label">Search:</label>
                    <div class="controls">
                        <input type="text"
                            id="searchTextBox"
                            ng-model="filterService.searchText">
                    </div>
                </div>
                <div class="controls-row">
                    <label for="sportComboBox" class="control-label">Sport:</label>
                    <div class="controls">
                        <select id="sportComboBox"
                            ng-model="filterService.activeFilters.sport">
                            <option ng-repeat="sport in ['Basketball', 'Hockey', 'Football']">
                                {{sport}}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="controls-row">
                    <label for="cityComboBox" class="control-label">City:</label>
                    <div class="controls">
                        <select id="cityComboBox"
                            ng-model="filterService.activeFilters.city">
                            <option ng-repeat="city in ['Dallas', 'Los Angeles',
                                                        'Boston', 'New York']">
                                {{city}}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="controls-row">
                    <label class="control-label">Featured:</label>
                    <div class="controls">
                        <input type="checkbox"
                            ng-model="filterService.activeFilters.featured"
                            ng-false-value="" />
                    </div>
                </div>
            </form>
        </div>
        <div class="offset1 span8" ng-controller="ListCtrl">
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Sport</th>
                    <th>City</th>
                    <th>Featured</th>
                </tr>
                </thead>
                <tbody id="teamListTable">
                <tr ng-repeat="team in teamsList | filter:filterService.activeFilters |
                               filter:filterService.searchText">
                    <td>{{team.name}}</td>
                    <td>{{team.sport}}</td>
                    <td>{{team.city}}</td>
                    <td>{{team.featured}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    </body>
    </html>

在整个上面这个HTML模板代码里面,真正需要关注的只有四项.除此以外的旧的东西我们到目前为止可能都看了几十遍了(甚至这些旧代码点都曾经以这样那样的形式出现过).让我们来挨个看一下那四个新代码点:

###搜索框

搜索框的值用ng-model指令绑定到了过滤服务的searchText域上(`filterService.searchText`),这个属性域本身没什么值得注意的.但是在后面他将被用在过滤器上的方式使得现在这儿这步很关键.

###组合框

这儿有两个组合框,尽管我们只高亮了第一个.但是这两个工作方式相同.他们都绑定到过滤服务(filterService)的激活过滤器域(activeFilters)的sports属性或者city属性(取决于具体组合框).这个基本设置了过滤服务(`filtersService`)的filter对象的sports属性或者city属性.

###复选框

复选框绑定到了过滤服务(`filterService`)的激活过滤器域(`activeFilters`)的`featured`属性上.这里需要注意的是如果复选框被选定.我们想显示那些`featured=true`的主力团队.如果复选框没有被选定,我们想显示`featured=true`和`feature=false`的两种团队(也就是全部团队).为了达到这个效果,我们用`ng-false-value=""`指令来告诉程序当复选框没有选定的时候,`featured`这个过滤属性将会被清掉.

###迭代器

让我们再一次看一下`ng-repeat`这条语句：

    "team in teamsList | filter:filterService.activeFilters |
    filter:filterService.searchText"

这条语句的第一部分和之前的一样.后面的两个新的过滤器则让一切变得不一样了.第一个过滤器告诉AngualrJS用`filterService.activeFilters`域过滤列表数据.使用过滤对象的每个属性来过滤数据，确保迭代器里的每个循环项的属性值与过滤对象的对应属性值相匹配.所以如果`activeFilter[city]=Dallas`,那么迭代器里面只有那些`city=Dallas`的被选择出来显示.如果有多个过滤器对象，那所有过滤器对象的属性都得匹配.

第二个过滤器是个文本值过滤器.它基本上只过滤那些只有其属性数据中出现`filterService.searchText`文本值即可.所以这个过滤的属性值会包含所有数据项：cities、team names、sports和featured.

##AngularJS中的文件上传

另外一个我们即将要看的常用情景示例是在AngularJS应用中如何实现文件上传功能.虽然目前支持这个功能可以通过HTML标准中的file类型的input输入域来做,但是为了达到这个示例的目的，我们将会扩展一个现存的文件上传解决方案.目前这方面的优秀实现之一是`BlueImp's File Upload`,它是用jQuery和JqueryUI实现(或者BootStrap)的.它的API相当简单,所以这样使得我们的AngularJS指令也超级简单.

让我们从令定义的代码开始：

    angular.module('myApp.directives', [])
        .directive('fileupload', function() {
        return {
            restrict: 'A',
            scope: {
                done: '&',
                progress: '&'
            },
            link: function(scope, element, attrs) {
                var optionsObj = {
                    dataType: 'json'
                };
                if (scope.done) {
                    optionsObj.done = function() {
                        scope.$apply(function() {
                            scope.done({e: e, data: data});
                        });
                    };
                }
                if (scope.progress) {
                    optionsObj.progress = function(e, data) {
                        scope.$apply(function() {
                            scope.progress({e: e, data: data});
                        });
                    }
                }
                // the above could easily be done in a loop, to cover
                // all API's that Fileupload provides
                element.fileupload(optionsObj);
            }
        };
    });
    
这段代码帮助我们以一个非常简单的方式定义了我们这个指令,并且添加了`onDone`和`onProgress`两个函数钩子.我们使用函数绑定来使AngualrJS调用正确的方法而且使用正确的作用域.
    
这一切都是通过隔离的作用域定义来完成,它之中有两个函数绑定:一个对应`pregress`,另外一个对应`done`.我们将在作用域对象`scope`上创建一个单参数函数.比如：`scope.done`以一个对象为参数.这个对象内有两个属性键:`e`和`data`.这些都作为参数传递给原始定义的函数.这个函数我们将会在下一小节中看到.

下面来让我们看一下我们的HTML代码来看看我们如何使用函数绑定:

    <!DOCTYPE html>
    <html ng-app="myApp">
        <head lang="en">
            <meta charset="utf-8">
            <title>File Upload with AngularJS</title>
            <!-- Because we are loading jQuery before AngularJS,
                 Angular will use the jQuery library instead of
                 its own jQueryLite implementation -->
            <script
                src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js">
            </script>
            <script
                src="http://raw.github.com/blueimp/jQuery-File-Upload/master/js/vendor/
                jquery.ui.widget.js">
            </script>
            <script
                src="http://raw.github.com/blueimp/jQuery-File-Upload/master/js/
                jquery.iframe-transport.js">
            </script>
            <script
                src="http://raw.github.com/blueimp/jQuery-File-Upload/master/js/
                jquery.fileupload.js">
            </script>
            <script
                src="//ajax.googleapis.com/ajax/libs/angularjs/1.0.3/angular.min.js">
            </script>
            <script src="app.js"></script>
        </head>
        <body ng-controller="MainCtrl">
            File Upload:
                <!-- We will define uploadFinished in MainCtrl and attach
                     it to the scope, so that it is available here -->
                <input id="testUpload"
                       type="file"
                       fileupload
                       name="files[]"
                       data-url="/server/uploadFile"
                       multiple
                       done="uploadFinished(e, data)">
        </body>
    </html>

我们的input标签仅仅添加了以下附加部分:

`fileupload`
    这个使得input标签成为一个文件上传元素

`data-url`
    这个属性被FileUpload插件用来确定文件上传的服务器端处理URL.在我们的示例中,我们假设在`/server/uploadFile`URL上有一个服务器端API在监听处理上传的文件数据.
    
`multiple`
    这个multiple属性告诉指令(以及fileupload组件)允许它一次性可以选择多个文件.我们可以通过插件轻松实现此功能，而不需要多写一行额外代码，这又是内建插件的一个福利啊。

`done`
    这是当插件文件上传结束以后AngularJS要调用的函数.如果我们想做，我们也可以以类似的方式为progress事件也定义一个函数.这也指定了我们的指令定义中调用的那两个参数函数.
    
那控制器看起来会是个什么样子那,正如你所期望的那样,它的代码是下面这样：

    var app = angular.module('myApp', ['myApp.directives']);
        app.controller('MainCtrl', function($scope) {
        $scope.uploadFinished = function(e, data) {
            console.log('We just finished uploading this baby...');
        };
    });
有了上面这些代码,我们就有了一个简单的、可运行且可复用的文件上传指令.

##使用Socket.IO

目前Web开发中一个常见需求就是构建实时Web应用,也就是服务器端数据一更新,前端浏览器的数据也立即实时刷新.之前使用的技巧如轮询之类的被发现有缺陷,有时我们仅仅想建立一个连接前端的套接字(socket)用来通信.

`Socket.IO`是一个优秀的库.它可以帮我们通过非常简单的基于事件API构建实时Web应用.下面我们将要开发一个实时的、匿名的消息广播系统(比如Twitter,不过不需要用户名),这个系统将帮助用户把自己的消息广播给所有的Socket.IO用户同时还可以看见系统的所有消息.这个系统中没有数据会被持久化存储,所以所有的消息只对那些在线活跃用户是可见的,但是这系统用于说明Socket.IO如何被优雅地集成进AngularJS这件事已经绰绰有余.

说干就干,我们来把Socket.IO封装到一个AngularJS服务里.这样做，我们就可以保证以下几点:

* Socket.IO的事件会在AngularJS的生命周期被激发和处理
* 测试集成效果将变得很简单

    var app = angular.module('myApp', []);
    // We define the socket service as a factory so that it
    // is instantiated only once, and thus acts as a singleton
    // for the scope of the application.
    app.factory('socket', function ($rootScope) {
        var socket = io.connect('http://localhost:8080');
        return {
            on: function (eventName, callback) {
                socket.on(eventName, function () {
                    var args = arguments;
                    $rootScope.$apply(function () {
                        callback.apply(socket, args);
                    });
                });
            },
            emit: function (eventName, data, callback) {
                socket.emit(eventName, data, function () {
                    var args = arguments;
                    $rootScope.$apply(function () {
                        if (callback) {
                            callback.apply(socket, args);
                        }
                    });
                })
            }
        };
    });

此处我们只封装了我们关注的两个函数,他们分别是Socket.IO API中的on事件和broadcast事件方法.API中还有很多事件方法,我们可以以类似的方式封装他们.

下面我们来看一下简单的`index.html`文件源码,将展示一个带发送按钮的文本框和一个消息列表.在这个示例中,我们并不跟踪谁发的消息以及什么时候发的.

    <!DOCTYPE html>
    <html ng-app="myApp">
    <head lang="en">
        <meta charset="utf-8">
        <title>Anonymous Broadcaster</title>
        <script src="/socket.io/socket.io.js">
        </script>
        <script
            src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.3/angular.min.js">
        </script>
        <script src="app.js"></script>
    </head>
    <body ng-controller="MainCtrl">
        <input type="text" ng-model="message">
        <button ng-click="broadcast()">Broadcast</button>
        <ul>
            <li ng-repeat="msg in messages">{{msg}}</li>
        </ul>
    </body>
    </html>

下面我们看一下`MainCtrl`控制器(这段代码是app.js中的一部分),在这个控制器中我们将把上面这些整合起来:

    function MainCtrl($scope, socket) {
        $scope.message = '';
        $scope.messages = [];
        // When we see a new msg event from the server
        socket.on('new:msg', function (message) {
            $scope.messages.push(message);
        });
        // Tell the server there is a new message
        $scope.broadcast = function() {
            socket.emit('broadcast:msg', {message: $scope.message});
            $scope.messages.push($scope.message);
            $scope.message = '';
        };
    }
    
这个控制器本身非常简单.它监听套接字连接的事件,而且一旦用户点击广播按钮,就让服务知道有新消息了.并且把新消息添加到消息列表把它直接显示给当前用户.

下面我们来完成最后一部分.这是NodeJS Server如何支撑前端应用的代码,它相应地用Socket.IOAPI建立了服务器端.

    var app = require('express')()
        , server = require('http').createServer(app)
        , io = require('socket.io').listen(server);
    server.listen(8080);
    app.get('/', function (req, res) {
        res.sendfile(__dirname + '/index.html');
    });
    app.get('/app.js', function(req, res) {
        res.sendfile(__dirname + '/app.js');
    });
    io.sockets.on('connection', function (socket) {
        socket.emit('new:msg', 'Welcome to AnonBoard');
        socket.on('broadcast:msg', function(data) {
            // Tell all the other clients (except self) about the new message
            socket.broadcast.emit('new:msg', data.message);
        });
    });
    
以后你可以轻松地扩展这段代码以支持处理更多的消息和更复杂的结构,尽管如此,这段代码已经打好了基础,在其之上,你可以实现客户端浏览器和服务器端之间的套接字连接.

整个示例应用非常简单.它没有做任何数据验证(不管消息是否为空),但是它包含AngularJS默认提供的HTML代码清理过滤功能.它没有处理复杂的消息,但是它提供了一个将Socket.IO集成进AngularJS的完全可用的端到端实现.你可以马上就基于它建立你自己的工作生产代码.

##一个简单的分页服务

大多数Web应用中一个常用的功能情景是显示一个项目列表.通常,我们有着比一个单个页面合理显示量更大的数据量.这样一个需求场景下,我们想以分页的方式显示这些数据，而且用户还可以在不同的页面之间穿梭.因为这一个在所有Web应用之中很常见的场景,所以有理由把这个功能抽取出来封装成一个公共可复用的分页服务.

我们的分页服务(一个非常简单的实现)将帮助该服务的用户在给定的数据偏移量、单页数据量、数据总量条件下取得分页数据.它在内部将处理以下逻辑:某一页要取那些数据，如何下一页存在的情况下，下一页是那页等等必须功能逻辑.

这个服务可以进一步扩展来在服务类缓存数据项,但是这个就作为练习题留给广大读者了.我们的示例全部所需要的就是把当前页数据项`currentPageItems`存储在缓存里.在它可用的情况下取出它，就相当于别的取数据函数`fetch Function`那一类的东西.

下面我们看一下这个服务的实现:

    angular.module(‘services’, []).factory('Paginator', function() {
        // Despite being a factory, the user of the service gets a new
        // Paginator every time he calls the service. This is because
        // we return a function that provides an object when executed
        return function(fetchFunction, pageSize) {
            var paginator = {
                hasNextVar: false,
                next: function() {
                    if (this.hasNextVar) {
                        this.currentOffset += pageSize;
                        this._load();
                    }
                },
                _load: function() {
                    var self = this;
                    fetchFunction(this.currentOffset, pageSize + 1, function(items) {
                        self.currentPageItems = items.slice(0, pageSize);
                        self.hasNextVar = items.length === pageSize + 1;
                    });
                },
                hasNext: function() {
                    return this.hasNextVar;
                },
                previous: function() {
                    if(this.hasPrevious()) {
                        this.currentOffset -= pageSize;
                        this._load();
                    }
                },
                hasPrevious: function() {
                    return this.currentOffset !== 0;
                },
                currentPageItems: [],
                currentOffset: 0
            };
            // Load the first page
            paginator._load();
            return paginator;
        };
    });

分页服务被调用的时候需要两个参数：一个是`fetch`取数据的函数,还有一个就是每页的大小.取数据的函数希望是如下这个函数签名：

    fetchFunction(offset, limit, callback);

一旦这个函数需要取得数来显示一个页面,它就会给以正确的数据偏移量、单页数据大小两个参数而被分页服务调用.在这个函数的内部,它可以或者从一个大的数据数据中做切片或者想服务器端发出请求取回数据.一旦数据可用,取数(`fetch`)函数就需要调用那个作为参数的回调函数.

让我们看一下啊这个函数的设计说明,将要澄清说明我们在有一个包含太多返回数据项的大数组的前提下如何使用这个函数。请注意：这是一个单元测试.由于其实现方式的原因，我们可以在不需要任何控制器和XHR异步请求的情况下测试这个服务.

    describe('Paginator Service', function() {
        beforeEach(module('services'));
        var paginator;
        var items = [1, 2, 3, 4, 5, 6];
        var fetchFn = function(offset, limit, callback) {
            callback(items.slice(offset, offset + limit));
        };
        beforeEach(inject(function(Paginator) {
            paginator = Paginator(fetchFn, 3);
        }));
        it('should show items on the first page', function() {
            expect(paginator.currentPageItems).toEqual([1, 2, 3]);
            expect(paginator.hasNext()).toBeTruthy();
            expect(paginator.hasPrevious()).toBeFalsy();
        });
        it('should go to the next page', function() {
            paginator.next();
            expect(paginator.currentPageItems).toEqual([4, 5, 6]);
            expect(paginator.hasNext()).toBeFalsy();
            expect(paginator.hasPrevious()).toBeTruthy();
        });
        it('should go to previous page', function() {
            paginator.next();
            expect(paginator.currentPageItems).toEqual([4, 5, 6]);
            paginator.previous();
            expect(paginator.currentPageItems).toEqual([1, 2, 3]);
        });
        it('should not go next from last page', function() {
            paginator.next();
            expect(paginator.currentPageItems).toEqual([4, 5, 6]);
            paginator.next();
            expect(paginator.currentPageItems).toEqual([4, 5, 6]);
        });
        it('should not go back from first page', function() {
            paginator.previous();
            expect(paginator.currentPageItems).toEqual([1, 2, 3]);
        });
    });

分页服务暴露其自身的`currentPageItems`当前分页数据项这个变量,这样它就可以在模板中被迭代器绑定(或者其它想显示这些数据项的地方).`hasNext()`和`hsrPreviour`两个函数可已被用来确定是否显示下一页或者上一页这两个链接.而在click事件上，我们只需要分别调用`next()`或者`previous()`这两个函数.

那么在我们需要从服务器端取回每页数据的条件下这个服务应该如何使用哪？这儿有这么一个控制器:它每显示一页数据都需要从服务器端取回一次搜索结果数据.大概代码如下:

    var app = angular.module('myApp', ['myApp.services']);
    app.controller('MainCtrl', ['$scope', '$http', 'Paginator',
        function($scope, $http, Paginator) {
        $scope.query = 'Testing';
        var fetchFunction = function(offset, limit, callback) {
            $http.get('/search',
                {params: {query: $scope.query, offset: offset, limit: limit}})
                .success(callback);
            };
        $scope.searchPaginator = Paginator(fetchFunction, 10);
    }]);

使用这个分页服务的HTML页面代码数据如下:

    <!DOCTYPE html>
    <html ng-app="myApp">
    <head lang="en">
        <meta charset="utf-8">
        <title>Pagination Service</title>
        <script
            src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.3/angular.min.js">
        </script>
        <script src="pagination.js"></script>
        <script src="app.js"></script>
    </head>
    <body ng-controller="MainCtrl">
        <input type="text" ng-model="query">
        <ul>
            <li ng-repeat="item in searchPaginator.currentPageItems">
                {{item}}
            </li>
        </ul>
        <a href=""
            ng-click="searchPaginator.previous()"
            ng-show="searchPaginator.hasPrevious()">&lt;&lt; Prev</a>
        <a href=""
            ng-click="searchPaginator.next()"
            ng-show="searchPaginator.hasNext()">Next &gt;&gt;</a>
    </body>
    </html>

##和服务器之间的协作与登录

最后一个案例将要覆盖众多的场景,它们中的全部或者大多数都与`$http`资源有联系.在我们的经验中,`$http`服务是AngularJS核心服务之一.同时它可以被扩展来满足Web应用的很多常见功能需求,包括:

* 共享一个公共错误处理代码点
* 处理认证和登录之后的重定向
* 与不支持或者支持JSON通信的服务器协作.
* 通过JSONP与外部服务(非同域的)之间的通信

所以在这个(轻度设计)的示例中,我们将会有一个成熟WebApp的骨架,它将会包括如下:

1.在`butterbar`指令显示所有不可恢复的错误(不包括验证失败HTTP401响应),只有异常发生的时候，这个指令才会在屏幕上出现.
2.将会有一个`ErrorService`,它将会被用来在指令、视图和控制器之间的通信工作.
3.在服务器端响应401验证失败时激发一个事件(事件`loginRequired`).它将会被覆盖整个应用的根控制器所处理.
4.处理那些需要带验证头信息的服务器请求,这些请求是特定于当前用户的.

我们不会覆盖整个应用的所有元素(比如路由、模板等等),而且大多数代码是简明易懂的.我们只高亮显示那些与主题关系较密切的代码(便于您把这些代码复制粘帖到您的代码库中并以正确的方式开始编码).这些代码将会是完全功能性代码.如果你想看定义服务或者工厂的代码,请参阅第7章.如果你想看如何与服务器端协同合作,可以参考第5章.

首先让我看回一下Error服务的代码:

    var servicesModule = angular.module('myApp.services', []);
    servicesModule.factory('errorService', function() {
        return {
            errorMessage: null,
            setError: function(msg) {
                this.errorMessage = msg;
            },
            clear: function() {
                this.errorMessage = null;
            }
        };
    });
    
我们的`error message`错误消息指令,它实际上与Error服务是独立的,它指挥寻找一个弹出框的消息属性，然后把它绑定到模板中.只有错误消息出现的情况下，弹出框才会显示.

    // USAGE: <div alert-bar alertMessage="myMessageVar"></div>
    angular.module('myApp.directives', []).
    directive('alertBar', ['$parse', function($parse) {
        return {
            restrict: 'A',
            template: '<div class="alert alert-error alert-bar"' +
                'ng-show="errorMessage">' +
                '<button type="button" class="close" ng-click="hideAlert()">' +
                'x</button>' +
                '{{errorMessage}}</div>',
            link: function(scope, elem, attrs) {
                var alertMessageAttr = attrs['alertmessage'];
                scope.errorMessage = null;
                scope.$watch(alertMessageAttr, function(newVal) {
                    scope.errorMessage = newVal;
                });
                scope.hideAlert = function() {
                    scope.errorMessage = null;
                    // Also clear the error message on the bound variable.
                    // Do this so that if the same error happens again
                    // the alert bar will be shown again next time.
                    $parse(alertMessageAttr).assign(scope, null);
                };
            }
        };
    }]);

我们添加进HTML的弹出框代码将如下所示：

    <div alert-bar alertmessage="errorService.errorMessage"></div>
    
我们需要保证在上面这段HTML被新增前,`ErrorSerivce`必须以"errorService"属性名保存在作用域对象范围之内.也就是说:如果`RootController`是负责拥有`AlertBar`指令的控制器,那么代码应如下:

    app.controller('RootController',
                   ['$scope', 'ErrorService', function($scope, ErrorService) {
        $scope.errorService = ErrorService;
    });

它给我们一个像样的框架来显示或隐藏错误信息和提示框.现在让我看看，如何利用拦截器来处理服务器端可能抛给我们的各种状态码:

    servicesModule.config(function ($httpProvider) {
        $httpProvider.responseInterceptors.push('errorHttpInterceptor');
    });
    // register the interceptor as a service
    // intercepts ALL angular ajax HTTP calls
    servicesModule.factory('errorHttpInterceptor',
            function ($q, $location, ErrorService, $rootScope) {
        return function (promise) {
            return promise.then(function (response) {
                return response;
            }, function (response) {
                if (response.status === 401) {
                    $rootScope.$broadcast('event:loginRequired');
                } else if (response.status >= 400 && response.status < 500) {
                    ErrorService.setError('Server was unable to find' +
                        ' what you were looking for... Sorry!!');
                }
                return $q.reject(response);
            });
        };
    });
    
对于某些地方一些控制器来说，所有需要做的就是注册监听`loginRequired`事件,然后重定向到登录页面(或者做相对更复杂的效果,比如显示一个登录模态对话框).

    $scope.$on('event:loginRequired', function() {
        $location.path('/login');
    });

剩下的就是处理需要认证授权的Web请求了.我们目前只说所有需要认证授权的Web请求都有一个"Authorization"报头,这个报头的的值对于每一个当前登录用户是特定的.因为这个报头值每次登录都会改变,所以我们不能用默认的`transformRequests`,因为它的数据改变是在`config`级.取而代之的是，我们将会封装`$http`服务,从而构建我们自己的`AuthService`.

我们也会有一个认证服务,他负责存储用户的认证信息(在你需要的时候读取它,通常是在登录过程中发生).`AuthHttp`服务将会访问这个认证服务并通过添加必要的报头来给Web请求授权.

    // This factory is only evaluated once, and authHttp is memorized. That is,
    // future requests to authHttp service return the same instance of authHttp
    servicesModule.factory('authHttp', function($http, Authentication) {
        var authHttp = {};
        // Append the right header to the request
        var extendHeaders = function(config) {
            config.headers = config.headers || {};
            config.headers['Authorization'] = Authentication.getTokenType() +
                ' ' + Authentication.getAccessToken();
        };
        // Do this for each $http call
        angular.forEach(['get', 'delete', 'head', 'jsonp'], function(name) {
            authHttp[name] = function(url, config) {
                config = config || {};
                extendHeaders(config);
                return $http[name](url, config);
            };
        });
        angular.forEach(['post', 'put'], function(name) {
            authHttp[name] = function(url, data, config) {
                config = config || {};
                extendHeaders(config);
                return $http[name](url, data, config);
            };
        });
        return authHttp;
    });
    
任何需要授权的请求其请求发起函数将会用`authHttp.get()`取代`$http.get()`.只要Authentication服务的被设定是正确的信息,你的每次Web请求调用都会快捷如飞地通过认证.因为我们用一个服务来做授权这个事情,所以其信息对于整个Web应用来说都是可用的,也就不需要每次路由改变的时候都不得不去读取验证信息.

这已经覆盖了我们在这个Web应用中需要的所有细节.你可以直接从这儿拷贝代码到你自己的应用代码中,让它为你工作.祝你好运.

##总结

当带着我们到这本书末尾的时候，我们几乎接近覆盖了关于AngularJS的所有内容.写这本书我们的目标就是给大家提供一个坚实的基础,在这个基础之上我们能开始我们的探索并且愉快地使用AngularJS做开发.我们覆盖了所有的基础知识(和一些高级话题),并且沿途提供了尽可能多的示例.

大功告成，一切都做完了吗？不,我们还需要花大功夫去学习AngularJS外在功能之下的内在运行机制.比如我们的内容从没有涉及过如何构建复杂且相互依赖的指令.还有那么多的内容没有提及,我们用三本甚至四本书来讲可能都不够.但是我们希望这本书能够给你信心去处理碰到的更复杂的需求.

我们花了大量的时间来写这本书,所以希望能够在Internet上看到一些用AngularJS实现的令人惊艳的应用.
