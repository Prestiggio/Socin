@section("form")
<div layout="row">
	<div flex="50" class="md-padding">
		<table style="width:100%">
		  <tr>
		    <th>éditeur</th>    
		    <th>nom</th>    
		    <th>url</th>
		  </tr>
		  <tbody ng-repeat="row in data.rows">
		 	<tr>  	  	
		    	<td>@{{row.editor.name}}</td>  	
		    	<td>@{{row.name}}</td>  	
		    	<td>@{{row.url}}</td>
		 	</tr>
		 	<tr>
		    	<td colspan="3" class="text-right">
		    		<md-button ng-click="data.focus=row">Modifier</md-button>
		    		<md-button href="@{{row.detail_url}}">Voir détail</md-button>
		    		<md-button ng-click="deleterysocinfacebooksource(row)">Supprimer</md-button>
		    	</td>
		  	</tr>
		  </tbody>
		</table>
	</div>
	<div flex="50">
		<md-content class="affix">
			<div class="text-right">
				<md-button ng-click="reset()">Nouveau</md-button>
			</div>
			<form novalidate name="frm_rysocinfacebooksource" ng-submit="frm_rysocinfacebooksource.$valid && submitrysocinfacebooksource()" layout="column" class="md-padding">	
				<md-input-container class="md-block">
				 	<label>nom</label>
				 	<input type="text" ng-model="data.focus.name" name="rysocinfacebooksource_name" required>
				 	<div ng-messages="frm_rysocinfacebooksource.rysocinfacebooksource_name.$error">
				 		<div ng-message="required">Vous devez renseigner le nom</div>
				 	</div>
				</md-input-container>				
				<md-input-container class="md-block">
				 	<label>url</label>
				 	<input type="text" ng-model="data.focus.url" name="rysocinfacebooksource_url" required>
				 	<div ng-messages="frm_rysocinfacebooksource.rysocinfacebooksource_url.$error">
				 		<div ng-message="required">Vous devez renseigner l'url</div>
				 	</div>
				</md-input-container>				
				<md-input-container class="md-block">
				 	<label>url d'API</label>
				 	<input type="text" ng-model="data.focus.endpoint" name="rysocinfacebooksource_endpoint" required>
				 	<div ng-messages="frm_rysocinfacebooksource.rysocinfacebooksource_endpoint.$error">
				 		<div ng-message="required">Vous devez renseigner l'url d'API</div>
				 	</div>
				</md-input-container>				
				<md-input-container class="md-block">
				 	<label>token d'accès</label>
				 	<input type="text" ng-model="data.focus.access_token" name="rysocinfacebooksource_access_token" required>
				 	<div ng-messages="frm_rysocinfacebooksource.rysocinfacebooksource_access_token.$error">
				 		<div ng-message="required">Vous devez renseigner le token d'accès</div>
				 	</div>
				</md-input-container>
				<md-button type="submit" class="md-raised md-primary" ng-disabled="loading || frm_rysocinfacebooksource.$pending">Enregistrer</md-button>
			</form>
		</md-content>
	</div>
</div>
@stop

@section("formscript")
<script type="text/javascript">
function main($scope, $http, $window) {
	$scope.data = {
		rows : {!!$rows!!},
		focus : {}
	};

	$scope.reset = function(){
		$scope.data.focus = {};
	};

	$scope.submitrysocinfacebooksource = function(){
		$http.post("{{action("\Ry\Socin\Http\Controllers\AdminController@postSubmit")}}", $scope.data.focus).then(function(){
			$window.location.reload();
		});
	};

	$scope.deleterysocinfacebooksource = function(row){
		$http.post("{{action("\Ry\Socin\Http\Controllers\AdminController@postDelete")}}", row).then(function(){
			$window.location.reload();
		});
	}
}
main.$inject = ["$scope", "$http", "$window"];
</script>
@stop