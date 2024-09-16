<?php

namespace PHPageBuilder\Core;

use PDO;
use PDOException;

class DB
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * DB constructor.
     *
     * @param array $config
     * @throws PDOException
     */
    public function __construct(array $config)
    {
        try {
            $dsn = sprintf(
                "%s:host=%s;dbname=%s;options='--client_encoding=%s'",
                $config['driver'],
                $config['host'],
                $config['database'],
                $config['charset']
            );

            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            $this->pdo->exec("SET NAMES " . $config['charset']);
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Return the ID of the last inserted record.
     *
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Return all records from the specified table.
     *
     * @param string       $table
     * @param string|array $columns
     * @return array
     */
    public function all(string $table, $columns = '*'): array
    {
        $columns = is_array($columns) ? $this->sanitizeColumns($columns) : '*';
        $query = sprintf("SELECT %s FROM %s", $columns, $this->sanitizeIdentifier($table));
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find a record by its ID.
     *
     * @param string $table
     * @param mixed  $id
     * @return array|null
     */
    public function findWithId(string $table, $id): ?array
    {
        $query = sprintf("SELECT * FROM %s WHERE id = ?", $this->sanitizeIdentifier($table));
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetchAll();
        return $result ?: null;
    }

    /**
     * Execute a custom select query with parameters.
     *
     * @param string $query
     * @param array  $parameters
     * @return array
     */
    public function select(string $query, array $parameters = []): array
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    /**
     * Execute a custom query with optional parameters.
     *
     * @param string $query
     * @param array  $parameters
     * @return bool
     */
    public function query(string $query, array $parameters = []): bool
    {
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($parameters);
    }

    /**
     * Sanitize column names to prevent SQL injection.
     *
     * @param array $columns
     * @return string
     */
    private function sanitizeColumns(array $columns): string
    {
        return implode(',', array_map([$this, 'sanitizeIdentifier'], $columns));
    }

    /**
     * Sanitize identifiers like table or column names.
     *
     * @param string $identifier
     * @return string
     */
    private function sanitizeIdentifier(string $identifier): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $identifier);
    }
}