<?php
require_once './_include/db.class.php';
require_once ('./_include/file.class.php');

if(isset($_POST['cmd']) && $_POST['cmd'] == 'put') {
    FileHandler::fileReadToDatabase('put');
} else {
    FileHandler::fileReadToDatabase('');
}

header("Location: index.php");
