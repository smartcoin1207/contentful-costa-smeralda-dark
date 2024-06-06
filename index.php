<?php

require_once './_include/db.class.php';
$db = new Database();
$conn = $db->conn;

// Number of records per page
$recordsPerPage = 10;

// Current page number
if (isset($_GET['page'])) {
    $currentPage = $_GET['page'];
} else {
    $currentPage = 1;
}

//Search by Title
if (isset($_GET['search_query'])) {
    $search_query = $_GET['search_query'];
} else {
    $search_query = '';
}

if(!$currentPage) $currentPage = 1;
$startFrom = ($currentPage - 1) * $recordsPerPage;

$sql = "SELECT tb.*, ca.category_new as category_new FROM article AS tb LEFT JOIN categories AS ca ON ca.category = tb.category WHERE title LIKE '%$search_query%' AND hidden=0 LIMIT $startFrom, $recordsPerPage";
$result = $conn->query($sql);
$all_results = $result->fetch_all(MYSQLI_ASSOC);
$all_results_json = json_encode($all_results);

$sql = "SELECT COUNT(*) AS total FROM article WHERE title LIKE '%$search_query%' AND hidden=0";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$totalRecords = $row["total"];
$totalPages = ceil($totalRecords / $recordsPerPage);
$startPage = max(1, $currentPage - 3);
$endPage = min($totalPages, $currentPage + 3);

ob_start();
include './frontend/src/index.html';
$html = ob_get_clean();
echo $html;

$conn->close();
?>