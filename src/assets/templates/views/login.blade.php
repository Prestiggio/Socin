<div flex layout="column" layout-align="center center">
	<div layout="column" style="width: 80%">
		<form layout="column" name="frmregister" class="md-inline-form" ng-submit="submit()">
			<md-input-container>
				<label>Hira fidirana</label>
				<md-autocomplete flex md-selected-item="selectedItem" md-search-text="searchText" md-items="chant in getMatches(searchText)" md-item-text="chant.titre">
				  <span md-highlight-text="searchText">@{{chant.titre}}</span>
				</md-autocomplete>
			</md-input-container>
		</form>
	</div>
	<form id="frmSignIn" method="POST" action="{{ url('/login') }}" layout="row">
		{{ csrf_field() }}
		<md-button class="ryfblogin md-raised md-primary" data-redirect="{{ $redirect }}">login</md-button>
		<br/>
		<md-button class="ryfbpagetab md-raised" data-redirect="{{ $redirect }}">ajouter à mes pages</md-button>
		<br/>
		<md-button class="ryfbshare md-raised" data-redirect="https://apps.facebook.com/faithfunding">partager</md-button>
		<br/>
		<md-button class="ryfbfeed md-raised" data-redirect="https://apps.facebook.com/faithfunding" data-caption="Faith Funding pour Antanetibe">partager sur mon mur</md-button>
		<br/>
		<md-button class="ryfbsend md-raised" data-redirect="https://apps.facebook.com/faithfunding">inviter mes amis</md-button>
	</form>
</div>
