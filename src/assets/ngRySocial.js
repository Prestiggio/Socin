String.prototype.toCamelCase = function () {
    return this.replace(/^(.)/, function ($1) {
        return $1.toUpperCase();
    });
};

(function( window, angular, undefined ){
	"use strict";
	
	(function(){
	"use strict";
	
	var Balita = function(confs) {
		this.confs = confs;
		
		this.firstLoaded = true;
		
		this.submitOnFirstLoad = function(fbResponse, refreshResponse, redirect){
			if(!this.firstLoaded) {
				$(document).trigger("ryfb:in", [fbResponse, refreshResponse]);
			}
			this.firstLoaded = false;
			if(redirect)
				document.location.href = this.confs.urls.home;
		};
		
		this.loginOptions = function(){
			return {scope:this.confs.scope};
		};
		
		this.revoke = function(perm, callback) {
			if(perm) {
				FB.api('/me/permissions/' + perm, 'delete', function(){
					callback();
				});
			}
		};
		
		this.allGranted = function(callback){
			FB.api('/me/permissions', function(response) {
  			  var declined = [];
  			  for (var i = 0; i < response.data.length; i++) { 
  			    if (response.data[i].status == 'granted') {
  			    	$(document).trigger("ryfb:grant", [response.data[i].permission]);
  			    }
  			    else {
  			    	declined.push(response.data[i].permission);
    			    $(document).trigger("ryfb:revoke", [response.data[i].permission]);
  			    }
  			  }
  			  if(declined.length>0)
  				  $(document).trigger("ryfb:decline", declined);
  			  else if(callback)
  				  callback();
			});
		};
		
		this.login = function(perm){
			var dis = this;
			FB.login(function(fbresponse){
    			if(fbresponse.authResponse) {
    				dis.allGranted(function(){
    					$(document).trigger("ryfb:refresh", [fbresponse]);
    				});
    			}
    			else {
    				$(document).trigger("ryfb:out", [fbresponse]);
    			}
            }, !perm ? this.loginOptions() : {scope:perm});
		};
	};
	
	var RyFacebook = {
        ready : function () {    
    		FB.getLoginStatus(RyFacebook.authStatusChanged, RyFacebook.accessTokenShouldExpire());
    	},
        authStatusChangedDispatching : false,
        authStatusChanged:function(fblogin){
        	if(RyFacebook.authStatusChangedDispatching)
        		return;
        	
        	RyFacebook.authStatusChangedDispatching = true;
    		if(fblogin.status==='connected') {
    			$(document).trigger("ryfb:refresh", [fblogin]);
    		}
    		else {
    			$(document).trigger("ryfb:out", [fblogin]);
    		}
    	},
        accessTokenShouldExpire : function(){
        	return false;
        }
	};

	angular.module('ngRySocial', ["ng","ngAnimate","ngSanitize","ngRyRoute"])
	.provider('$ngRyFacebook', function $ngRyFacebookProvider(){
		
		var confs = {
			urls : {
				refreshToken : "/example/refreshtoken",
		    	hasLogout : "/example/haslogout",
		    	home : "/example"
			},
			social:{
				facebook: {
					id: ""
				}
			},
			region : 'en_GB',
			scope : 'publish_actions'
		};
		
		this.setHomeUrl = function(url) {
			confs.urls.home = url;
		};
		
		this.serverSideCallbackUrl = function(url) {
			confs.urls.refreshToken = url;
		};
		
		this.serverSideLogoutCallbackUrl = function(url) {
			confs.urls.hasLogout = url;
		};
		
		this.facebookAppID = function(appId) {
			confs.social.facebook.id = appId;
		};
		
		this.setRegion = function(region) {
			confs.region = region;
		};
		
		this.setScope = function(scopes) {
			confs.scope = scopes;
		};
		
		this.$get = function(){
			return new Balita(confs);
		};
	})
	.factory('FBSS', ["$http", "$q", "$ngRyFacebook", function($http, $q, $ngRyFacebook){
    	var refreshToken = function(){
    		var deferred = $q.defer();
            var promise;

            var cancel = function(reason){
                deferred.resolve(reason);
            };

            promise = $http.get($ngRyFacebook.confs.urls.refreshToken, { timeout: deferred.promise}).then(function(resp){
                return resp;
            });

            return {
                promise : promise,
                cancel : cancel
            };
    	};
    	
    	var hasLogout = function(){
    		var deferred = $q.defer();
            var promise;

            var cancel = function(reason){
                deferred.resolve(reason);
            };

            promise = $http.get($ngRyFacebook.confs.urls.hasLogout, { timeout: deferred.promise}).then(function(resp){
                return resp;
            });

            return {
                promise : promise,
                cancel : cancel
            };
    	};
    	
    	return {
    		refreshToken : refreshToken,
    		hasLogout : hasLogout
    	};
    }]).service('facebook', ["$window", "$q", "$ngRyFacebook", function ($window, $q, $ngRyFacebook) {
        var deferred = $q.defer();
        
        function loadScript() {
            $.getScript("//connect.facebook.net/"+$ngRyFacebook.confs.region+"/sdk.js");
        }

        $window.fbAsyncInit = function () {
            deferred.resolve();
            FB.init({
                appId: $ngRyFacebook.confs.social.facebook.id,
                cookie: true,
                xfbml: true,
                status: true,
                version: 'v2.7'
            });
            FB.Event.subscribe("auth.statusChange", RyFacebook.authStatusChanged);
        };
        
        /*
        if ($window.attachEvent) {
            $window.attachEvent('onload', loadScript);
        } else {
            $window.addEventListener('load', loadScript, false);
        }
        */
        
        loadScript();
        
        this.promise = deferred.promise;
        
        return this.promise;
    }]).directive('ryfbpagetab', ["facebook", "FBSS", "RyRouter", "$ngRyFacebook", function (facebook, FBSS, RyRouter, $ngRyFacebook){
    	var btns = [];
    	return {
    		restrict : 'C',
    		controller : RyRouter.controller(),
    		link: function($scope, element, attr) {
    			btns.push(element);
    			
    			var clickAddToTab = function(){
    				FB.ui({
    					method: 'pagetab',
    					redirect_uri: attr.redirect
					}, function(response){
						
					});
    			};
    			
    			facebook.then(function(){
    				$(btns).each(function(){
            			$(this).off("click", clickAddToTab);
                		$(this).on("click", clickAddToTab);
        			});
    			});
    		}
    	};
    }]).directive('ryfbshare', ["facebook", "FBSS", "RyRouter", "$ngRyFacebook", function (facebook, FBSS, RyRouter, $ngRyFacebook){
    	var btns = [];
    	return {
    		restrict : 'C',
    		controller : RyRouter.controller(),
    		link: function($scope, element, attr) {
    			btns.push(element);
    			var clickAddToTab = function(){
    				FB.ui({
    					method: 'share',
    					href: attr.redirect
					}, function(response){
						
					});
    			};
    			
    			facebook.then(function(){
    				$(btns).each(function(){
            			$(this).off("click", clickAddToTab);
                		$(this).on("click", clickAddToTab);
        			});
    			});
    		}
    	};
    }]).directive('ryfbfeed', ["facebook", "FBSS", "RyRouter", "$ngRyFacebook", function (facebook, FBSS, RyRouter, $ngRyFacebook){
    	var btns = [];
    	return {
    		restrict : 'C',
    		controller : RyRouter.controller(),
    		link: function($scope, element, attr) {
    			btns.push(element);
    			var clickAddToTab = function(){
    				FB.ui({
    					method: 'feed',
    					link: attr.redirect,
    					caption: attr.caption
					}, function(response){
						
					});
    			};
    			
    			facebook.then(function(){
    				$(btns).each(function(){
            			$(this).off("click", clickAddToTab);
                		$(this).on("click", clickAddToTab);
        			});
    			});
    		}
    	};
    }]).directive('ryfbsend', ["facebook", "FBSS", "RyRouter", "$ngRyFacebook", function (facebook, FBSS, RyRouter, $ngRyFacebook){
    	var btns = [];
    	return {
    		restrict : 'C',
    		controller : RyRouter.controller(),
    		link: function($scope, element, attr) {
    			btns.push(element);
    			var clickAddToTab = function(){
    				FB.ui({
    					method: 'send',
    					link: attr.redirect
					}, function(response){
						
					});
    			};
    			
    			facebook.then(function(){
    				$(btns).each(function(){
            			$(this).off("click", clickAddToTab);
                		$(this).on("click", clickAddToTab);
        			});
    			});
    		}
    	};
    }]).directive('ryfblogin', ["facebook", "FBSS", "RyRouter", "$ngRyFacebook", function (facebook, FBSS, RyRouter, $ngRyFacebook){
    	var grantLogin = function(){
    		$ngRyFacebook.login();
    	};
    	var btns = [];
    	return {
    		restrict: 'C',
    		controller : RyRouter.controller(),
            link: function($scope, element, attr) {
            	btns.push(element);
            	
            	var clickLogin = function () {  
            		$(this).prop("disabled", "disabled");
            		RyFacebook.authStatusChangedDispatching = true;
            		grantLogin();
                };
                
                var clickLogout = function () { 
                	var dis = this;
                	$(this).prop("disabled", "disabled");
					FB.logout(function(){
						$(dis).prop("disabled", "disabled");
    					FBSS.hasLogout().promise.then(function(ret){
    						$(dis).prop("disabled", false);
    						$(dis).html("login");
    						$(document).trigger("ryfb:logout", [ret]);
                        });
    				});
                };
                
                var refreshAction = function(event, fbresponse){
                	$.each(btns, function(){
                		$(this).prop("disabled", "disabled");
                	});
        			FBSS.refreshToken().promise.then(function(ret){
        				RyFacebook.authStatusChangedDispatching = false;
        				$(btns).each(function(){
        					$(this).prop("disabled", false);
        					var logoutLabel = $(this).data("logout");
        					if(!logoutLabel)
        						logoutLabel = "logout";
            				$(this).html(logoutLabel);
            				$(this).off("click", clickLogin);
            				$(this).off("click", clickLogout);
            				$(this).on("click", clickLogout);
        				});   				
        				$ngRyFacebook.submitOnFirstLoad(fbresponse, ret, attr.redirect);
                    });
        		};
        		
        		var outAction = function(event, ret){
        			RyFacebook.authStatusChangedDispatching = false;
        			$ngRyFacebook.firstLoaded = false;
        			$(btns).each(function(){
        				$(this).prop("disabled", false);
            			$(this).off("click", clickLogout);
            			$(this).off("click", clickLogin);
                		$(this).on("click", clickLogin);
        			});
        		};
        		
        		$(btns).each(function(){
        			$(this).prop("disabled", "disabled");
        		});
        		
        		RyFacebook.authStatusChangedDispatching = false;
        		$(document).off("ryfb:refresh.core");
        		$(document).on("ryfb:refresh.core", refreshAction);
        		$(document).off("ryfb:out.core");
        		$(document).on("ryfb:out.core", outAction);
            	facebook.then(RyFacebook.ready);
            }
    	}
    }]).directive('ryfbgrant', ["facebook", "FBSS", "RyRouter", "$ngRyFacebook", function (facebook, FBSS, RyRouter, $ngRyFacebook){
    	var grantLogin = function(perm){
    		$ngRyFacebook.login(perm);
    	};
    	var btns = {};
    	return {
    		restrict: 'C',
    		controller : RyRouter.controller(),
            link: function($scope, element, attr) {
            	if(!btns[attr.perm])
            		btns[attr.perm] = [];
            	
            	btns[attr.perm].push(element);
            	
            	var clickGrant = function () {
            		//$(this).prop("disabled", "disabled");
            		//grantLogin(attr['data-perm']);
                };
                
                var clickRevoke = function () { 
                	var dis = this;
                	/*$(this).prop("disabled", "disabled");
                	$ngRyFacebook.revoke($(this).attr("data-perm"), function(){
                		$(dis).prop("disabled", "disabled");
                		$(document).trigger("ryfb:revoke", [$(dis).attr("data-perm")]);
                	});*/
                };
                
                var refreshAction = function(event, fbresponse){
                	$.each(btns[attr.perm], function(){
                		$(this).prop("disabled", "disabled");
                	});
                	$ngRyFacebook.allGranted();
        		};
        		
        		var grantAction = function(event, ret){
        			$(btns[ret]).each(function(){                		
            			$(this).prop("disabled", false);
        				$(this).off("click", clickGrant);
        				$(this).off("click", clickRevoke);
        				$(this).on("click", clickRevoke);
                	});
        		};
        		
        		var revokeAction = function(event, ret){
        			RyFacebook.authStatusChangedDispatching = false;
        			$ngRyFacebook.firstLoaded = false;
        			$(btns[ret]).each(function(els){
            			$(this).prop("disabled", false);
            			$(this).off("click", clickRevoke);
            			$(this).off("click", clickGrant);
                		$(this).on("click", clickGrant);
        			});
        		};
        		
        		$.each(btns[attr.perm], function(){
            		$(this).prop("disabled", "disabled");
            	});
        		
        		$(document).off("ryfb:refresh");
        		$(document).on("ryfb:refresh", refreshAction);
        		$(document).off("ryfb:grant");
        		$(document).on("ryfb:grant", grantAction);
        		$(document).off("ryfb:revoke");
        		$(document).on("ryfb:revoke", revokeAction);
            	facebook.then(RyFacebook.ready);
            }
    	}
    }]);
	
	
	})();
	
})(window, window.angular);;
window.ryFacebook={version:{full: "1.0.0"}};
