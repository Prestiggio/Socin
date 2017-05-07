@extends("rykto::canvas.layouts.material")

@section("meta")
<meta property="fb:app_id" content="225202401245843" />
<meta property="og:url" content="https://www.skygraver.com/faithfunding/" />
<meta property="og:title" content="Contribuez à partir de 100 Ar Via Mvola" />
<meta property="og:description" content="Plateforme de collecte de fond universel pour aider votre organisation" />
<meta property="og:image" content="{{ url("vendor/ryndbc/img/wall.jpg") }}" />
@stop

@section("content")
<div layout="row" ng-view layout-align="center center" flex>
	
</div>
<div flex="20">
	<resolve-loader></resolve-loader>
</div>
@stop
