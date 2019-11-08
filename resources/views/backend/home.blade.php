<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ mix('css/app.css','/backend') }}">
</head>
	<body>
		<div id="app">
            <router-view></router-view>
        </div>
	</body>
    <script src="{{ mix('js/manifest.js', '/backend') }}"></script>
    <script src="{{ mix('js/vendor.js', '/backend') }}"></script>
    <script src="{{ mix('js/app.js', '/backend') }}"></script>
</html>