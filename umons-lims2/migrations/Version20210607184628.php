<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210607184628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hazard (id VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hazard_product (hazard_id VARCHAR(255) NOT NULL, product_id INT NOT NULL, INDEX IDX_D36E74DA38150B74 (hazard_id), INDEX IDX_D36E74DA4584665A (product_id), PRIMARY KEY(hazard_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hazard_hazard (hazard_source VARCHAR(255) NOT NULL, hazard_target VARCHAR(255) NOT NULL, INDEX IDX_CF065AB37BFA0873 (hazard_source), INDEX IDX_CF065AB3621F58FC (hazard_target), PRIMARY KEY(hazard_source, hazard_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, shelf VARCHAR(255) NOT NULL, level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE name (id INT AUTO_INCREMENT NOT NULL, ncas VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5E237E065C65035 (ncas), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, ncas VARCHAR(255) NOT NULL, size VARCHAR(255) DEFAULT NULL, concentration INT NOT NULL, INDEX IDX_D34A04AD64D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `usage` (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, user_id INT NOT NULL, action SMALLINT NOT NULL, date DATETIME NOT NULL, INDEX IDX_D0EB5E704584665A (product_id), INDEX IDX_D0EB5E70A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, registration_number VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_8D93D649D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hazard_product ADD CONSTRAINT FK_D36E74DA38150B74 FOREIGN KEY (hazard_id) REFERENCES hazard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hazard_product ADD CONSTRAINT FK_D36E74DA4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hazard_hazard ADD CONSTRAINT FK_CF065AB37BFA0873 FOREIGN KEY (hazard_source) REFERENCES hazard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hazard_hazard ADD CONSTRAINT FK_CF065AB3621F58FC FOREIGN KEY (hazard_target) REFERENCES hazard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE `usage` ADD CONSTRAINT FK_D0EB5E704584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `usage` ADD CONSTRAINT FK_D0EB5E70A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hazard_product DROP FOREIGN KEY FK_D36E74DA38150B74');
        $this->addSql('ALTER TABLE hazard_hazard DROP FOREIGN KEY FK_CF065AB37BFA0873');
        $this->addSql('ALTER TABLE hazard_hazard DROP FOREIGN KEY FK_CF065AB3621F58FC');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD64D218E');
        $this->addSql('ALTER TABLE hazard_product DROP FOREIGN KEY FK_D36E74DA4584665A');
        $this->addSql('ALTER TABLE `usage` DROP FOREIGN KEY FK_D0EB5E704584665A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE `usage` DROP FOREIGN KEY FK_D0EB5E70A76ED395');
        $this->addSql('DROP TABLE hazard');
        $this->addSql('DROP TABLE hazard_product');
        $this->addSql('DROP TABLE hazard_hazard');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE name');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE `usage`');
        $this->addSql('DROP TABLE user');
    }
}
