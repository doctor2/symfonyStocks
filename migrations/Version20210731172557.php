<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210731172557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etf (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, figi VARCHAR(255) NOT NULL, ticker VARCHAR(255) NOT NULL, isin VARCHAR(255) DEFAULT NULL, currency VARCHAR(255) NOT NULL, is_tracked TINYINT(1) DEFAULT \'0\' NOT NULL, current DOUBLE PRECISION DEFAULT NULL, week_open DOUBLE PRECISION DEFAULT NULL, week_open_percent DOUBLE PRECISION DEFAULT NULL, month_open DOUBLE PRECISION DEFAULT NULL, month_open_percent DOUBLE PRECISION DEFAULT NULL, useful_links LONGTEXT NOT NULL, comment LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE etf');
    }
}
