<?php

class Database {
    private $host = "localhost:3307";
    private $username = "root";
    private $password = "";
    private $database = "xmls";
    public $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function query($sql) {
        $result = $this->conn->query($sql);

        if (!$result) {
            die("Query failed: " . $this->conn->error);
        }

        return $result;
    }

    public function fetch_all($sql) {
        $result = $this->query($sql);
        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    public function fetch_one($sql) {
        $result = $this->query($sql);
        return $result->fetch_assoc();
    }

    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";

        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $this->query($sql);

        return $this->conn->insert_id;
    }

    public function update($table, $data, $where) {
        $set = array();
        foreach ($data as $key => $value) {
            $set[] = "$key = '$value'";
        }
        $set = implode(", ", $set);

        $sql = "UPDATE $table SET $set WHERE $where";
        $this->query($sql);

        return $this->conn->affected_rows;
    }

    public function delete($table, $where) {
        $sql = "DELETE FROM $table WHERE $where";
        $this->query($sql);

        return $this->conn->affected_rows;
    }
}
