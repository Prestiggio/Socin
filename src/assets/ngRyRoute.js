'use script'

String.prototype.toCamelCase = function () {
    return this.replace(/^(.)/, function ($1) {
        return $1.toUpperCase();
    });
};

(function( window, angular, undefined ){
	"use strict";
	
	(function(){
		"use strict";
		
		var RyRoute = {
			locationController : function() {
				var slugs = document.location.pathname.split(/[^\w]/i);
				slugs.push("controller");
				var newslugs = [];
				for (var islug in slugs) {
				    if (slugs[islug].length == 0 || !slugs[islug])
				        continue;
				    newslugs.push(slugs[islug].toCamelCase());
				}
				return newslugs.join("");
			},
		    Controller: function ($scope) {
		        
		    }
		};
		
		var Balita = function(routes){
			this.routes = routes;
		};
		
		angular.module('ngRyRoute', ["ngRoute"])
    	.provider("$ngRyRouteSetup", ["$routeProvider", "$locationProvider", function $ngRyRouteSetupProvider($routeProvider, $locationProvider){
    		this.routes = {};
    		this.controllers = {};
    		
    		this.route = function(url, wsurl){
    			this.routes[url] = wsurl;
    		};
    		
    		this.setController = function(url, controller){
    			this.controllers[url] = controller;
    		};
    		
    		this.setRoutes = function(froutes) {
    			this.routes = froutes;
    			
    			for(var u in this.routes) {
    				if(u=="default")
    					$routeProvider.otherwise({redirectTo: this.routes.default});
    				else {
    					var params = {
        		    	        templateUrl : this.routes[u]
        		    	    };
    					if(this.controllers[u]) {
    						params.controller = this.controllers[u];
    					}
    					$routeProvider.when(u, params);
    				}
    			}    	    
        	    $locationProvider.html5Mode({
        	    	enabled: true,
        	    	requireBase: false
        	    });
    		};
    		
    		var dis = this;
    		
    		this.$get = function(){
    			return new Balita(dis.routes);
    		};
    	}])
    	.service("RyRouter", ["$ngRyRouteSetup", function($ngRyRouteSetupProvider){
    		return {
    			controller : function(){
    				var c = $ngRyRouteSetupProvider.routes[RyRoute.locationController()];
    	    	     if(c) {
    	    	      	c.$inject = ["$scope", "facebook"];
    	    	      	return c;
    	    	     }
    	    	     RyRoute.Controller.$inject = ["$scope", "facebook"];
    	    	     return RyRoute.Controller;
    			}
    		};
    	}])
    	.directive('resolveLoader', ["$rootScope", "$timeout", function($rootScope, $timeout) {
    		RyRoute.cachedRoutes = [];
        	return {
    		    restrict: 'E',
    		    replace: true,
    		    template: '<div class="alert alert-success ng-hide">Chargement en cours.</div>',
    		    link: function(scope, element) {
    		
    		      $rootScope.$on('$routeChangeStart', function(event, currentRoute, previousRoute) {  
    		    	if (currentRoute.$$route.templateUrl && RyRoute.cachedRoutes.indexOf(currentRoute.$$route.templateUrl)>=0) 
    		    		return;
    		
    		    	RyRoute.cachedRoutes.push(currentRoute.$$route.templateUrl);
    		    	
    		        $timeout(function() {
    		          element.removeClass('ng-hide');
    		          var overlay = $("<div>ty atao overlay e</div>");
    		          angular.element($("[ng-view]")).append(overlay);
    		          overlay.css('height',$("[ng-view]").height());
    		          overlay.css('width', $("[ng-view]").width());
    		          overlay.css('position','absolute');
    		          overlay.css('top',0);
    		          overlay.css('left',0);
    		          overlay.css('background','#000');
    		          overlay.css('opacity','0.8');
    		          overlay.css('display','block');
    		        });
    		        
    		      });
    		
    		      $rootScope.$on('$routeChangeSuccess', function() {
    		        element.addClass('ng-hide');
    		      });
    		    }
        	};
    	}]);
		
	})();
	
})(window, window.angular);;
window.ryRoute={version:{full: "1.0.0"}};
