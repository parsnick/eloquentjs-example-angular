<!DOCTYPE html>
<html>
<head>
    <title>EloquentJs + Angular example</title>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.1.8/semantic.min.css">
    <style>
        body {
            padding-top: 4em;
        }
    </style>
</head>
<body ng-app="eloquentjs">

    <div class="ui text container">
        <div ng-view></div>
    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.1.8/semantic.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0/angular-route.min.js"></script>

    <script src="/eloquent.js"></script>

    <script>
    angular
        .module('eloquentjs', ['ngRoute'])
        .config(function($routeProvider) {

            $routeProvider
                .when('/', {
                    templateUrl: "list.html",
                    resolve: {
                        posts: function () { return Eloquent.Post.orderBy('published_at', 'desc').get() }
                    },
                    controller: function (posts) {
                        this.posts = posts;
                    },
                    controllerAs: 'vm'
                })
                .when('/create', {
                    templateUrl: "create.html",
                    controller: function ($scope, $location) {
                        $scope.post = new Eloquent.Post;
                        $scope.submit = function (newPost) {

                            $scope.post = new Eloquent.Post;

                            newPost.save().then(function () {
                                $location.path('/');
                                $scope.$apply();
                            });

                        }
                    }
                })
                .when('/:id', {
                    templateUrl: "view.html",
                    resolve: {
                        post: function ($route) {
                            return Eloquent.Post.with('comments').find($route.current.params.id);
                        }
                    },
                    controller: function ($scope, post) {

                        $scope.post = post;

                        $scope.resetComment = function () {
                            $scope.newComment = new Eloquent.Comment({ post_id: post.getKey() });
                        };

                        $scope.resetComment();

                        $scope.addReply = function (comment) {

                            $scope.resetComment();

                            comment.save().then(function () {
                                $scope.post.comments.push(comment);
                                $scope.$apply();
                            });

                        }
                    }
                })
            ;
        })
    ;
    </script>

<script type="text/ng-template" id="list.html">

    <a href="#/create" class="ui right floated primary button">
        <i class="pencil icon"></i>
        New post
    </a>

    <h1>Posts</h1>

    <div class="ui vertical divided segment" ng-repeat="post in vm.posts">
                <span class="ui top right attached label" ng-if="post.published_at">
                    Published
                    {{ post.published_at | date: 'short' }}
                </span>
        <a href="#/{{ post.id }}" class="ui header">
            {{ post.title }}
        </a>
    </div>

</script>

<script type="text/ng-template" id="create.html">

    <h1>Write New Post</h1>

    <form class="ui form" ng-submit="submit(post)">
        <div class="field">
            <label>Title</label>
            <input type="text" name="title" placeholder="Title" required ng-model="post.title">
        </div>
        <div class="field">
            <label>Body</label>
            <textarea name="body" rows="3" placeholder="Body" required ng-model="post.body"></textarea>
        </div>
        <button class="ui primary button" type="submit">Submit</button>
    </form>

    <div class="ui center aligned basic segment">
        <a href="#/" class="ui button">
            <i class="left angle icon"></i>
            Cancel
        </a>
    </div>

</script>

<script type="text/ng-template" id="view.html">

    <h1 class="ui header">
        {{ post.title }}
        <div class="sub header">{{ post.published_at | date: 'short' }}</div>
    </h1>

    <div class="ui inverted segment">
        {{ post.body }}
    </div>

    <h3 ng-if="post.comments.length">Comments</h3>
    <div class="ui celled list">
        <div class="item" ng-repeat="comment in post.comments">
            {{ comment.body }}
        </div>
    </div>

    <br>

    <form class="ui form" ng-submit="addReply(newComment)">
        <div class="field">
            <textarea name="body" rows="2" placeholder="Leave a comment" required ng-model="newComment.body"></textarea>
        </div>
        <div class="field">
            <button class="ui secondary button" type="submit">
                <i class="plus icon"></i>
                Reply
            </button>
        </div>
    </form>

    <div class="ui center aligned basic segment">
        <a href="#/" class="ui button">
            <i class="left angle icon"></i>
            Back
        </a>
    </div>

</script>

</body>
</html>
