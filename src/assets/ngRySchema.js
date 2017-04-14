(function( window, angular, $, undefined ){
	"use strict";
	
	$("script[type='application/ld+json']").each(function(){
        var str = $(this).text();
        var json = JSON.parse(str);
    });

    $.fn.serializeObject = function()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
	
	(function(){
		"use strict";
		
		var Balita = function(_theme) {
			this.theme = _theme;
		};
		
		angular.module('ngRySchema', ["ngMaterial", "ngRyRoute"])
		.provider('$rySchema', function $rySchemaProvider(){
			var theme = "/vendor/ryfacebook/elements/";
			
			this.setTheme = function(_theme) {
				theme = _theme;
			};
			
			this.$get = function(){
				return new Balita(theme);
			};
		})
		.directive('x6link', ["$mdDialog", "RyRouter", function ($mdDialog, RyRouter) {
            return {
                restrict: 'C',
                compile: function (element, attrs) {
                    return function (scope, element) {
                        element.on('click', function (event) {
                            event.preventDefault();
                            var params = angular.extend({
                                modal: "false"
                            }, attrs);
                            var url = $(this).attr("href") + (/\?/.test($(this).attr("href"))?'&mdDialog=true':'?mdDialog=true') + "&t=" + Date.now();
                            $mdDialog.show({
                                controller: RyRouter.controller(),
                                templateUrl: url,
                                parent: angular.element(document.body),
                                targetEvent: event,
                                closeTo: "center",
                                clickOutsideToClose: !(params.modal == "true")
                            })
                                .then(function (answer) {
                                    scope.status = 'You said the information was "' + answer + '".';
                                }, function () {
                                    scope.status = 'You cancelled the dialog.';
                                });
                        });
                    }
                }
            }
        }]).directive('itemtype', ["$templateRequest", "$compile", "$rySchema", function($templateRequest, $compile, $rySchemaProvider){
            return {
                restrict: "A",
                link: function(scope, element, attrs, ctrl){
                    if(attrs.itemtype=="http://schema.org/BreadcrumbList") {
                        var req = $templateRequest($rySchemaProvider.theme + "X6Breadcrumb.html").then(function (data) {
                            var template = angular.element(data);
                            element.replaceWith(template);
                            $compile(template)(scope);
                        });
                    }

                    if(attrs.itemtype=="http://schema.org/WebSite") {
                        var req = $templateRequest($rySchemaProvider.theme + "X6Breadcrumb.html").then(function (data) {
                            var template = angular.element(data);
                            element.replaceWith(template);
                            $compile(template)(scope);
                        });
                    }
                }
            }
        }])
        .directive('x6textfield', ["$templateRequest", "$compile", "$rySchema", function ($templateRequest, $compile, $rySchemaProvider) {
            return {
                restrict: "E",
                require: 'ngModel',
                scope: {
                    ngModel: '='
                },
                link: function (scope, element, attrs, ctrl) {
                    scope.hint = attrs.hint;
                    scope.name = attrs.ngModel;
                    scope.ngModel = attrs.text;
                    scope.type = "text";
                    scope.ready = true;
                    scope.showerror = false;
                    if (attrs.secure)
                        scope.type = "password";
                    scope.validations = {};
                    scope.errors = {};
                    $(element).find("error").each(function (k, item) {
                        var callback = $(item).attr("valid");
                        scope.errors[callback] = true;
                        scope.validations[callback] = $(item).html();
                    });

                    ctrl.$parsers.unshift(function (viewvalue) {
                        var checked = true;
                        scope.showerror = true;
                        $(element).find("error").each(function (k, item) {
                            var callback = $(item).attr("valid");
                            var bodyScope = angular.element($("body")).scope();
                            if (typeof(bodyScope[callback]) == 'function') {
                                var check = bodyScope[callback].apply(bodyScope, [viewvalue]);
                                scope.errors[callback] = !check;
                                checked &= check;
                                ctrl.$setValidity(callback, check);
                            }
                        });
                        return checked ? viewvalue : undefined;
                    });

                    ctrl.$formatters.unshift(function (viewvalue) {
                        if (viewvalue)
                            scope.showerror = true;
                        $(element).find("error").each(function (k, item) {
                            var callback = $(item).attr("valid");
                            var bodyScope = angular.element($("body")).scope();
                            if (typeof(bodyScope[callback]) == 'function') {
                                var check = bodyScope[callback].apply(bodyScope, [viewvalue]);
                                scope.errors[callback] = !check;
                                ctrl.$setValidity(callback, check);
                            }
                        });
                        return viewvalue;
                    });

                    var req = $templateRequest($rySchemaProvider.theme + "X6TextField.html").then(function (data) {
                        var template = angular.element(data);
                        element.replaceWith(template);
                        $compile(template)(scope);
                    });
                }
            };
        }])
        .directive('x6submit', ["$http", "$mdDialog", "RyRouter", "$rySchema", function ($http, $mdDialog, RyRouter, $rySchemaProvider) {
            return {
                restrict: 'E',
                transclude: true,
                require: '^^form',
                link: function (scope, element, attrs, form) {
                    scope.title = "Login";
                    scope.$watch(form.$name + ".$invalid", function (newvalue, oldvalue) {
                        scope.ngDisabled = newvalue;
                    });
                    $(element).on("click", function(e){
                        e.preventDefault();

                        var form = $(element).parents("form");

                        $http({
                            url : form.attr("action"),
                            method : form.attr("method"),
                            data : form.serializeObject()
                        }).then(function(success){
                            $mdDialog.show({
                                controller: RyRouter.controller(),
                                parent: angular.element(document.body),
                                template : success.data,
                                clickOutsideToClose: true,
                                openFrom: "center"
                            });
                        }, function(error){

                        });

                        return false;
                    });
                },
                templateUrl: $rySchemaProvider.theme + "Button.html"
            }
        }])
        .directive('x6button', ["$mdDialog", "RyRouter", "$rySchema", function ($mdDialog, RyRouter, $rySchemaProvider) {
            return {
                restrict: 'AEC',
                transclude: true,
                link: function (scope, element, attrs) {
                    scope.href = attrs.href;
                    var params = angular.extend({
                        modal: "true"
                    }, attrs);
                    element.on("click", function (ev) {
                        $mdDialog.show({
                            controller: RyRouter.controller(),
                            templateUrl: attrs.href,
                            parent: angular.element(document.body),
                            targetEvent: ev,
                            clickOutsideToClose: !(params.modal == "true")
                        })
                            .then(function (answer) {
                                scope.status = 'You said the information was "' + answer + '".';
                            }, function () {
                                scope.status = 'You cancelled the dialog.';
                            });
                    });
                },
                templateUrl: $rySchemaProvider.theme + "X6Button.html"
            }
        }]).directive('content', function () {
            return {
                restrict: 'C',
                transclude: true,
                template: '<body><div class="container" ng-transclude></div></body>'
            }
        })
        .directive('nav', ["$templateRequest", "$compile", "$rySchema", function ($templateRequest, $compile, $rySchemaProvider) {
            return {
                link: function (scope, element, attrs) {
                    if(attrs.class)
                        return;

                    scope.title = $("h1").html();
                    scope.back = '<img src="/medias/sky2016/img/logo.png"/>';
                    scope.menus = [];
                    $(element).find("ul li a").each(function (k, item) {
                        scope.menus.push({
                            icon: $(item).find("img").attr("src"),
                            title: $(item).text(),
                            link: $(item).attr("href")
                        });
                    });
                    scope.advancedmenu = [];
                    $(element).find("ol").each(function(k, item){
                        var amenu = [];
                        $(item).find("li a").each(function(k2, item2){
                            amenu.push({
                                link : $(item2).attr("href"),
                                icon: $(item2).attr("rel"),
                                title: $(item2).text()
                            })
                        });
                        scope.advancedmenu.push(amenu);
                    });
                    $templateRequest($rySchemaProvider.theme + "X6Nav.html").then(function (data) {
                        var template = angular.element(data);
                        element.replaceWith(template);
                        $compile(template)(scope);

                        $(document).ready(function(){
                            $("md-toolbar").scroll(function(){
                                //console.log($("md-toolbar").scrollTop());
                            });
                        });
                    });
                    if(scope.advancedmenu.length>0) {
                        $templateRequest($rySchemaProvider.theme + "X6Menu.html").then(function (data) {
                            var template = angular.element(data);
                            $("body").append(template);
                            $compile(template)(scope);
                        });
                    }
                }
            }
        }]).directive('x5form', ["RyRouter", function (RyRouter) {
            return {
                restrict: 'E',
                transclude: true,
                link: RyRouter.controller(),
                template: '<div class="col-lg-4"></div><form action="" role="form" class="col-lg-4 frmSearch"><div class="form-group col-lg-12"><ng-transclude></ng-transclude></div></form><div class="col-lg-4"></div><div class="clearfix"></div>'
            }
        }])    
        .directive('section', ["$templateRequest", "$compile", "$rySchema", function ($templateRequest, $compile, $rySchemaProvider) {
            return {
                restrict: 'E',
                link: function (scope, element, attrs) {
                    scope.list = [];
                    if($(element).find("article").length > 1) {
                        $(element).find("article").each(function (k, item) {
                            scope.list.push({
                                img: $(item).find("img").attr("src"),
                                name: $(item).find("h2").text(),
                                currency: $(item).find("[itemprop='priceCurrency']").attr("content"),
                                price: $(item).find("[itemprop='price']").text(),
                                link: $(item).find("[itemprop='url']").attr("href")
                            })
                        });
                        var req = $templateRequest($rySchemaProvider.theme + "X6Tiles.html").then(function (data) {
                            var template = angular.element(data);
                            element.replaceWith(template);
                            $compile(template)(scope);
                        });
                    }
                    else {
                        $(element).find("article").each(function (k, item) {
                            var images = [];
                            $(item).find("[itemtype='https://schema.org/image']").each(function(){
                                images.push($(this).attr("src"));
                            });
                            images = $.unique(images);
                            var names = $(item).find("h1").text().split(" - ");
                            scope.list.push({
                                img: $(item).find("img").attr("src"),
                                name: $(item).find("h1").text(),
                                names: {
                                    a : names.length == 3 ? names[0] : '',
                                    b: names.length == 3 ? names[1] : names.length == 2 ? names[0] : names.length == 1 ? names[0] : '',
                                    c : names.length == 3 ? names[2] : names.length == 2 ? names[1] : ''
                                },
                                currency: $(item).find("[itemprop='priceCurrency']").attr("content"),
                                promo: parseFloat($(item).find("[rel='price_old']").text()) > parseFloat($(item).find("[itemprop='price']").text().replace(",",".")),
                                price: $(item).find("[itemprop='price']").text(),
                                link: $(item).find("a[rel='detail']").attr("href"),
                                deep_link: $(item).find("a[rel='deeplink']").attr("href"),
                                price_old: $(item).find("[rel='price_old']").text(),
                                description: $(item).find("[itemprop='description']").html(),
                                images: images
                            })
                        });
                        var req = $templateRequest($rySchemaProvider.theme + "X6Thing.html").then(function (data) {
                            var template = angular.element(data);
                            element.replaceWith(template);
                            $compile(template)(scope);
                        });
                    }
                }
            }
        }]);
		
	})();
	
})(window, window.angular, $);;
window.rySchema={version:{full: "1.0.0"}};
