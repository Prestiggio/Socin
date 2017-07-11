<script type="text/javascript">
function main($scope, $http, $mdDialog, $sessionStorage, ryfb, $timeout, $location, $app) {
	$scope.loading = false;

	$scope.userdata = {};

	$scope.userdatareset = {};

	$scope.login = function(){
		$scope.loading = true;

		$timeout(function(){
			document.location.reload();
		}, 30000);
		
		$http.post("{{ url('/login') }}", $scope.userdata).then(function(response){
			document.location.href = response.data.redirect;
			$scope.loading = false;
		}, function(error){
			$scope.loading = false;
			$mdDialog.show($mdDialog.alert().clickOutsideToClose(false).title(document.location.host)
			        .textContent(error.message)
			        .ok('OK!'));
		});
	};

	$app.onDialogRemove = function(){
		$location.path("");
	};

	$scope.gofb = function(){
		$scope.loading = true;
		delete $sessionStorage.fb.facebookphobia;
		ryfb.user().then(function(response){
			$scope.loading = false;
			if(response.redirect)
				document.location.href = response.redirect;
		}, function(error){
			$scope.loading = false;
			$mdDialog.show($mdDialog.alert().clickOutsideToClose(false).title(document.location.host)
			        .textContent(error.data.message)
			        .ok('OK!'));
		});
	}

	$scope.reset = function(){
		$http.post("{{ url('/password/email') }}", $scope.userdatareset).then(function(response){
			$mdDialog.show($mdDialog.alert().clickOutsideToClose(true).title(document.location.host)
			        .textContent(response.data.message)
			        .ok('OK!'));
		}, function(){
			
		});
	};
}
main.$inject = ["$scope", "$http", "$mdDialog", "$sessionStorage", "ryfb", "$timeout", "$location", "$appSetup"];
</script>