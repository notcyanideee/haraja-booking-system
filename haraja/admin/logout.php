<?php
require_once __DIR__ . "/../db.php";
session_unset();
session_destroy();

// 4. Redirect to login
header("Location: login.php");
exit();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

</body>

</html>