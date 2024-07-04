<?php

class JSONClass {
    public static function createJson()
    {
        $db = new Database();
        $conn = $db->conn;

        $sql = "SELECT * FROM article";
        $result = $conn->query($sql);

        // Check if there are any results
        if ($result->num_rows > 0) {
            // Fetch all rows at once
            $rows = $result->fetch_all(MYSQLI_ASSOC);

            $allDatas = [];
            // Process the data

            foreach ($rows as $row) {
               $data = array(
                'id' => $row['id'],
                'slugUrl' => basename($row['slug'], "/"),
                'title' => $row['title'],
                'coverURL' => $row['image_url'],
                'coverAlt' => $row['image_text'],
                'coverLegacy' => false,
                'coverType' => 'image',
                'thumbFocus' => 'faces',
                'articleType' => 'Article',
                'contentType' => 'article',
                'updatedAt' => $row['date'],
                'content' => $row['full_text']
               );

               $allDatas[] = $data;
            }

            // $json_data = array(
            //     "Articles" => array(
            //         "old" => $allDatas
            //     )
            // );

            $categorynew_sql = "SELECT DISTINCT category_new FROM categories;";
            $category_result = $conn->query($categorynew_sql);
            $categories = $category_result->fetch_all(MYSQLI_ASSOC);

            $allDatas = array();
            
            foreach ($categories as $key => $category) {
                $category = $category['category_new'];
                $sql = "SELECT a.* FROM article AS a LEFT JOIN categories as ca ON a.category=ca.category WHERE ca.category_new='" . $category . "'";
                $result = $conn->query($sql);
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                $datas = [];
                // Process the data

                foreach ($rows as $row) {
                    $data = array(
                        'id' => $row['id'],
                        'slugUrl' => basename($row['slug'], "/"),
                        'title' => $row['title'],
                        'coverURL' => $row['image_url'],
                        'coverAlt' => $row['image_text'],
                        'coverLegacy' => false,
                        'coverType' => 'image',
                        'thumbFocus' => 'faces',
                        'articleType' => 'Article',
                        'contentType' => 'article',
                        'updatedAt' => $row['date'],
                        'content' => $row['full_text']
                    );

                    $datas[] = $data;
                }

                $allDatas[$category] = $datas;
            }

            $nocategory_sql = "SELECT a.* FROM article a LEFT JOIN categories c ON a.category = c.category WHERE c.category IS NULL";
            $result = $conn->query($nocategory_sql);
            $rows = $result->fetch_all(MYSQLI_ASSOC);

            $datas = [];
            foreach ($rows as $row) {
                $data = array(
                    'id' => $row['id'],
                    'slugUrl' => basename($row['slug'], "/"),
                    'title' => $row['title'],
                    'coverURL' => $row['image_url'],
                    'coverAlt' => $row['image_text'],
                    'coverLegacy' => false,
                    'coverType' => 'image',
                    'thumbFocus' => 'faces',
                    'articleType' => 'Article',
                    'contentType' => 'article',
                    'updatedAt' => $row['date'],
                    'content' => $row['full_text']
                );

                $datas[] = $data;
            }

            $allDatas['No_category'] = $datas;

            $json_data = array(
                "Articles" => $allDatas
            );
            
            /**--------------------------------------- */

            try {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $json = json_encode($json_data, JSON_PRETTY_PRINT);
                $current_timestamp = time();
                $filename = "./uploads/data.json";
                file_put_contents($filename, $json);

                // Set the appropriate headers for file download
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
                echo $json;
            } catch (\Throwable $th) {
                echo "Error was occoured";
            }
        } else {
            echo "No records found.";
        }

        // Close the connection
        $conn->close();
    }
}