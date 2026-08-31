<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $mainPage->event_name ?? config('app.name'))</title>
</head>
<body>
    @yield('content')
</body>
</html>
