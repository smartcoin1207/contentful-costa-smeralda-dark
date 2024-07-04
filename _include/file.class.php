<?php

class FileHandler
{
    public static function fileUpload()
    {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['file'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = $file['type'];

            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($fileTmpName, $uploadFile)) {
                return $uploadFile;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function fileReadToDatabase($cmd = '')
    {
        $db = new Database();

        $file_path = self::fileUpload();        
        
        try {

        if (!($xml = simplexml_load_file($file_path))) {
            return false;
        }
            foreach ($xml->channel->item as $item) {
                $title = (string) $item->title;
                $link = (string) $item->link;
                $pubDate = (string) $item->pubDate;
                $content = (string) $item->children('content', true)->encoded;
                $main_content = preg_replace('/\[[^\]]+\]/', '', $content);
                $excerpt = (string) $item->children('excerpt', true)->encoded;
                $categories = $item->category;

                $category_name = '';
                foreach ($categories as $category) {
                    if($category['domain'] == 'category') 
                    {
                        $category_name = (string) $category;
                        break;
                    }
                }

                $category = $category_name;
                $image_text = '';
                $image_url = '';

                try {
                    // get image alt text and image url from wp-post _bes_post_meta value
                    foreach ($item->children('wp', true)->postmeta as $key => $value) {
                        if (((string) $value->meta_key) == '_b2s_post_meta') {
                            $b2s_post_meta = (string) $value->meta_value;
                            $data = unserialize($b2s_post_meta);
                            $image_text = $data['og_title'];
                            $image_url = $data['card_image'];
                            break;
                        }
                    }
                } catch (\Throwable $th) {
                    // echo 'err';
                }

                $posts[] = array(
                    'title' => $title,
                    'slug' => $link,
                    'date' => $pubDate,
                    'full_text' => $main_content,
                    'excerpt' => $excerpt,
                    'category' => $category,
                    'image_text' => $image_text,
                    'image_url' => $image_url,
                );
            }
        } catch (\Throwable $th) {
            // throw $th;
        }

        // Create the SQL table if it doesn't exist
            $sql = "CREATE TABLE IF NOT EXISTS article (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255),
            slug VARCHAR(255),
            date date,
            full_text text,
            excerpt text,
            category VARCHAR(20),
            image_text VARCHAR(255),
            image_url VARCHAR(255)
        )";

        if ($db->query($sql) === false) {
            echo "Error creating table: " . $db->conn->error;
        }

        if($cmd != 'put') {
            //delete exist articles
            $delete_sql = "DELETE FROM article;";
            $stmt = $db->conn->prepare($delete_sql);

            if ($stmt->execute()) {
                echo "All records deleted successfully";
            } else {
                echo "Error deleting records: " . $stmt->error;
            }
        }
        
        // Insert the data from the XML file into the database table
        foreach ($posts as $post) {
            $title = isset($post['title']) ? $db->conn->real_escape_string($post['title']) : '';
            $slug = isset($post['slug']) ? $db->conn->real_escape_string($post['slug']) : '';
            $date_string = isset($post['date']) ? $post['date'] : '';
            $date = date("Y-m-d", strtotime($date_string));
            $full_text = isset($post['full_text']) ? $db->conn->real_escape_string(str_replace(array("\n", "\r", "\t"), '<br>', $post['full_text'])) : '';
            $excerpt = isset($post['excerpt']) ? $db->conn->real_escape_string(str_replace(array("\n"), '', $post['excerpt'])) : '';
            $category = isset($post['category']) ? $db->conn->real_escape_string($post['category']) : '';
            // echo $category;
            $image_text = isset($post['image_text']) ? $db->conn->real_escape_string($post['image_text']) : '';
            $image_url = isset($post['image_url']) ? $db->conn->real_escape_string($post['image_url']) : '';

            $sql = "INSERT INTO article (title, slug, date, full_text, excerpt, category, image_text, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->conn->prepare($sql);

            if ($stmt->bind_param("ssssssss", $title, $slug, $date, $full_text, $excerpt, $category, $image_text, $image_url)) {
                if ($stmt->execute()) {
                    // echo "Data inserted successfully.";
                } else {
                    echo "Error inserting data: " . $stmt->error;
                }
            } else {
                echo "Error preparing statement: " . $db->conn->error;
            }

            $stmt->close();
        }
        
        // Close the database connection
        $db->conn->close();
    }
}
