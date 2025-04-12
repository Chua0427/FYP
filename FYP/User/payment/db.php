<?php
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'verosports';
    private $conn;
    private $transactionActive = false;
    private $lastError = '';

    /**
     * Constructor - Connect to database
     * @throws Exception If connection fails
     */
    public function __construct() {
        try {
            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->database
            );

            if ($this->conn->connect_error) {
                $this->logError("Database Connection Failed: " . $this->conn->connect_error);
                throw new Exception("Database connection error: " . $this->conn->connect_error);
            }

            // Set UTF-8 charset
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            $this->logError("Database Connection Exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Execute a query with parameters
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return int|false Number of affected rows or false on failure
     * @throws Exception If query execution fails
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->prepareAndBind($sql, $params);
            if (!$stmt) {
                return false;
            }

            $stmt->execute();
            
            if ($stmt->error) {
                $this->lastError = $stmt->error;
                $this->logError("SQL Execute Error: " . $stmt->error . " in query: $sql");
                
                if ($this->transactionActive) {
                    $this->rollback();
                }
                
                throw new Exception("SQL execution failed: " . $stmt->error);
            }

            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            return $affectedRows;
        } catch (Exception $e) {
            $this->logError("Execute Exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch all rows from a query
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return array Result set as associative array
     * @throws Exception If query execution fails
     */
    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->prepareAndBind($sql, $params);
            if (!$stmt) {
                return [];
            }

            $stmt->execute();
            
            if ($stmt->error) {
                $this->lastError = $stmt->error;
                $this->logError("SQL FetchAll Error: " . $stmt->error . " in query: $sql");
                throw new Exception("SQL query failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $data;
        } catch (Exception $e) {
            $this->logError("FetchAll Exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch a single row from a query
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return array|null Single row as associative array or null if no results
     * @throws Exception If query execution fails
     */
    public function fetchOne($sql, $params = []) {
        try {
            $stmt = $this->prepareAndBind($sql, $params);
            if (!$stmt) {
                return null;
            }

            $stmt->execute();
            
            if ($stmt->error) {
                $this->lastError = $stmt->error;
                $this->logError("SQL FetchOne Error: " . $stmt->error . " in query: $sql");
                throw new Exception("SQL query failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data;
        } catch (Exception $e) {
            $this->logError("FetchOne Exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Begin a transaction
     * 
     * @return bool Success
     */
    public function beginTransaction() {
        if ($this->transactionActive) {
            return false; // Transaction already active
        }
        
        $this->conn->begin_transaction();
        $this->transactionActive = true;
        return true;
    }

    /**
     * Commit a transaction
     * 
     * @return bool Success
     */
    public function commit() {
        if (!$this->transactionActive) {
            return false;
        }
        
        $result = $this->conn->commit();
        $this->transactionActive = false;
        return $result;
    }

    /**
     * Rollback a transaction
     * 
     * @return bool Success
     */
    public function rollback() {
        if (!$this->transactionActive) {
            return false;
        }
        
        $result = $this->conn->rollback();
        $this->transactionActive = false;
        return $result;
    }

    /**
     * Check if a transaction is active
     * 
     * @return bool Is transaction active
     */
    public function isTransactionActive() {
        return $this->transactionActive;
    }

    /**
     * Get the last error message
     * 
     * @return string Last error message
     */
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Prepare and bind parameters to a statement
     * 
     * @param string $sql SQL query
     * @param array $params Parameters to bind
     * @return mysqli_stmt|false Prepared statement or false on failure
     */
    private function prepareAndBind($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            $this->lastError = $this->conn->error;
            $this->logError("SQL Prepare Failed: " . $this->conn->error . " in query: $sql");
            return false;
        }

        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        return $stmt;
    }

    /**
     * Get parameter types for bind_param
     * 
     * @param array $params Parameters to analyze
     * @return string Type string (i, d, s)
     */
    private function getParamTypes($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) $types .= 'i'; 
            elseif (is_float($param)) $types .= 'd'; 
            elseif (is_null($param)) $types .= 's'; // Treat NULL as string
            else $types .= 's';
        }
        return $types;
    }

    /**
     * Log an error message
     * 
     * @param string $message Error message
     */
    private function logError($message) {
        $log = date("[Y-m-d H:i:s]") . " [DB_ERROR] " . $message . PHP_EOL;
        error_log($log, 3, __DIR__ . '/logs/db_errors.log');
    }

    /**
     * Close the database connection
     */
    public function close() {
        if ($this->transactionActive) {
            $this->rollback();
        }
        $this->conn->close();
    }

    /**
     * Destructor - ensure connection is closed
     */
    public function __destruct() {
        if ($this->conn && !$this->conn->connect_error) {
            $this->close();
        }
    }
}
?>
