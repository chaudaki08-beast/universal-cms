<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Thin PDO singleton + query helpers. All statements are prepared
 * (SQL-injection safe). Use placeholders, never string concatenation.
 */
class Database
{
    protected static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            defined('DB_PORT') ? DB_PORT : '3306',
            DB_NAME,
            defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'
        );

        try {
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                throw $e;
            }
            http_response_code(500);
            exit('Database connection failed.');
        }

        return self::$pdo;
    }

    /** Run a prepared statement and return the PDOStatement. */
    public static function run(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Fetch a single row (or null). */
    public static function first(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Fetch all rows. */
    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /** Fetch a single scalar value. */
    public static function scalar(string $sql, array $params = [])
    {
        return self::run($sql, $params)->fetchColumn();
    }

    public static function lastInsertId(): string
    {
        return self::connection()->lastInsertId();
    }

    /** Convenience INSERT. Returns the new id. */
    public static function insert(string $table, array $data): string
    {
        $cols = array_keys($data);
        $ph   = array_map(fn($c) => ':' . $c, $cols);
        $sql  = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $table,
            implode('`,`', $cols),
            implode(',', $ph)
        );
        self::run($sql, $data);
        return self::lastInsertId();
    }

    /** Convenience UPDATE by id (or custom where). */
    public static function update(string $table, array $data, array $where): int
    {
        $set = implode(',', array_map(fn($c) => "`$c`=:s_$c", array_keys($data)));
        $wh  = implode(' AND ', array_map(fn($c) => "`$c`=:w_$c", array_keys($where)));

        $params = [];
        foreach ($data as $k => $v)  { $params["s_$k"] = $v; }
        foreach ($where as $k => $v) { $params["w_$k"] = $v; }

        $sql = "UPDATE `$table` SET $set WHERE $wh";
        return self::run($sql, $params)->rowCount();
    }

    public static function delete(string $table, array $where): int
    {
        $wh = implode(' AND ', array_map(fn($c) => "`$c`=:$c", array_keys($where)));
        return self::run("DELETE FROM `$table` WHERE $wh", $where)->rowCount();
    }
}
