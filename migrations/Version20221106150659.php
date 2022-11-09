<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221106150659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_code (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, code VARCHAR(255) NOT NULL, type SMALLINT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_D947C51A76ED395 (user_id), INDEX USER_CODE_CODE_IDX (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_code ADD CONSTRAINT FK_D947C51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD is_verified TINYINT(1) NOT NULL DEFAULT 0, ADD is_registration_completed TINYINT(1) NOT NULL DEFAULT 0, CHANGE phone phone VARCHAR(12) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_code DROP FOREIGN KEY FK_D947C51A76ED395');
        $this->addSql('DROP TABLE user_code');
        $this->addSql('ALTER TABLE user DROP is_verified, DROP is_registration_completed, CHANGE phone phone VARCHAR(12) NOT NULL');
    }
}
