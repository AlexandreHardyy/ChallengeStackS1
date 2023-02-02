<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202130758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE thumbnail_id_seq CASCADE');
        $this->addSql('ALTER TABLE thumbnail DROP CONSTRAINT fk_c35726e63da5256d');
        $this->addSql('DROP TABLE thumbnail');
        $this->addSql('ALTER TABLE employee ADD firstname VARCHAR(90) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD lastname VARCHAR(120) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD is_banned BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE employee DROP image');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE thumbnail_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE thumbnail (id INT NOT NULL, image_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_c35726e63da5256d ON thumbnail (image_id)');
        $this->addSql('ALTER TABLE thumbnail ADD CONSTRAINT fk_c35726e63da5256d FOREIGN KEY (image_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "employee" ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "employee" DROP firstname');
        $this->addSql('ALTER TABLE "employee" DROP lastname');
        $this->addSql('ALTER TABLE "employee" DROP is_banned');
    }
}
