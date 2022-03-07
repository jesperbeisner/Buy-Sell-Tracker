<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220307173319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER DEFAULT NULL, fraction_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, condition INTEGER NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_81398E094584665A ON customer (product_id)');
        $this->addSql('CREATE INDEX IDX_81398E095A615CA9 ON customer (fraction_id)');
        $this->addSql('CREATE TABLE fraction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE map_position (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, x INTEGER NOT NULL, y INTEGER NOT NULL, size INTEGER NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE purchase (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER DEFAULT NULL, product_id INTEGER DEFAULT NULL, fraction_id INTEGER DEFAULT NULL, amount INTEGER NOT NULL, price INTEGER NOT NULL, name VARCHAR(255) NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_6117D13BBB70BC0E ON purchase (shift_id)');
        $this->addSql('CREATE INDEX IDX_6117D13B4584665A ON purchase (product_id)');
        $this->addSql('CREATE INDEX IDX_6117D13B5A615CA9 ON purchase (fraction_id)');
        $this->addSql('CREATE INDEX purchase_created_index ON purchase (created)');
        $this->addSql('CREATE TABLE sale (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, amount INTEGER NOT NULL, black_money INTEGER NOT NULL, real_money INTEGER NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_E54BC0054584665A ON sale (product_id)');
        $this->addSql('CREATE INDEX sale_created_index ON sale (created)');
        $this->addSql('CREATE TABLE shift (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE fraction');
        $this->addSql('DROP TABLE map_position');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE shift');
        $this->addSql('DROP TABLE user');
    }
}
