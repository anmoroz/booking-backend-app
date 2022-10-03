<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221003073450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP INDEX UNIQ_4C62E638444F97DD, ADD INDEX CONTACT_PHONE_IDX (phone)');
        $this->addSql('CREATE INDEX CONTACT_PHONE_USER_IDX ON contact (phone, user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP INDEX CONTACT_PHONE_IDX, ADD UNIQUE INDEX UNIQ_4C62E638444F97DD (phone)');
        $this->addSql('DROP INDEX CONTACT_PHONE_USER_IDX ON contact');
    }
}
