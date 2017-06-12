(function( window, angular, undefined ){
	"use strict";
	
	(function(){
	"use strict";
	
	var RySocin = function(confs) {
		this.confs = confs;
	};

	angular.module('ngRySocin', ["ng"])
	.provider('$ngRyFacebook', function $ngRyFacebookProvider(){
		
		this.confs = {
			urls : {
				register : "/example/register",
				refreshToken : "/example/refreshtoken",
		    	hasLogout : "/example/haslogout",
		    	send : "/example/send"
			},
			social:{
				facebook: {
					id: "",
					scope : 'publish_actions'
				}
			},
			region : 'en_GB'
		};
		
		this.setup = function(confs){
			this.confs = angular.merge(this.confs, confs);
		};
		
		this.$get = function(){
			return new RySocin(this.confs);
		};
	}).service("$socin", function(){
		
		this.engine = "facebook";
		
	}).service("ryfbinit", ["$q", "$window", "$ngRyFacebook", "$sessionStorage", "$socin", function($q, $window, $ngRyFacebook, $sessionStorage, $socin){
		
		var initDeferred = $q.defer();
		
		var dis = this;
		
		$window.fbAsyncInit = function () {
            FB.init({
                appId: $ngRyFacebook.confs.social.facebook.id,
                cookie: true,
                xfbml: true,
                status: true,
                version: 'v2.9'
            });
            FB.Event.subscribe("auth.statusChange", function(fblogin){
	        	switch(fblogin.status){
					case "connected":
						
						break;
					default:
						if($socin.engine == "facebook")
							delete $sessionStorage.user;
						break;
				}
			});
            initDeferred.resolve();
        };
        
        $.getScript("//connect.facebook.net/"+$ngRyFacebook.confs.region+"/sdk.js");
		
		return initDeferred.promise;
		
	}]).service('ryfb', ["$window", "$http", "$q", "$ngRyFacebook", "ryfbinit", "$sessionStorage", "$rootScope", "$mdDialog", "$appSetup",
	                     function($window, $http, $q, $ngRyFacebook, ryfbinit, $sessionStorage, $rootScope, $mdDialog, $app){
		
		var defaultuser = {
			id : $app.data.auth ? $app.data.auth.id : 0,
			profile : {
				picture : {
					path : "/kipa2/images/avatar.png"
				}
			},
			notifications : [],
			url : {
				dashboard : "#"
			},
			menuitems : []
		};
		
		var needrefresh = true;
		
		this.init = function(){
			if(!$sessionStorage.fb)
				$sessionStorage.fb = {};
			
			if($sessionStorage.fb.facebookphobia) {
				this.initDeferred = $q.defer();
				
				this.initDeferred.reject($sessionStorage.fb);
				
				return this.initDeferred.promise;
			}
			return ryfbinit;
		};
		
		this.createUser = function(fbresponse){
			return {
				id : fbresponse.id,
				name : fbresponse.name,
				profile : {
					picture : {
						path : "https://graph.facebook.com/me/picture?access_token=" + FB.getAccessToken()
					}
				},
				notifications : [],
				url : {
					dashboard : "#"
				},
				menuitems : []
			};
		}
		
		this.status = function(){			
			if(this.statusDeferred!=null) {
				this.statusDeferred.reject();
			}
			this.statusDeferred = $q.defer();
			
			var dis = this;
			
			var callback = function(fblogin){
				switch(fblogin.status){
					case "connected":
						if(!$sessionStorage.user) {
							FB.api("/me?fields=id,email", function(response){
								$sessionStorage.user = dis.createUser(response);
								dis.statusDeferred.resolve($sessionStorage.user);
							});
						}
						else
							dis.statusDeferred.resolve($sessionStorage.user);
						break;
					default:
						dis.statusDeferred.reject(defaultuser);
						break;
				}
			};
			
			this.init().then(function(){	
				FB.getLoginStatus(callback, needrefresh);
				needrefresh = false;
			}, function(){
				dis.statusDeferred.reject(defaultuser);
			});
			
			return this.statusDeferred.promise;
		};
		
		this.user = function(attr){
			if(this.userDeferred!=null) {
				this.userDeferred.reject();
			}			
			this.userDeferred = $q.defer();
			
			var dis = this;
			
			if($sessionStorage.user) {
				this.userDeferred.resolve($sessionStorage.user);
			}
			else {
				this.init().then(function(){
					var registration = function(){
						$http.get($ngRyFacebook.confs.urls.register).then(function(response){
							$sessionStorage.user = dis.createUser(response);
							dis.userDeferred.resolve($sessionStorage.user);
						}, function(error){
							dis.userDeferred.reject(error);
						});
					};
					var redial = function(error){
						if(error.indexOf && error.indexOf("email")>=0) {
							$mdDialog.show($mdDialog.alert().clickOutsideToClose(false).title(document.location.host)
							        .textContent("L'email est absolument obligatoire pour vous enregistrer! Merci de vous réinscrire !")
							        .ok('OK!')).then(function(){
							        	if(!$sessionStorage.fb.facebookphobia) {
							        		FB.login(function(fbresponse){					
								    			if(fbresponse.authResponse) {
								    				dis.allGranted().then(registration, redial);
								    			}
								    			else {
								    				$sessionStorage.fb.facebookphobia = true;
								    				dis.grantDeferred.reject(angular.merge({}, fbresponse, $sessionStorage.fb));
								    			}
								            }, {scope:["email"]});
							        	}
							        });
						}
						else {
							dis.userDeferred.reject(error);
						}
					};
					dis.grant({scope:["email"]}).then(registration, redial);
				}, function(error){
					dis.userDeferred.reject(error);
				});
			}
			
			return this.userDeferred.promise;
		};

		this.grant = function(attr){
			if(this.grantDeferred!=null) {
				this.grantDeferred.reject();
			}
			this.grantDeferred = $q.defer();
			
			var dis = this;
			
			this.init().then(function(){
				dis.status().then(function(){
					dis.allGranted().then(function(){
						$http.get($ngRyFacebook.confs.urls.refreshToken).then(function(resp){
    						dis.grantDeferred.resolve();
    		            },function(){
    		            	dis.grantDeferred.reject();
    		            });
    				}, function(declined){
    					dis.grantDeferred.reject(declined);
    				});
				}, function(){
					if(!$sessionStorage.fb.facebookphobia) {
						FB.login(function(fbresponse){						
			    			if(fbresponse.authResponse) {
			    				dis.allGranted().then(function(){
			    					var refreshTokenServer = $http.get($ngRyFacebook.confs.urls.refreshToken);
				    				refreshTokenServer.then(function(resp){
			    						dis.grantDeferred.resolve();
			    		            },function(){
			    		            	dis.grantDeferred.reject();
			    		            });
			    				}, function(declined){
			    					dis.grantDeferred.reject(declined);
			    				});
			    			}
			    			else {
			    				$sessionStorage.fb.facebookphobia = true;
			    				dis.grantDeferred.reject(angular.merge({}, fbresponse, $sessionStorage.fb));
			    			}
			            }, attr.scope ? {scope:attr.scope} : null);
					}
					else {
						dis.grantDeferred.reject($sessionStorage.fb);
					}					
				});
			}, function(){
				dis.grantDeferred.reject();
			});
			
			return this.grantDeferred.promise;
		};
		
		this.sizeOf = function(obj){
			var i = 0;
			for(key in obj)
				i++;
			return i;
		};
		
		this.allGranted = function(){
			if(this.allGrantedDeferred!=null) {
				this.allGrantedDeferred.reject();
			}
			this.allGrantedDeferred = $q.defer();
			
			var dis = this;
			
			this.init().then(function(){
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
		  				dis.allGrantedDeferred.reject(declined);
		  			else
		  				dis.allGrantedDeferred.resolve(response);
				});
			}, function(){
				
			});
			
			return this.allGrantedDeferred.promise;
			
		};
		
		this.pagetab = function(attr){
			if(this.pagetabDeferred!=null)
				this.pagetabDeferred.reject();
			
			this.pagetabDeferred = $q.defer();
			
			var dis = this;
			this.init().then(function(){
				FB.ui({
					method: 'pagetab',
					redirect_uri: attr.redirect
				}, function(response){
					dis.resolve();
				});
			}, function(){
				dis.reject();
			});
			
			return this.pagetabDeferred.promise;
		};
		
		this.share = function(attr){
			if(this.shareDeferred!=null)
				this.shareDeferred.reject();
			
			this.shareDeferred = $q.defer();
			
			var dis = this;
			
			this.init().then(function(){
				FB.ui({
					method: 'share',
					href: attr.redirect
				}, function(response){
					dis.sharedDeferred.resolve();
				});
			}, function(){
				dis.sharedDeferred.reject();
			});
			
			return this.sharedDeferred.promise;
		};
		
		this.feed = function(attr){
			if(this.feedDeferred!=null)
				this.feedDeferred.reject();
			
			this.feedDeferred = $q.defer();
			
			var dis = this;
			
			this.init().then(function(){
				FB.ui({
					method: 'feed',
					link: attr.redirect,
					caption: attr.caption
				}, function(response){
					
				});			
			}, function(){
				dis.feedDeferred.reject();
			});
			
			return this.feedDeferred.promise;
		};
		
		this.send = function(attr){
			if(this.sendDeferred!=null)
				this.sendDeferred.reject();
			
			this.sendDeferred = $q.defer();
			
			var dis = this;
			
			this.init().then(function(){
				FB.ui({
					method: 'send',
					to : attr.to,
					link: attr.link
				}, function(response){
					
				});
			}, function(){
				dis.sendDeferred.reject();
			});
			
			return this.sendDeferred.promise;
		};
		
		this.revoke = function(attr) {
			if(this.revokeDeferred!=null)
				this.revokeDeferred.reject();
			
			this.revokeDeferred = $q.defer();
			
			var dis = this;
			
			this.init().then(function(){
				FB.api('/me/permissions/' + attr.perm, 'delete', function(){
					dis.revokeDeferred.resolve();
				});
			}, function(){
				dis.revokeDeferred.reject();
			});
			
			return this.revokeDeferred.promise;
		};
		
		this.api = function(){
			FB.api("/me?fields=id,name", function(response){
				if ($rootScope.$$phase) {
					
	            } else {
	                $rootScope.$apply(function(){
	                	
	                });
	            }
			});
		};
		
		this.logout = function(){
			if(this.logoutDeferred!=null) {
				this.logoutDeferred.reject();
			}
			this.logoutDeferred = $q.defer();
			
			var dis = this;
			
			this.init().then(function(){
				if(FB.getAccessToken()) {
					FB.logout(function(){
						$http.get($ngRyFacebook.confs.urls.hasLogout).then(function(resp){
							needrefresh = true;
							dis.logoutDeferred.resolve();
			            }, function(){
			            	dis.logoutDeferred.reject();
			            });
					});
				}
				else {
					dis.logoutDeferred.reject();
				}
			}, function(){
				dis.logoutDeferred.reject();
			});
			
			return this.logoutDeferred.promise;
		};
    	
    }]).directive('hideSocin', function(){
    	return {
    		restrict : 'AEC',
    		link : function($scope, element, attr){
    			$(element).hide();
    			if(!window.parent,document)
    				$(element).show();
    		}
    	}
    }).directive('showSocin', function(){
    	return {
    		restrict : 'AEC',
    		link : function($scope, element, attr){
    			$(element).hide();
    			if(window.parent,document)
    				$(element).show();
    		}
    	}
    }).directive('ryfbbutton', ["ryfb", "$parse", function (ryfb, $parse){
    	var btns = {};
    	return {
    		restrict : 'A',
    		scope : {
    			fbConf: "=ryfbbutton"
    		},
    		link: function($scope, element, attr) {
    			if(!$scope.fbConf)
    				$scope.fbConf = {
    					then:[]
    				};
    			
    			if(attr.action)
    				$scope.fbConf.action = attr.action;
    			
    			if(!$scope.fbConf.action)
    				$scope.fbConf.action = "login";
    			
    			if(!btns[$scope.fbConf.action])
    				btns[$scope.fbConf.action] = [];
    			
    			if(btns[$scope.fbConf.action].indexOf(element)<0)
    				btns[$scope.fbConf.action].push(element);
    			
    			var action = $scope.fbConf.action;
    			var conf = $scope.fbConf;
    			
    			$(element).on("click", function(){
    				var success = conf.then.length > 0 ? conf.then[0] : function(){
    					document.location.href = attr.href;
    				};
    				var fail = conf.then.length > 1 ? conf.then[1] : function(){
    					document.location.href = attr.href;
    				};
    				ryfb[action](conf).then(success, fail);
    			});
    			
    			ryfb.init().then(function(){
    				$.each(btns[action], function(){
    					
                	});  
    			});
    		}
    	};
    }]);
	
	})();
	
})(window, window.angular);;
window.ryFacebook={version:{full: "1.0.0"}};
