<?php
/**
 * FabX ERP - Database Class
 * Singleton pattern with prepared statements
 */

namespace Core;

use mysqli;
use Exception;

class Database {
    private static ?self $instance = null;
    private mysqli $connection;
    private string $prefix;

    private function __construct() {
        $this->prefix = DB_PREFIX;
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
        
        $this->connection->set_charset(DB_CHARSET);
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
        return $this->connection;
    }

    public function getPrefix(): string {
        return $this->prefix;
    }

    /**
     * Execute prepared query
     */
    public function query(string $sql, array $params = []): \mysqli_stmt|false {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            error_log("SQL Error: " . $this->connection->error . " | Query: " . $sql);
            return false;
        }

        if (!empty($params)) {
            $types = '';
            $values = [];

            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_bool($param)) {
                    $types .= 'i';
                    $param = $param ? 1 : 0;
                } else {
                    $types .= 's';
                }
                $values[] = $param;
            }

            $stmt->bind_param($types, ...$values);
        }

        return $stmt;
    }

    /**
     * Execute and get affected rows
     */
    public function execute(string $sql, array $params = []): int {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return 0;
        
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    /**
     * Fetch single row
     */
    public function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return null;
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ?: null;
    }

    /**
     * Fetch all rows
     */
    public function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return [];
        
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $rows;
    }

    /**
     * Fetch single value
     */
    public function fetchValue(string $sql, array $params = []) {
        $row = $this->fetchOne($sql, $params);
        return $row ? array_values($row)[0] : null;
    }

    /**
     * Insert and get last ID
     */
    public function insert(string $sql, array $params = []): int {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return 0;
        
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): void {
        $this->connection->begin_transaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): void {
        $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): void {
        $this->connection->rollback();
    }

    /**
     * Get last error
     */
    public function getError(): string {
        return $this->connection->error;
    }

    /**
     * Escape string
     */
    public function escape(string $string): string {
        return $this->connection->real_escape_string($string);
    }

    /**
     * Get table name with prefix
     */
    public function table(string $name): string {
        return $this->prefix . $name;
    }

    public function __destruct() {
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }
}
