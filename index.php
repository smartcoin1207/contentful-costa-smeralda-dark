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

// Calculate the starting record index
$startFrom = ($currentPage - 1) * $recordsPerPage;

// Fetch student data with pagination

// $sql = "SELECT * FROM article LIMIT $startFrom, $recordsPerPage";
$sql = "SELECT tb.*, ca.category_new as category_new FROM article AS tb LEFT JOIN categories AS ca ON ca.category = tb.category WHERE title LIKE '%$search_query%' LIMIT $startFrom, $recordsPerPage";
$result = $conn->query($sql);

echo '<!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My PHP Project</title>
        <link rel="stylesheet" href="./frontend/css/app.css">
        
        <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" rel="stylesheet" /> -->

    <style>
        /* Grid styles */
        #table-search:focus-visible {
            border-color: #3b82f6; /* Blue color */
        }
    </style>
    </head>
    <body>';

    echo '
      <div id="upload-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full h-full bg-opacity-60 bg-gray-500 dark:bg-gray-700">
          <div class="relative max-w-3xl w-1/2">
              <!-- Modal content -->
              <div class="relative bg-white rounded-lg dark:bg-gray-700 border border-gray-500   m-4 p-4 " style="width: 500px">
                  <!-- Modal header -->
                  <div class="flex items-center justify-between">
                        <h1 class="text-3xl font-bold">Upload a File</h1>

                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="upload-modal">
                          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                          </svg>
                      </button>
                  </div>
                  <div class="flex justify-center items-center mt-4">
                    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
                        <form action="upload.php" method="post" enctype="multipart/form-data" class="space-y-4">
                            <div>
                                <input type="file" name="file" id="file" required class="border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-500">
                            </div>
                            <div class="flex justify-between mt-4 mb-4" style="margin-top: 50px">
                                <button type="button" class="block h-12 mx-6 text-white bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" data-modal-hide="upload-modal">Cancel</button>
                                <button type="submit" class="block h-12 mx-6 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Upload</button>
                            </div>
                        </form>
                    </div>
                    </div>
                  </div></div></div>';

echo '<div class=" container mx-auto mt-4 relative overflow-x-auto shadow-md sm:rounded-lg">

            <h1 class="text-lg px-6 font-bold mb-4 ml-2">Costa Smeralda Legacy Articles</h1>

            <div class="flex justify-between">
              <div class="pb-4 px-6 bg-white dark:bg-gray-900">
                <label for="table-search" class="sr-only">Search</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" id="table-search" class="block pt-2 pb-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
                </div>
              </div>

              <div class="flex">
                <div id="convertBtn" class="block h-12 mx-6 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800" type="button">
                    Convert
                </div>
                <div id="createJsonBtn" class="block h-12 mx-6 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800" type="button">
                    Create Json
                </div>
                <div data-modal-target="upload-modal" data-modal-toggle="upload-modal"  class="block h-12 mx-6 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                    Xml Read
                </div>
              </div>
            </div>

            <table class="w-full text-sm text-left rtl:text-right te xt-gray-500 dark:text-gray-400">
              <thead class="text-ms text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b-2 dark:border-red-700 h-16">
                  <tr>
                      <th scope="col" class="px-6 py-3">
                          
                      </th>
                      <th scope="col" class="px-6 py-3">
                          Article Title
                      </th>
                      <th scope="col" class="px-3 py-3">
                          <div class="flex items-center">
                              Category
                              <a href="#"><svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                  <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                </svg>
                              </a>
                          </div>
                      </th>

                      <th scope="col" class="px-3 py-3">
                          <div class="flex items-center">
                             New Category
                              <a href="#"><svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                  <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                </svg>
                              </a>
                          </div>
                      </th>
                      <th scope="col" class="px-3 py-3" style="width: 100px;">
                          <div class="flex items-center">
                              Date
                              <a href="#"><svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                              </svg>
                              </a>
                          </div>
                      </th>
                      <th scope="col" class="px-3 py-3">
                          <div class="flex items-center">
                              Actions
                              <a href="#"><svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                              </svg></a>
                          </div>
                      </th>
                  </tr>
              </thead>
            <tbody>';
while ($row = $result->fetch_assoc()) {
    echo '<tr class="bg-white border-b-2 dark:bg-gray-800 dark:border-gray-700">';
    echo '<td class="px-6 py-3"><input id="checkbox-' . $row['id'] . '" type="checkbox"  value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"></td>';
    echo '<th scope="row" class="px-6 py-3 font-medium text-gray-900 dark:text-white w-4/5"><a class="text-blue-500 underline hover:text-blue-700 hover:no-underline" target="_blank" href="' . $row['slug'] . '">' . $row['title'] . '</a></th>';

    echo '<td class="px-3 py-3">' . $row['category'] . '</td>';
    echo '<td class="px-3 py-3">' . $row['category_new'] . '</td>';

    echo '<td class="px-3 py-3" style="width: 100px;">' . $row['date'] . '</td>';

    echo '<td class="px-3 py-3"><a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
          <button data-modal-target="default-modal' . '-' . $row['id'] . '" data-modal-toggle="default-modal' . '-' . $row['id'] . '" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
            view
          </button>
          </a></td>';
    echo '</tr>';
}
echo '</tbody></table>';

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo '
      <div id="default-modal' . '-' . $row['id'] . '" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full h-full bg-opacity-60 bg-gray-500 dark:bg-gray-700">
          <div class="relative w-full max-w-4xl w-3/4 max-h-full m-4 p-4">
              <!-- Modal content -->
              <div class="relative bg-white rounded-lg dark:bg-gray-700 border border-blue-500">
                  <!-- Modal header -->
                  <div class="flex items-center justify-between p-4 md:p-5">
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal' . '-' . $row['id'] . '">
                          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                          </svg>
                      </button>
                  </div>
                  <!-- Modal body -->';
    echo '<div class="p-6 pt-0 flex justify-between self-center bg-white rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 m-auto overflow-y-auto">
                <div class="w-2/5 mr-4">
                  <div><strong>'
        . $row['title'] .
        '</strong></div>
                  <div class="mt-4">'
        . $row['category'] .
        '</div>
                  <div class="mt-4">'
        . $row['date'] .
        '</div>
                  <img class="rounded-lg mt-4" src="' . $row['image_url'] . '" alt="' . $row['image_text'] . '">
                </div>
                <div class="w-3/5" style="max-height: 80vh">
                  <div>
                    <strong>Excerpt:</strong><p>'

        . $row['excerpt'] .
        '</p></div>
                  <div class="mt-4">
                    <strong>Full text:</strong>'
        . $row['full_text'] .
        '</div>
                </div>
              </div>';
    echo '</div>
          </div>
      </div>';
}

echo '</div>';

// Pagination links
$sql = "SELECT COUNT(*) AS total FROM article ";

if ($search_query) {
    $sql = "SELECT COUNT(*) AS total FROM article WHERE title LIKE '%$search_query%'";
}
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalRecords = $row["total"];
$totalPages = ceil($totalRecords / $recordsPerPage);

echo '<nav class="flex items-center flex-column flex-wrap md:flex-row justify-evenly pt-4" aria-label="Table navigation">';
echo '<span class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing <span class="font-semibold text-gray-900 dark:text-white">' . (($currentPage - 1) * $recordsPerPage + 1) . '-' . min($currentPage * $recordsPerPage, $totalRecords) . '</span> of <span class="font-semibold text-gray-900 dark:text-white">' . $totalRecords . '</span></span>';
echo '<ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">';

if ($currentPage > 1) {
    echo '<li><a href="?page=' . ($currentPage - 1) . '" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a></li>';
}

$startPage = max(1, $currentPage - 3);
$endPage = min($totalPages, $currentPage + 3);

for ($i = $startPage; $i <= $endPage; $i++) {
    if ($i == $currentPage) {
        echo '<li><a href="?page=' . $i . '" aria-current="page" class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">' . $i . '</a></li>';
    } else {
        echo '<li><a href="?page=' . $i . '" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">' . $i . '</a></li>';
    }
}

if ($currentPage < $totalPages) {
    echo '<li><a href="?page=' . ($currentPage + 1) . '" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next</a></li>';
}
echo '</ul>';
echo '</nav>';

echo '</body>
    <script src="./dist/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/contentful@latest/dist/contentful.browser.min.js"></script>
    </html>';

// Close the connection
$conn->close();
?>

