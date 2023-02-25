<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221224743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE order_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_state_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE order_history (id INT NOT NULL, state_id INT NOT NULL, orders_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D1C0D9005D83CC1 ON order_history (state_id)');
        $this->addSql('CREATE INDEX IDX_D1C0D900CFFE9AD6 ON order_history (orders_id)');
        $this->addSql('CREATE TABLE order_state (id INT NOT NULL, state VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO order_state (id, state) VALUES (1, \'VALIDATE\')');
        $this->addSql('INSERT INTO order_state (id, state) VALUES (2, \'REFUND\')');
        $this->addSql('INSERT INTO order_state (id, state) VALUES (3, \'REFUND_PENDING\')');
        $this->addSql('INSERT INTO order_state (id, state) VALUES (4, \'CANCEL\')');
        $this->addSql('ALTER TABLE order_history ADD CONSTRAINT FK_D1C0D9005D83CC1 FOREIGN KEY (state_id) REFERENCES order_state (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_history ADD CONSTRAINT FK_D1C0D900CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE order_history_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE order_state_id_seq CASCADE');
        $this->addSql('ALTER TABLE order_history DROP CONSTRAINT FK_D1C0D9005D83CC1');
        $this->addSql('ALTER TABLE order_history DROP CONSTRAINT FK_D1C0D900CFFE9AD6');
        $this->addSql('DROP TABLE order_history');
        $this->addSql('DROP TABLE order_state');
    }
}
