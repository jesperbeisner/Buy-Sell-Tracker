<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220203180402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entry (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER DEFAULT NULL, product_id INTEGER DEFAULT NULL, seller_id INTEGER DEFAULT NULL, amount INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_2B219D70BB70BC0E ON entry (shift_id)');
        $this->addSql('CREATE INDEX IDX_2B219D704584665A ON entry (product_id)');
        $this->addSql('CREATE INDEX IDX_2B219D708DE820D9 ON entry (seller_id)');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE seller (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE shift (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE entry');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE seller');
        $this->addSql('DROP TABLE shift');
    }
}
