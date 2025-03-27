<?php
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'verosports';
    private $conn;

    public function __construct() {

        $this->conn = new mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->database
        );


        if ($this->conn->connect_error) {
            error_log("Database Connection Failed: " . $this->conn->connect_error);
            throw new Exception("Database connection error");
        }
    }

    public function execute($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("SQL Prepare Failed: " . $this->conn->error);
            return false;
        }

        if (!empty($params)) {
            $types = $this->get_param_types($params);
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        if ($stmt->error) {
            error_log("SQL Execute Error: " . $stmt->error);
            return false;
        }

        return $stmt->affected_rows;
    }


    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 查询单行数据
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->get_result()->fetch_assoc() : null;
    }

    // 处理 SELECT 查询
    private function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("SQL Prepare Failed: " . $this->conn->error);
            return false;
        }

        if (!empty($params)) {
            $types = $this->get_param_types($params);
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt;
    }

    // 根据参数类型自动绑定 SQL 语句
    private function get_param_types($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) $types .= 'i'; // 整数类型
            elseif (is_float($param)) $types .= 'd'; // 浮点数
            else $types .= 's'; // 字符串类型
        }
        return $types;
    }

    public function close() {
        $this->conn->close();
    }
}
?>
