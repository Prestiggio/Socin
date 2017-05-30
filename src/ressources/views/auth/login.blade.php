<script type="application/dialog" data-href="/reset-password">
@include("rysocin::auth.email")
</script>
<md-content layout="row" layout-align="center center">
	<form class="md-whiteframe-2dp md-padding" name="frm_login" novalidate flex-lg="33" flex-md="33" ng-submit="login()">
         <div class="text-center md-headline">@lang("rysocin::auth.login")</div>
         <div class="text-center md-subhead">en un clic avec</div>
         <div layout="row" layout-align="space-between center" layout-wrap>
         	<md-button type="button" class="md-raised md-primary" flex-md-lg flex-xs="100" ng-click="gofb()">@lang("rysocin::auth.facebook")</md-button>
         </div>
         <div class="text-center md-subhead">ou</div>
         <md-input-container class="md-block">
         	<label>@lang("rysocin::auth.email")</label>
         	<input type="email" name="email" ng-model="userdata.email" required>
         	<div ng-messages="frm_login.email.$error">
         		<div ng-message="email">Veuillez renseigner un email valide</div>
         		<div ng-message="required">Vous devez renseigner un email</div>
         		@if ($errors->has('email'))
	            <div>{{ $errors->first('email') }}</div>
	            @endif
         	</div>
         	
         </md-input-container>
         <md-input-container class="md-block">
         	<label>@lang("rysocin::auth.password")</label>
         	<input type="password" name="password" ng-model="userdata.password" required minlength="4">
         	<div ng-messages="frm_login.password.$error">
         		<div ng-message="required">Vous devez renseigner un mot de passe</div>
         		<div ng-message="minlength">Saisissez au moins 4 caractères</div>
         		 @if ($errors->has('password'))
	            <div>{{ $errors->first('password') }}</div>
	            @endif
         	</div>
         </md-input-container>
         <md-input-container class="md-block">
         	<md-checkbox ng-model="userdata.remember" aria-label="Checkbox 1">
	            @lang("rysocin::auth.remember")
	          </md-checkbox>
         </md-input-container>
         <div layout="row" layout-align="space-between center">
         	<md-button type="submit" class="md-raised md-accent">@lang("rysocin::auth.login")</md-button>
			<a href="#!/reset-password">@lang("rysocin::auth.forgotten")</a>
         </div>
         <div layout="column">
         	<md-button href="/register" class="md-raised md-accent" flex>@lang("rysocin::auth.not_yet")</md-button>
         </div>
    </form>
</md-content>