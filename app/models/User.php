<?php
class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (role, first_name, last_name, email, phone, password_hash, location_city, location_province, bio)
                VALUES (:role, :first_name, :last_name, :email, :phone, :password_hash, :location_city, :location_province, :bio)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':role'              => $data['role'] ?? 'customer',
            ':first_name'        => $data['first_name'],
            ':last_name'         => $data['last_name'],
            ':email'             => $data['email'],
            ':phone'             => $data['phone'] ?? null,
            ':password_hash'     => password_hash($data['password'], PASSWORD_DEFAULT),
            ':location_city'     => $data['location_city'] ?? null,
            ':location_province' => $data['location_province'] ?? null,
            ':bio'               => $data['bio'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getArtisans(array $filters = []): array
    {
        $sql = 'SELECT u.id, u.first_name, u.last_name, u.location_city, u.location_province, u.bio, u.profile_image,
                       ap.business_name, ap.years_experience, ap.avg_rating, ap.total_reviews, ap.total_bookings, ap.is_verified
                FROM users u
                INNER JOIN artisan_profiles ap ON ap.user_id = u.id
                WHERE u.role = "artisan" AND u.is_active = 1 AND ap.approval_status = "approved"';
        $params = [];

        if (!empty($filters['city'])) {
            $sql .= ' AND u.location_city LIKE ?';
            $params[] = '%' . $filters['city'] . '%';
        }
        if (!empty($filters['category_id'])) {
            $sql .= ' AND EXISTS (SELECT 1 FROM services s WHERE s.artisan_id = u.id AND s.category_id = ? AND s.is_active = 1)';
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['min_rating'])) {
            $sql .= ' AND ap.avg_rating >= ?';
            $params[] = $filters['min_rating'];
        }

        $sql .= ' ORDER BY ap.avg_rating DESC, ap.total_bookings DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAllArtisans(?string $status = null): array
    {
        $sql = 'SELECT u.id, u.first_name, u.last_name, u.email, u.phone,
                       u.location_city, u.location_province, u.bio,
                       ap.business_name, ap.years_experience, ap.total_bookings,
                       ap.approval_status, ap.created_at AS applied_at
                FROM users u
                INNER JOIN artisan_profiles ap ON ap.user_id = u.id
                WHERE u.role = "artisan"';
        $params = [];

        if ($status !== null) {
            $sql .= ' AND ap.approval_status = ?';
            $params[] = $status;
        }

        $sql .= ' ORDER BY FIELD(ap.approval_status, "pending", "approved", "declined"), ap.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getArtisanApprovalStats(): array
    {
        $stats = ['all' => 0, 'pending' => 0, 'approved' => 0, 'declined' => 0];
        $rows = $this->db->query(
            'SELECT ap.approval_status, COUNT(*) AS total
             FROM artisan_profiles ap
             INNER JOIN users u ON u.id = ap.user_id
             WHERE u.role = "artisan"
             GROUP BY ap.approval_status'
        )->fetchAll();

        foreach ($rows as $row) {
            $stats[$row['approval_status']] = (int)$row['total'];
            $stats['all'] += (int)$row['total'];
        }

        return $stats;
    }

    public function updateArtisanApprovalStatus(int $artisanId, string $status): bool
    {
        if (!in_array($status, ['approved', 'declined'], true)) {
            throw new InvalidArgumentException('Invalid artisan approval status.');
        }

        $stmt = $this->db->prepare(
            'UPDATE artisan_profiles ap
             INNER JOIN users u ON u.id = ap.user_id
             SET ap.approval_status = ?, ap.is_verified = ?
             WHERE ap.user_id = ? AND u.role = "artisan" AND ap.approval_status = "pending"'
        );
        $stmt->execute([$status, $status === 'approved' ? 1 : 0, $artisanId]);

        return $stmt->rowCount() > 0;
    }

    public function getArtisanApprovalStatus(int $artisanId): ?string
    {
        $stmt = $this->db->prepare(
            'SELECT ap.approval_status
             FROM artisan_profiles ap
             INNER JOIN users u ON u.id = ap.user_id
             WHERE ap.user_id = ? AND u.role = "artisan"
             LIMIT 1'
        );
        $stmt->execute([$artisanId]);
        $status = $stmt->fetchColumn();

        return $status === false ? null : (string)$status;
    }
}
