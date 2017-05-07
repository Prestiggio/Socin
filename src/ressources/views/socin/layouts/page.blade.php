@extends("rymd::layouts.doctype")

@section("html")
<html ng-app="appPublic" ng-strict-di>
@stop

@section("meta")
<title>Ry Social Connection Material Design Angular Js</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta property="fb:app_id" content="{{$facebook["appId"]}}" />
<meta property="og:url" content="https://www.skygraver.com/faithfunding/" />
<meta property="og:title" content="Contribuez à partir de 100 Ar Via Mvola" />
<meta property="og:description" content="Plateforme de collecte de fond universel pour aider votre organisation" />
<meta property="og:image" content="{{ url("vendor/ryndbc/img/wall.jpg") }}" />
@stop

@section("basescript")
<script type="text/javascript" src="{{url("vendor/rysocin/js/script.min.js")}}"></script>
<script type="application/ld+json" id="conf">
{!!$js!!}
</script>
<script type="text/javascript">
(function(window, angular, $, gameApp, undefined){

	angular.module("appPublic", ["ngRySocin"]);
	
})(window, window.angular, window.jQuery, window.appApp);
</script>
@yield("script")
@stop

@section("body")
@yield("main")
@stop