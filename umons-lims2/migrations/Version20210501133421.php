<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210501133421 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chemical_safety (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chemical_safety_chemical_safety (chemical_safety_source INT NOT NULL, chemical_safety_target INT NOT NULL, INDEX IDX_C96E90C8D5B4939 (chemical_safety_source), INDEX IDX_C96E90C814BE19B6 (chemical_safety_target), PRIMARY KEY(chemical_safety_source, chemical_safety_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, shelf VARCHAR(255) NOT NULL, level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, ncas VARCHAR(255) NOT NULL, volume VARCHAR(255) DEFAULT NULL, mass VARCHAR(255) DEFAULT NULL, concentration INT NOT NULL, INDEX IDX_D34A04AD64D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_chemical_safety (product_id INT NOT NULL, chemical_safety_id INT NOT NULL, INDEX IDX_BF09E73B4584665A (product_id), INDEX IDX_BF09E73BD44A970D (chemical_safety_id), PRIMARY KEY(product_id, chemical_safety_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `usage` (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, user_id INT NOT NULL, action SMALLINT NOT NULL, date DATETIME NOT NULL, INDEX IDX_D0EB5E704584665A (product_id), INDEX IDX_D0EB5E70A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, registration_number VARCHAR(255) NOT NULL, INDEX IDX_8D93D649D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chemical_safety_chemical_safety ADD CONSTRAINT FK_C96E90C8D5B4939 FOREIGN KEY (chemical_safety_source) REFERENCES chemical_safety (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chemical_safety_chemical_safety ADD CONSTRAINT FK_C96E90C814BE19B6 FOREIGN KEY (chemical_safety_target) REFERENCES chemical_safety (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE product_chemical_safety ADD CONSTRAINT FK_BF09E73B4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_chemical_safety ADD CONSTRAINT FK_BF09E73BD44A970D FOREIGN KEY (chemical_safety_id) REFERENCES chemical_safety (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `usage` ADD CONSTRAINT FK_D0EB5E704584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `usage` ADD CONSTRAINT FK_D0EB5E70A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chemical_safety_chemical_safety DROP FOREIGN KEY FK_C96E90C8D5B4939');
        $this->addSql('ALTER TABLE chemical_safety_chemical_safety DROP FOREIGN KEY FK_C96E90C814BE19B6');
        $this->addSql('ALTER TABLE product_chemical_safety DROP FOREIGN KEY FK_BF09E73BD44A970D');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD64D218E');
        $this->addSql('ALTER TABLE product_chemical_safety DROP FOREIGN KEY FK_BF09E73B4584665A');
        $this->addSql('ALTER TABLE `usage` DROP FOREIGN KEY FK_D0EB5E704584665A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE `usage` DROP FOREIGN KEY FK_D0EB5E70A76ED395');
        $this->addSql('DROP TABLE chemical_safety');
        $this->addSql('DROP TABLE chemical_safety_chemical_safety');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_chemical_safety');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE `usage`');
        $this->addSql('DROP TABLE user');
    }
}
