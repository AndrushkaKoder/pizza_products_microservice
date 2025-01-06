<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250106182602 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE 
            category_product 
            (
               product_id INT NOT NULL,
               category_id INT NOT NULL
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE category_product');
    }
}
