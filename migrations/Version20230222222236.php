<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222222236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "order" ADD stripe_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ADD brand_stripe VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ADD last4_stripe VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ADD id_charge_stripe VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ADD status_stripe VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "order" DROP stripe_token');
        $this->addSql('ALTER TABLE "order" DROP brand_stripe');
        $this->addSql('ALTER TABLE "order" DROP last4_stripe');
        $this->addSql('ALTER TABLE "order" DROP id_charge_stripe');
        $this->addSql('ALTER TABLE "order" DROP status_stripe');
    }
}
