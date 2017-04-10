<?php

require 'vendor/autoload.php';

$routesContent = file_get_contents('/home/pgiusti/dev/star-tracking/api/code/routes/api.php');

echo $routesContent;