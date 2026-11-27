<?php

function crud_config(string $table): array
{
    if ($table === 'Role') {
        return [
            'id' => 'role_id',
            'fields' => ['role_name', 'description']
        ];
    }

    require_once __DIR__ . '/entities.php';

    foreach (entities() as $entity) {
        if ($entity['table'] === $table) {
            return [
                'id' => $entity['id'],
                'fields' => array_keys($entity['fields'])
            ];
        }
    }

    throw new InvalidArgumentException(
        'Недозволена таблиця.'
    );
}

function find_record(PDO $pdo, string $table, int $id)
{
    $config = crud_config($table);
    $pk = $config['id'];

    $stmt = $pdo->prepare(
        "SELECT * FROM `$table` WHERE `$pk` = :id"
    );

    $stmt->execute(['id' => $id]);

    return $stmt->fetch();
}

function insert_record(PDO $pdo, string $table, array $data): void
{
    $config = crud_config($table);
    $fields = $config['fields'];

    $columns = implode(', ', $fields);
    $placeholders = ':' . implode(', :', $fields);

    $stmt = $pdo->prepare(
        "INSERT INTO `$table` ($columns)
         VALUES ($placeholders)"
    );

    $stmt->execute($data);
}

function update_record(
    PDO $pdo,
    string $table,
    int $id,
    array $data
): void {
    $config = crud_config($table);
    $pk = $config['id'];
    $fields = $config['fields'];

    $parts = [];

    foreach ($fields as $field) {
        $parts[] = "$field = :$field";
    }

    $set = implode(', ', $parts);

    $stmt = $pdo->prepare(
        "UPDATE `$table` SET $set WHERE `$pk` = :id"
    );

    $data['id'] = $id;

    $stmt->execute($data);
}

function delete_record(PDO $pdo, string $table, int $id): bool
{
    $config = crud_config($table);
    $pk = $config['id'];

    $stmt = $pdo->prepare(
        "DELETE FROM `$table` WHERE `$pk` = :id"
    );

    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
}
