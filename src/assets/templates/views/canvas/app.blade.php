<style type="text/css">
@font-face {
	font-family: 'Milton_One';
	src: url('{{ url("vendor/ryndbc/font/Milton_One.eot") }}');
	src: local('â˜º'), url('{{ url("vendor/ryndbc/font/Milton_One.woff") }}') format('woff'), url('{{ url("vendor/ryndbc/font/Milton_One.ttf") }}') format('truetype'), url('{{ url("vendor/ryndbc/font/Milton_One.svg") }}') format('svg');
	font-weight: normal;
	font-style: normal;
}
.bgndbc {
	background : #fff url("{{url("vendor/ryndbc/img/bg.gif")}}") no-repeat;
	background-size : contain;
	color: #fff;
	width: 100%;
	min-height: 980px;
}
.bgndbc {
	color: #c2aa38;
	font-size: 40px;
	text-align: center;
}
.bgndbc p span {
	font-family:'Milton_One',Sans-Serif;
	font-size: 72px;
}
.ryndbclink {
	display: block;
	background: #f5f5f5;
	padding: 10px 30px;
	font-family:'Milton_One',Sans-Serif;
	font-size: 44px;
	color: #7a592d;
	text-decoration: none;
}
</style>
<div class="bgndbc" flex layout="column" layout-align="center center">
	<a href="/faithfunding/ndbc"><img alt="" src="{{ url("vendor/ryndbc/img/medal.png") }}"></a>
	<p>Dans le cadre du <span>125ème</span> Anniversaire de notre église<br/>
<span>Notre Dame du Bon Conseil Antanetibe</span><br/> 
Nous avons décidé de rajouter les ailes est et ouest.<br/>
Contribuons tous à ce projet plein d’avenir.</p>
<p><a href="/faithfunding/ndbc" class="ryndbclink">Voir les options</a></p>
<div layout="row">
	<md-button class="ryfbpagetab md-raised" data-redirect="{{ $redirect or '0' }}">ajouter à mes pages</md-button>
	<md-button class="ryfbshare md-raised" data-redirect="https://apps.facebook.com/faithfunding">partager</md-button>
	<md-button class="ryfbfeed md-raised" data-redirect="https://apps.facebook.com/faithfunding" data-caption="Faith Funding pour Antanetibe">partager sur mon mur</md-button>
	<md-button class="ryfbsend md-raised" data-redirect="https://apps.facebook.com/faithfunding">inviter mes amis</md-button>
</div>
</div>