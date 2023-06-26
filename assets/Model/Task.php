<?php

namespace LucasAlbuquerque\LoginSystem\Model;

use LucasAlbuquerque\LoginSystem\Infrastructure\DatabaseConnection;
use DateTime;
use DateTimeZone;
use PDO;

class Task
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = DatabaseConnection::connect();
    }

    public function getAllTasks()
    {
        $statement = $this->connection->query("SELECT * FROM tasks;");
        return $statement->fetchAll();
    }

    public function create(
    $name,
    $description,
    $initStatus,
    $creationDate,
    $initDate,
    $conclusionDate
    )
    {
        $queryInsert = "INSERT into tasks(task_name, task_description, task_creation_date, task_init_date, task_conclusion_date, task_status_id)
        VALUES (:task_name , :task_description, :creationDate, :initDate, :conclusionDate, :statusCode)";
        $statement = $this->connection->prepare($queryInsert);
        $statement->bindValue(':task_name', $name);
        $statement->bindValue(':task_description', $description);
        $statement->bindValue(':creationDate', $creationDate);
        $statement->bindValue(':initDate', $initDate);
        $statement->bindValue(':conclusionDate', $conclusionDate);
        $statement->bindValue(':statusCode', $initStatus);
        $statement->execute();
    }

    public function delete($id)
    {
        $queryDelete = "DELETE FROM tasks WHERE task_id = :id";
        $statement = $this->connection->prepare($queryDelete);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function setStatus(int $id, $statusCode): void
    {
        $queryUpdate = "UPDATE tasks SET task_status_id = :status WHERE task_id = :id";

    }

    public function updateText($id, $column, $value)
    {
        $query = "UPDATE tasks SET $column = :value WHERE task_id = :id";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->bindValue(':value', $value);
        $statement->execute();
    }
    // public function update($id, $data)
    // {
    //     $formattedColumns = $this->getFormattedUpdateColumns($data);
    //     $queryUpdate = "UPDATE tasks SET $formattedColumns WHERE task_id = :id";
    //     $statement = $this->connection->prepare($queryUpdate);
    //     foreach($data as $key => $value) {
    //         $statement->bindValue(":$key", $value);
    //     }

    //     $statement->bindValue(":id", $id);
    //     $statement->execute();
    // }

    public function getTaskStatus($taskId)
    {
        $querySelect = "SELECT ts.task_status_name
        FROM tasks t
        JOIN task_status ts ON t.task_status_id = ts.task_status_id
        WHERE t.task_id = :taskId";        
    
        $statement = $this->connection->prepare($querySelect);
        $statement->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        return $result[0]['task_status_name'];
    }

    private function getDateTime()
    {
        $now = new DateTime('now');
        $now->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $dateTime = $now->format('Y-m-d H:i:s');
        return $dateTime;
    }

    private function getFormattedUpdateColumns($data)
    {
        $formattedColumns = array_map(function($key) {
            return "$key = ?";
        }, array_keys($data));
        
        return implode(', ', $formattedColumns);
    }

}