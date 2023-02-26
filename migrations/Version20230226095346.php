<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230226095346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_category (product_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(product_id, category_id))');
        $this->addSql('CREATE INDEX IDX_CDFC73564584665A ON product_category (product_id)');
        $this->addSql('CREATE INDEX IDX_CDFC735612469DE2 ON product_category (category_id)');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC73564584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC735612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_product DROP CONSTRAINT fk_149244d312469de2');
        $this->addSql('ALTER TABLE category_product DROP CONSTRAINT fk_149244d34584665a');
        $this->addSql('DROP TABLE category_product');
        $this->addSql('ALTER TABLE category ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "order" ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD reference VARCHAR(15) NOT NULL');
        $this->addSql('ALTER TABLE product ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD2B36786B ON product (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE category_product (category_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(category_id, product_id))');
        $this->addSql('CREATE INDEX idx_149244d34584665a ON category_product (product_id)');
        $this->addSql('CREATE INDEX idx_149244d312469de2 ON category_product (category_id)');
        $this->addSql('ALTER TABLE category_product ADD CONSTRAINT fk_149244d312469de2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_product ADD CONSTRAINT fk_149244d34584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_category DROP CONSTRAINT FK_CDFC73564584665A');
        $this->addSql('ALTER TABLE product_category DROP CONSTRAINT FK_CDFC735612469DE2');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('ALTER TABLE category ALTER name TYPE VARCHAR(205)');
        $this->addSql('ALTER TABLE "order" DROP slug');
        $this->addSql('ALTER TABLE "order" DROP reference');
        $this->addSql('DROP INDEX UNIQ_D34A04AD2B36786B');
        $this->addSql('ALTER TABLE product DROP slug');
    }
}
