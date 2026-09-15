<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Creates the `user` table backing account registration and login.
 *
 * Hand-written because no MySQL connection was reachable in this environment to run
 * `php bin/console make:migration`. Once MySQL is up (`docker compose up -d`, or your
 * own instance matching DATABASE_URL in .env), run `php bin/console doctrine:migrations:diff`
 * to confirm it matches the current mapping in src/Entity/User.php exactly before deploying.
 */
final class Version20260914153000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the user table for account registration and login';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(180) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                telephone VARCHAR(20) NOT NULL,
                created_at DATETIME NOT NULL,
                rgpd_consent_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `user`');
    }
}
