<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250108172632 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
       $this->addSql('ALTER TABLE product ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
       $this->addSql('ALTER TABLE product ADD updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
       $this->addSql('ALTER TABLE category ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
       $this->addSql('ALTER TABLE category ADD updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
       $this->addSql('ALTER TABLE category RENAME COLUMN title TO name');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category DROP created_at, DROP updated_at, CHANGE name title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product DROP created_at, DROP updated_at');
    }
}
