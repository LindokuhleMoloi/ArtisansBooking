<?php
class Review
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO reviews (booking_id, customer_id, artisan_id, rating, comment)
                VALUES (:booking_id, :customer_id, :artisan_id, :rating, :comment)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':booking_id'  => $data['booking_id'],
            ':customer_id' => $data['customer_id'],
            ':artisan_id'  => $data['artisan_id'],
            ':rating'      => $data['rating'],
            ':comment'     => $data['comment'] ?? null,
        ]);

        // Recalculate artisan average rating
        $this->recalculateArtisanRating((int)$data['artisan_id']);
        return (int)$this->db->lastInsertId();
    }

    public function recalculateArtisanRating(int $artisanId): void
    {
        $stmt = $this->db->prepare(
            'SELECT AVG(rating) AS avg_r, COUNT(*) AS cnt FROM reviews WHERE artisan_id = ?'
        );
        $stmt->execute([$artisanId]);
        $row = $stmt->fetch();
        $avg = $row['avg_r'] ? round((float)$row['avg_r'], 2) : 0;
        $cnt = (int)$row['cnt'];

        $this->db->prepare(
            'UPDATE artisan_profiles SET avg_rating = ?, total_reviews = ? WHERE user_id = ?'
        )->execute([$avg, $cnt, $artisanId]);
    }

    public function getByArtisan(int $artisanId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.first_name, u.last_name
             FROM reviews r
             JOIN users u ON u.id = r.customer_id
             WHERE r.artisan_id = ?
             ORDER BY r.created_at DESC
             LIMIT ?'
        );
        $stmt->execute([$artisanId, $limit]);
        return $stmt->fetchAll();
    }
}
