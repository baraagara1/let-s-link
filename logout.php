<?php
session_start();
session_unset();
session_destroy();
header('Location: /mon_project_web/View/frontoffice/PROJECTS/login.php');
exit();
