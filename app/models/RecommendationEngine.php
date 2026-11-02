<?php
/**
 * Recommendation Engine
 * Hybrid scoring: rating + popularity + category match + location proximity
 */
class RecommendationEngine
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get recommended artisans for a user (or guest)
     * @param int|null $userId
     * @param int $limit
     * @param int|null $preferredCategoryId
     * @return array
     */
    public function recommend(?int $userId = null, int $limit = 6, ?int $preferredCategoryId = null): array
    {
        $userCity = null;
        $pastCategories = [];

        if ($userId) {
            $u = $this->db->prepare('SELECT location_city FROM users WHERE id = ?');
            $u->execute([$userId]);
            $userCity = $u->fetchColumn() ?: null;

            // Categories the user previously booked
            $c = $this->db->prepare(
                'SELECT DISTINCT s.category_id
                 FROM bookings b
                 JOIN services s ON s.id = b.service_id
                 WHERE b.customer_id = ? AND b.status IN ("completed","confirmed")'
            );
            $c->execute([$userId]);
            $pastCategories = $c->fetchAll(PDO::FETCH_COLUMN);
        }

        if ($preferredCategoryId) {
            $pastCategories[] = $preferredCategoryId;
            $pastCategories = array_unique($pastCategories);
        }

        // Fetch candidate artisans
        $sql = 'SELECT u.id, u.first_name, u.last_name, u.location_city, u.location_province, u.bio, u.profile_image,
                       ap.business_name, ap.years_experience, ap.avg_rating, ap.total_reviews,
                       ap.total_bookings, ap.is_verified, ap.service_radius_km
                FROM users u
                INNER JOIN artisan_profiles ap ON ap.user_id = u.id
                WHERE u.role = "artisan" AND u.is_active = 1
                  AND ap.approval_status = "approved"';
        $artisans = $this->db->query($sql)->fetchAll();

        $scored = [];
        $maxBookings = 1;
        foreach ($artisans as $a) {
            $maxBookings = max($maxBookings, (int)$a['total_bookings']);
        }

        foreach ($artisans as $a) {
            $score = 0.0;
            $reasons = [];

            // 1. Rating (0-5 → 0-1)
            $ratingNorm = min(1.0, (float)$a['avg_rating'] / 5.0);
            $score += REC_WEIGHT_RATING * $ratingNorm;
            if ($a['avg_rating'] >= 4.5) $reasons[] = 'Highly rated';

            // 2. Popularity (bookings)
            $popNorm = min(1.0, (int)$a['total_bookings'] / $maxBookings);
            $score += REC_WEIGHT_BOOKINGS * $popNorm;
            if ($a['total_bookings'] > 20) $reasons[] = 'Popular';

            // 3. Category match
            $catScore = 0.0;
            if (!empty($pastCategories)) {
                $stmt = $this->db->prepare(
                    'SELECT COUNT(*) FROM services
                     WHERE artisan_id = ? AND category_id IN (' . implode(',', array_fill(0, count($pastCategories), '?')) . ') AND is_active = 1'
                );
                $stmt->execute(array_merge([$a['id']], $pastCategories));
                $matchCount = (int)$stmt->fetchColumn();
                $catScore = $matchCount > 0 ? 1.0 : 0.0;
                if ($matchCount > 0) $reasons[] = 'Matches your interests';
            } else {
                $catScore = 0.5; // neutral for new users
            }
            $score += REC_WEIGHT_CATEGORY * $catScore;

            // 4. Location proximity (simple same-city match)
            $locScore = 0.0;
            if ($userCity && strcasecmp($userCity, $a['location_city']) === 0) {
                $locScore = 1.0;
                $reasons[] = 'Near you';
            } elseif ($userCity) {
                $locScore = 0.3; // different city but still possible
            } else {
                $locScore = 0.5;
            }
            $score += REC_WEIGHT_LOCATION * $locScore;

            // Verified bonus
            if ($a['is_verified']) {
                $score += 0.05;
                $reasons[] = 'Verified';
            }

            $a['recommendation_score'] = round($score, 4);
            $a['recommendation_reasons'] = $reasons;
            $scored[] = $a;
        }

        // Sort by score descending
        usort($scored, fn($x, $y) => $y['recommendation_score'] <=> $x['recommendation_score']);

        // Optional logging
        if ($userId) {
            $this->logRecommendations($userId, array_slice($scored, 0, $limit));
        }

        return array_slice($scored, 0, $limit);
    }

    private function logRecommendations(int $userId, array $list): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO recommendation_logs (user_id, artisan_id, score, reason) VALUES (?, ?, ?, ?)'
        );
        foreach ($list as $item) {
            $reason = implode(', ', $item['recommendation_reasons'] ?? []);
            $stmt->execute([$userId, $item['id'], $item['recommendation_score'], $reason]);
        }
    }
}
