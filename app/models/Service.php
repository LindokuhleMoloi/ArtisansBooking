<?php
class Service
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, c.name AS category_name, u.first_name, u.last_name, u.location_city
             FROM services s
             JOIN categories c ON c.id = s.category_id
             JOIN users u ON u.id = s.artisan_id
             JOIN artisan_profiles ap ON ap.user_id = u.id
             WHERE s.id = ? AND s.is_active = 1 AND u.is_active = 1
               AND ap.approval_status = "approved"'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByArtisan(int $artisanId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, c.name AS category_name
             FROM services s
             JOIN categories c ON c.id = s.category_id
             JOIN artisan_profiles ap ON ap.user_id = s.artisan_id
             WHERE s.artisan_id = ? AND s.is_active = 1
               AND ap.approval_status = "approved"
             ORDER BY c.name, s.title'
        );
        $stmt->execute([$artisanId]);
        return $stmt->fetchAll();
    }

    public function getAllActive(array $filters = []): array
    {
        $sql = 'SELECT s.*, c.name AS category_name, u.first_name, u.last_name, u.location_city,
                       ap.avg_rating, ap.business_name
                FROM services s
                JOIN categories c ON c.id = s.category_id
                JOIN users u ON u.id = s.artisan_id
                JOIN artisan_profiles ap ON ap.user_id = u.id
                WHERE s.is_active = 1 AND u.is_active = 1
                  AND ap.approval_status = "approved"';
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= ' AND s.category_id = ?';
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['city'])) {
            $sql .= ' AND u.location_city LIKE ?';
            $params[] = '%' . $filters['city'] . '%';
        }

        $sql .= ' ORDER BY ap.avg_rating DESC, s.price ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
