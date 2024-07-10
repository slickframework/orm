<?php

declare(strict_types=1);

namespace Features\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240705111232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table';
    }

    public function up(Schema $schema): void
    {
        $password = '$argon2id$v=19$m=65536,t=4,p=1$UFBQVE9ibGNVVVMvaS5icA$4/7K5Tt1RuN9aN1vzz7+kCmvCrtIObVlNZx69jfzmNs';
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE users (
            id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id))'
        );
        $this->addSql("INSERT INTO users VALUES (null, \"Filipe Silva\", \"filipe.silva@example.com\", \"$password\")");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS users');
    }
}
