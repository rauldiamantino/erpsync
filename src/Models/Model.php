<?php

namespace App\Models;

use PDO;
use App\Classes\Database;

abstract class Model
{
  protected string $table;
  protected PDO $database;

  public function __construct()
  {
    $this->database = Database::getInstance()->getConnection();
  }

  public function setTable(string $table): void
  {
    $this->table = $table;
  }

  protected function checkTable(): void
  {
    if (empty($this->table)) {
      throw new RuntimeException('Table name is not set.');
    }
  }

  public function find(int $id, array $columns = ['*']): ?array
  {
    $this->checkTable();

    $columnsList = implode(', ', $columns);

    $sql = <<<SQL
  SELECT {$columnsList} FROM {$this->table} WHERE id = :id LIMIT 1
  SQL;

    $data = [':id' => (int) $id];
    $stmt = $this->database->prepare($sql);
    $stmt->execute($data);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $result = $stmt->fetch();

    return $result !== false ? $result : null;
  }


  public function findBy(string $field, mixed $value, int $limit = 1): ?array
  {
    $this->checkTable();

    $sql = <<<SQL
  SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT {$limit}
  SQL;

    $data = [':value' => $value];
    $stmt = $this->database->prepare($sql);
    $stmt->execute($data);
    $stmt->setFetchMode(\PDO::FETCH_ASSOC);

    if ($limit > 1) {
      $result = $stmt->fetchAll();
    }
    else {
      $result = $stmt->fetch();
    }

    if ($result === false) {
      return null;
    }

    return $result;
  }

  public function all(string $sortDirection = 'ASC', array $columns = ['*']): array
  {
    $this->checkTable();

    $sortDirection = strtoupper($sortDirection);

    if (! in_array($sortDirection, ['ASC', 'DESC'])) {
      $sortDirection = 'ASC';
    }

    $columnsList = implode(', ', $columns);

    $sql = <<<SQL
  SELECT {$columnsList} FROM {$this->table} ORDER BY `id` {$sortDirection}
  SQL;

    $stmt = $this->database->query($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    return $stmt->fetchAll();
  }

  public function allPagination(int $page, int $perPage, array $columns = ['*'], string $sortDirection = 'ASC'): array
  {
    $this->checkTable();

    $sortDirection = strtoupper($sortDirection);

    if (! in_array($sortDirection, ['ASC', 'DESC'])) {
      $sortDirection = 'ASC';
    }

    $columnsList = implode(', ', $columns);

    $offset = ($page - 1) * $perPage;

    $sql = <<<SQL
  SELECT {$columnsList} FROM {$this->table} ORDER BY `id` {$sortDirection} LIMIT {$offset}, {$perPage}
  SQL;

    $stmt = $this->database->query($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    return $stmt->fetchAll();
  }

  public function count(): int
  {
    $this->checkTable();

    $sql = <<<SQL
  SELECT COUNT(*) AS total FROM {$this->table};
  SQL;

    $stmt = $this->database->query($sql);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int) $resultado['total'];
  }

  public function create(array $data): bool
  {
    $this->checkTable();

    $fields = [];
    $placeholders = [];

    foreach ($data as $key => $value):
      $fields[] = $key;
      $placeholders[] = ':' . $key;
    endforeach;

    $fieldsString = implode(', ', $fields);
    $placeholdersString = implode(', ', $placeholders);

    $sql = <<<SQL
  INSERT INTO {$this->table} ({$fieldsString}) VALUES ({$placeholdersString})
  SQL;

    $stmt = $this->database->prepare($sql);

    return $stmt->execute($data);
  }

  public function createOrUpdate(array $data): bool
  {
    $this->checkTable();

    $fields = [];
    $placeholders = [];
    $updateFields = [];

    foreach ($data as $key => $value):
      $fields[] = $key;
      $placeholders[] = ':' . $key;

      // Avoid updating primary key
      if ($key !== 'id') {
        $updateFields[] = $key . '= VALUES(' . $key . ')';
      }
    endforeach;

    $fieldsString = implode(', ', $fields);
    $placeholdersString = implode(', ', $placeholders);
    $updateString = implode(', ', $updateFields);

    $sql = <<<SQL
  INSERT INTO {$this->table} ({$fieldsString})
  VALUES ({$placeholdersString})
  ON DUPLICATE KEY UPDATE {$updateString}
  SQL;

    $stmt = $this->database->prepare($sql);
    return $stmt->execute($data);
  }

  public function update(int $id, array $data): bool
  {
    $this->checkTable();

    $setParts = [];
    foreach ($data as $key => $value):
      $setParts[] = $key . ' = :' . $key;
    endforeach;

    $set = implode(', ', $setParts);

    $sql = <<<SQL
  UPDATE {$this->table} SET $set WHERE id = :id
  SQL;

    $data['id'] = $id;
    $stmt = $this->database->prepare($sql);

    return $stmt->execute($data);
  }

  public function delete(int $id): bool
  {
    $this->checkTable();

    $sql = <<<SQL
  DELETE FROM {$this->table} WHERE id = :id
  SQL;

    $data = [':id' => $id];
    $stmt = $this->database->prepare($sql);

    return $stmt->execute($data);
  }

  /**
  * Executes any SQL query with optional parameters
  * Use with attention: this method executes raw SQL queries directly
  *
  * @param string $sql The SQL query to execute
  * @param array $params Optional associative array of parameters to bind
  *
  * @return mixed Returns an associative array of results for SELECT queries,
  *               true for successful non-SELECT queries, or false on failure
  *
  * @throws RuntimeException If the query preparation fails.
  */
  public function executeQuery(string $sql, array $params = []): mixed
  {
    $this->checkTable();

    $stmt = $this->database->prepare($sql);

    if (empty($stmt)) {
      throw new RuntimeException('Failed to prepare statement');
    }

    $success = $stmt->execute($params);

    if (empty($success)) {
      return false;
    }

    if (stripos(trim($sql), 'SELECT') === 0) {
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return true;
  }
}