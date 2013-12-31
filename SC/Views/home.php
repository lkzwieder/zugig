<!DOCTYPE html>
<html>
<head>
    <title><?=$view->title?></title>
    <!-- Third party libraries and plugins -->
    <script src="/public/js/lib/jquery-2.0.3.min.js"></script>
    <script src="/public/js/lib/jquery.uriHandler-0.1.js"></script>

    <!-- our owns -->
    <script src="/public/js/sc.js"></script>
    <script src="/public/js/sc.shell.js"></script>
    <script src="/public/js/sc.test.js"></script>
</head>
<body>
    <div id="sc"></div>
</body>
<script>(function(){sc.initModule($('#sc'));})();</script>
</html>