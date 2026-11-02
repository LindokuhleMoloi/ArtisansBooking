<?php
class Booking
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO bookings
                (customer_id, artisan_id, service_id, booking_date, start_time, end_time, status, total_price, notes, address)
                VALUES
                (:customer_id, :artisan_id, :service_id, :booking_date, :start_time, :end_time, :status, :total_price, :notes, :address)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':customer_id'  => $data['customer_id'],
            ':artisan_id'   => $data['artisan_id'],
            ':service_id'   => $data['service_id'],
            ':booking_date' => $data['booking_date'],
            ':start_time'   => $data['start_time'],
            ':end_time'     => $data['end_time'],
            ':status'       => $data['status'] ?? 'pending',
            ':total_price'  => $data['total_price'],
            ':notes'        => $data['notes'] ?? null,
            ':address'      => $data['address'] ?? null,
        ]);

        // Increment artisan total_bookings
        $this->db->prepare('UPDATE artisan_profiles SET total_bookings = total_bookings + 1 WHERE user_id = ?')
                 ->execute([$data['artisan_id']]);

        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*,
                    s.title AS service_title, s.duration_minutes,
                    c.first_name AS customer_first, c.last_name AS customer_last, c.email AS customer_email,
                    a.first_name AS artisan_first, a.last_name AS artisan_last, a.email AS artisan_email
             FROM bookings b
             JOIN services s ON s.id = b.service_id
             JOIN users c ON c.id = b.customer_id
             JOIN users a ON a.id = b.artisan_id
             WHERE b.id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, s.title AS service_title, u.first_name AS artisan_first, u.last_name AS artisan_last,
                    ap.business_name
             FROM bookings b
             JOIN services s ON s.id = b.service_id
             JOIN users u ON u.id = b.artisan_id
             LEFT JOIN artisan_profiles ap ON ap.user_id = u.id
             WHERE b.customer_id = ?
             ORDER BY b.booking_date DESC, b.start_time DESC'
        );
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    public function getByArtisan(int $artisanId, ?string $status = null): array
    {
        $sql = 'SELECT b.*, s.title AS service_title,
                       u.first_name AS customer_first, u.last_name AS customer_last, u.phone AS customer_phone
                FROM bookings b
                JOIN services s ON s.id = b.service_id
                JOIN users u ON u.id = b.customer_id
                WHERE b.artisan_id = ?';
        $params = [$artisanId];
        if ($status) {
            $sql .= ' AND b.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY b.booking_date DESC, b.start_time DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    /** Check if a time slot is free for the artisan on a given date */
    public function isSlotAvailable(int $artisanId, string $date, string $start, string $end): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM bookings
             WHERE artisan_id = ?
               AND booking_date = ?
               AND status IN ("pending","confirmed")
               AND (
                    (start_time < ? AND end_time > ?)
                 OR (start_time < ? AND end_time > ?)
                 OR (start_time >= ? AND end_time <= ?)
               )'
        );
        $stmt->execute([$artisanId, $date, $end, $start, $end, $start, $start, $end]);
        return (int)$stmt->fetchColumn() === 0;
    }
}
