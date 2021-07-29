<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210729142632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock CHANGE week_open current_week_open DOUBLE PRECISION DEFAULT NULL, CHANGE week_open_percent current_week_open_percent DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD last_week_open DOUBLE PRECISION DEFAULT NULL, ADD last_week_open_percent DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock CHANGE current_week_open week_open DOUBLE PRECISION DEFAULT NULL, CHANGE current_week_open_percent week_open_percent DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock DROP last_week_open, DROP last_week_open_percent');
    }
}
