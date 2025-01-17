<?php
require("../services/auth.php");

Auth::logOut();
header('Location: ../index.php');