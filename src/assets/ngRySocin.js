(function(window, angular, $, undefined){
	
	var ngRySocinService = function(){
		
	};
	
	var ngRySocin = angular.module("ngRySocin", ["ngMaterial", "ngRySocial"])
	.provider("$ngRySocinSetup", ["$ngRyRouteSetupProvider", "$ngRyFacebookProvider", "$mdDateLocaleProvider", "$mdThemingProvider", function $ngRySocinSetupProvider($route, $fb, dateLocaleProvider, $mdThemingProvider){
		
		this.conf = {};
		
		this.setup = function(conf, routes){
			this.conf = conf;
			
			$fb.facebookAppID(conf.appId);
			$fb.setRegion(conf.region);
			$fb.setScope(conf.scope);
			$fb.serverSideCallbackUrl(conf.refreshTokenUrl);
			$fb.serverSideLogoutCallbackUrl(conf.flushUrl);
			$fb.setHomeUrl(conf.homeUrl);
			
			dateLocaleProvider.formatDate = function(date) {
				return date.getDate() + "/" + (date.getMonth()+1) + "/" + date.getFullYear();
			};
			
			if(conf.theme.palette) {
				$mdThemingProvider.definePalette('rypalette', conf.theme.palette);
				$mdThemingProvider.theme('default')
                .primaryPalette(conf.theme.primary)
                .accentPalette('rypalette');
			}
			
			angular.forEach(routes, function(v, k){
				$route.setController(k, v);
			});
			
			$route.setRoutes(conf.ngRoutes);
		};
		
		this.$get = function(){			
			return new ngRySocinService();
		};
	}]);
	
})(window, window.angular, $);;

window.rySocin={version:{full: "1.0.0"}};
