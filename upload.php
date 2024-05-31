<?php
require_once './_include/db.class.php';
require_once ('./_include/file.class.php');

FileHandler::fileReadToDatabase();

header("Location: index.php");
