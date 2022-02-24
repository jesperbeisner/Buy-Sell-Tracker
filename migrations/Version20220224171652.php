<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220224171652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('DROP INDEX IDX_2B219D70BB70BC0E');
        $this->addSql('DROP INDEX IDX_2B219D704584665A');
        $this->addSql('DROP INDEX IDX_2B219D708DE820D9');
        $this->addSql('DROP INDEX created_index');
        $this->addSql('CREATE TEMPORARY TABLE __temp__entry AS SELECT id, shift_id, product_id, seller_id, amount, price, created, name FROM entry');
        $this->addSql('DROP TABLE entry');
        $this->addSql('CREATE TABLE entry (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER NOT NULL, product_id INTEGER NOT NULL, seller_id INTEGER NOT NULL, amount INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, name VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_2B219D70BB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2B219D704584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2B219D708DE820D9 FOREIGN KEY (seller_id) REFERENCES seller (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO entry (id, shift_id, product_id, seller_id, amount, price, created, name) SELECT id, shift_id, product_id, seller_id, amount, price, created, name FROM __temp__entry');
        $this->addSql('DROP TABLE __temp__entry');
        $this->addSql('CREATE INDEX IDX_2B219D70BB70BC0E ON entry (shift_id)');
        $this->addSql('CREATE INDEX IDX_2B219D704584665A ON entry (product_id)');
        $this->addSql('CREATE INDEX IDX_2B219D708DE820D9 ON entry (seller_id)');
        $this->addSql('CREATE INDEX created_index ON entry (created)');
        $this->addSql('DROP INDEX IDX_E54BC0054584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__sale AS SELECT id, product_id, name, amount, black_money, real_money, created FROM sale');
        $this->addSql('DROP TABLE sale');
        $this->addSql('CREATE TABLE sale (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, amount VARCHAR(255) NOT NULL, black_money DOUBLE PRECISION NOT NULL, real_money DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, CONSTRAINT FK_E54BC0054584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO sale (id, product_id, name, amount, black_money, real_money, created) SELECT id, product_id, name, amount, black_money, real_money, created FROM __temp__sale');
        $this->addSql('DROP TABLE __temp__sale');
        $this->addSql('CREATE INDEX IDX_E54BC0054584665A ON sale (product_id)');
        $this->addSql('DROP INDEX IDX_5B5A69C04584665A');
        $this->addSql('DROP INDEX week_year_index');
        $this->addSql('CREATE TEMPORARY TABLE __temp__week AS SELECT id, product_id, week, year, black_money, real_money, created FROM week');
        $this->addSql('DROP TABLE week');
        $this->addSql('CREATE TABLE week (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, week INTEGER NOT NULL, year INTEGER NOT NULL, black_money DOUBLE PRECISION NOT NULL, real_money DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, CONSTRAINT FK_5B5A69C04584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO week (id, product_id, week, year, black_money, real_money, created) SELECT id, product_id, week, year, black_money, real_money, created FROM __temp__week');
        $this->addSql('DROP TABLE __temp__week');
        $this->addSql('CREATE INDEX IDX_5B5A69C04584665A ON week (product_id)');
        $this->addSql('CREATE INDEX week_year_index ON week (week, year)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_2B219D70BB70BC0E');
        $this->addSql('DROP INDEX IDX_2B219D704584665A');
        $this->addSql('DROP INDEX IDX_2B219D708DE820D9');
        $this->addSql('DROP INDEX created_index');
        $this->addSql('CREATE TEMPORARY TABLE __temp__entry AS SELECT id, shift_id, product_id, seller_id, amount, price, created, name FROM entry');
        $this->addSql('DROP TABLE entry');
        $this->addSql('CREATE TABLE entry (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER NOT NULL, product_id INTEGER NOT NULL, seller_id INTEGER NOT NULL, amount INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, name VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO entry (id, shift_id, product_id, seller_id, amount, price, created, name) SELECT id, shift_id, product_id, seller_id, amount, price, created, name FROM __temp__entry');
        $this->addSql('DROP TABLE __temp__entry');
        $this->addSql('CREATE INDEX IDX_2B219D70BB70BC0E ON entry (shift_id)');
        $this->addSql('CREATE INDEX IDX_2B219D704584665A ON entry (product_id)');
        $this->addSql('CREATE INDEX IDX_2B219D708DE820D9 ON entry (seller_id)');
        $this->addSql('CREATE INDEX created_index ON entry (created)');
        $this->addSql('DROP INDEX IDX_E54BC0054584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__sale AS SELECT id, product_id, name, amount, black_money, real_money, created FROM sale');
        $this->addSql('DROP TABLE sale');
        $this->addSql('CREATE TABLE sale (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, amount VARCHAR(255) NOT NULL, black_money DOUBLE PRECISION NOT NULL, real_money DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('INSERT INTO sale (id, product_id, name, amount, black_money, real_money, created) SELECT id, product_id, name, amount, black_money, real_money, created FROM __temp__sale');
        $this->addSql('DROP TABLE __temp__sale');
        $this->addSql('CREATE INDEX IDX_E54BC0054584665A ON sale (product_id)');
        $this->addSql('DROP INDEX IDX_5B5A69C04584665A');
        $this->addSql('DROP INDEX week_year_index');
        $this->addSql('CREATE TEMPORARY TABLE __temp__week AS SELECT id, product_id, week, year, black_money, real_money, created FROM week');
        $this->addSql('DROP TABLE week');
        $this->addSql('CREATE TABLE week (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, week INTEGER NOT NULL, year INTEGER NOT NULL, black_money DOUBLE PRECISION NOT NULL, real_money DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('INSERT INTO week (id, product_id, week, year, black_money, real_money, created) SELECT id, product_id, week, year, black_money, real_money, created FROM __temp__week');
        $this->addSql('DROP TABLE __temp__week');
        $this->addSql('CREATE INDEX IDX_5B5A69C04584665A ON week (product_id)');
        $this->addSql('CREATE INDEX week_year_index ON week (week, year)');
    }
}
