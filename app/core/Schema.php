<?php
/**
 * Database schema. Generates driver-appropriate DDL for MySQL and SQLite.
 */
class Schema
{
    /** @return string[] CREATE statements for the given driver, with $prefix applied. */
    public static function statements(string $driver, string $prefix): array
    {
        $pk = $driver === 'sqlite'
            ? 'INTEGER PRIMARY KEY AUTOINCREMENT'
            : 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';
        $suffix = $driver === 'sqlite'
            ? ''
            : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
        $p = $prefix;

        $tables = [];

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}users (
            id {$pk},
            name VARCHAR(100) NOT NULL,
            email VARCHAR(190) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'admin',
            active TINYINT NOT NULL DEFAULT 1,
            failed_attempts INT NOT NULL DEFAULT 0,
            locked_until DATETIME NULL,
            last_login DATETIME NULL,
            created_at DATETIME NOT NULL
        ){$suffix}";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}settings (
            id {$pk},
            skey VARCHAR(100) NOT NULL,
            svalue MEDIUMTEXT NULL
        ){$suffix}";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}pages (
            id {$pk},
            slug VARCHAR(190) NOT NULL,
            title VARCHAR(190) NOT NULL,
            nav_label VARCHAR(100) NULL,
            content MEDIUMTEXT NULL,
            meta_title VARCHAR(190) NULL,
            meta_description VARCHAR(300) NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'published',
            show_in_nav TINYINT NOT NULL DEFAULT 0,
            nav_order INT NOT NULL DEFAULT 0,
            is_system TINYINT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        ){$suffix}";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}sliders (
            id {$pk},
            title VARCHAR(190) NOT NULL,
            subtitle VARCHAR(400) NULL,
            image VARCHAR(255) NULL,
            cta_text VARCHAR(100) NULL,
            cta_url VARCHAR(255) NULL,
            cta2_text VARCHAR(100) NULL,
            cta2_url VARCHAR(255) NULL,
            sort_order INT NOT NULL DEFAULT 0,
            active TINYINT NOT NULL DEFAULT 1
        ){$suffix}";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}blocks (
            id {$pk},
            identifier VARCHAR(100) NOT NULL,
            title VARCHAR(190) NULL,
            content MEDIUMTEXT NULL,
            note VARCHAR(255) NULL,
            active TINYINT NOT NULL DEFAULT 1
        ){$suffix}";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}companies (
            id {$pk},
            slug VARCHAR(190) NOT NULL,
            name VARCHAR(190) NOT NULL,
            short_name VARCHAR(60) NULL,
            category VARCHAR(60) NULL,
            tagline VARCHAR(300) NULL,
            summary TEXT NULL,
            content MEDIUMTEXT NULL,
            icon VARCHAR(50) NULL,
            image VARCHAR(255) NULL,
            website_url VARCHAR(255) NULL,
            site_status VARCHAR(20) NOT NULL DEFAULT 'coming-soon',
            sort_order INT NOT NULL DEFAULT 0,
            active TINYINT NOT NULL DEFAULT 1
        ){$suffix}";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}leaders (
            id {$pk},
            name VARCHAR(190) NOT NULL,
            title VARCHAR(190) NULL,
            bio MEDIUMTEXT NULL,
            photo VARCHAR(255) NULL,
            linkedin VARCHAR(255) NULL,
            email VARCHAR(190) NULL,
            sort_order INT NOT NULL DEFAULT 0,
            active TINYINT NOT NULL DEFAULT 1
        ){$suffix}";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}media (
            id {$pk},
            filename VARCHAR(255) NOT NULL,
            path VARCHAR(255) NOT NULL,
            mime VARCHAR(100) NULL,
            size INT NOT NULL DEFAULT 0,
            uploaded_by INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL
        ){$suffix}";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}messages (
            id {$pk},
            name VARCHAR(190) NOT NULL,
            email VARCHAR(190) NOT NULL,
            phone VARCHAR(50) NULL,
            company VARCHAR(190) NULL,
            subject VARCHAR(190) NULL,
            message TEXT NOT NULL,
            ip VARCHAR(45) NULL,
            is_read TINYINT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL
        ){$suffix}";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$p}rate_limits (
            id {$pk},
            bucket VARCHAR(50) NOT NULL,
            rkey VARCHAR(120) NOT NULL,
            hits INT NOT NULL DEFAULT 0,
            window_start INT NOT NULL DEFAULT 0
        ){$suffix}";

        // Indexes (separate statements: portable across both drivers)
        $tables[] = "CREATE UNIQUE INDEX {$p}idx_users_email ON {$p}users (email)";
        $tables[] = "CREATE UNIQUE INDEX {$p}idx_settings_skey ON {$p}settings (skey)";
        $tables[] = "CREATE UNIQUE INDEX {$p}idx_pages_slug ON {$p}pages (slug)";
        $tables[] = "CREATE UNIQUE INDEX {$p}idx_blocks_identifier ON {$p}blocks (identifier)";
        $tables[] = "CREATE UNIQUE INDEX {$p}idx_companies_slug ON {$p}companies (slug)";
        $tables[] = "CREATE INDEX {$p}idx_rate_bucket ON {$p}rate_limits (bucket, rkey)";
        $tables[] = "CREATE INDEX {$p}idx_messages_read ON {$p}messages (is_read)";

        return $tables;
    }
}
