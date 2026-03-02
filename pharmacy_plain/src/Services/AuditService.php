<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class AuditService
{
    public static function log(?int $companyId, ?int $userId, string $action, string $entity, ?int $entityId = null, array $meta = []): void
    {
        $db = Database::connection();
        $stmt = $db->prepare('INSERT INTO audit_trails (company_id, user_id, action, entity, entity_id, meta_json) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $companyId,
            $userId,
            $action,
            $entity,
            $entityId,
            $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}
