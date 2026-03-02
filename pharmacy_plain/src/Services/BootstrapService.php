<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class BootstrapService
{
    public static function ensureMainAdmin(): void
    {
        $db = Database::connection();
        $driver = (string) $db->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $db->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY,
                company_id INTEGER NULL,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                role TEXT NOT NULL,
                full_name TEXT NOT NULL,
                active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
        } else {
            $db->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_id INT NULL,
                username VARCHAR(120) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role ENUM('admin','owner','manager','pharmacist','billing') NOT NULL,
                full_name VARCHAR(255) NOT NULL,
                active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        }

        $stmt = $db->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute(['Vrushab']);
        $exists = $stmt->fetchColumn();

        $hash = password_hash('Fx993ms@vru', PASSWORD_DEFAULT);

        if ($exists) {
            $upd = $db->prepare('UPDATE users SET email = ?, password_hash = ?, role = ?, full_name = ?, active = 1 WHERE username = ?');
            $upd->execute(['vrushab.admin@syncare.local', $hash, 'admin', 'Main Administrator', 'Vrushab']);
            return;
        }

        $ins = $db->prepare('INSERT INTO users (company_id, username, email, password_hash, role, full_name, active) VALUES (NULL, ?, ?, ?, ?, ?, 1)');
        $ins->execute(['Vrushab', 'vrushab.admin@syncare.local', $hash, 'admin', 'Main Administrator']);
    }
}
