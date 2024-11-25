<?php
namespace App\Repository;
use App\Service\Sql\Sql;
use PDO;

/**
 *
 */
class EventsRepository
{

	function __construct(private Sql $sql)
	{

	}

    public function getEventsByMonth(int $year, int $month): array
    {
        $startDate = "{$year}-{$month}-01 00:00:00";
        $endDate = "{$year}-{$month}-" . date('t', strtotime($startDate)) . " 23:59:59";

        $query = 'SELECT id, title, description, start_datetime, end_datetime, created_at, updated_at, deleted_at
                  FROM events
                  WHERE deleted_at IS NULL
                  AND start_datetime BETWEEN :startDate AND :endDate
                  ORDER BY start_datetime';

        $stmt = $this->sql->prepare($query);
        $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function createEvent(array $eventData): ?int
    {
        try {
            $query = 'INSERT INTO events (title, description, start_datetime, end_datetime, created_at, updated_at)
                      VALUES (:title, :description, :start_datetime, :end_datetime, NOW(), NOW())';

            $stmt = $this->sql->prepare($query);
            $stmt->execute([
                ':title' => $eventData['title'],
                ':description' => $eventData['description'],
                ':start_datetime' => $eventData['start_datetime'],
                ':end_datetime' => $eventData['end_datetime']
            ]);

            return (int)$this->sql->lastInsertId();
        } catch (\PDOException $e) {
            error_log('Erro ao criar evento: ' . $e->getMessage());
            return null;
        }
    }

    public function updateEvent(int $eventId, array $eventData): bool
    {
        try {
            $query = 'UPDATE events 
                      SET title = :title,
                          description = :description,
                          start_datetime = :start_datetime,
                          end_datetime = :end_datetime,
                          updated_at = NOW()
                      WHERE id = :id';

            $stmt = $this->sql->prepare($query);
            $stmt->execute([
                ':title' => $eventData['title'],
                ':description' => $eventData['description'],
                ':start_datetime' => $eventData['start_datetime'],
                ':end_datetime' => $eventData['end_datetime'],
                ':id' => $eventId
            ]);

            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log('Erro ao atualizar evento: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteEvent(int $eventId): bool
    {
        $query = 'SELECT id FROM events WHERE id = :eventId AND deleted_at IS NULL';
        $stmt = $this->sql->prepare($query);
        $stmt->bindValue(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();

        $event = $stmt->fetch();

        if (!$event) {
            return false;
        }

        $query = 'UPDATE events SET deleted_at = :deletedAt WHERE id = :eventId';
        $stmt = $this->sql->prepare($query);
        $stmt->bindValue(':deletedAt', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(':eventId', $eventId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
