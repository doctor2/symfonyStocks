<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210719144154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD is_tracked TINYINT(1) NOT NULL DEFAULT 0, ADD six_months_maximum DOUBLE PRECISION DEFAULT NULL, ADD six_months_minimum DOUBLE PRECISION DEFAULT NULL, ADD six_months_maximum_percent DOUBLE DEFAULT NULL, ADD current DOUBLE PRECISION DEFAULT NULL, ADD week_open DOUBLE PRECISION DEFAULT NULL, ADD week_open_percent DOUBLE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP is_tracked, DROP six_months_maximum, DROP six_months_minimum, DROP six_months_maximum_percent, DROP current, DROP week_open, DROP week_open_percent');
    }
}
