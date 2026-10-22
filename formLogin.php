<?php

$queryString = !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '';
header('Location: index.php?opt=login' . $queryString);
exit;
