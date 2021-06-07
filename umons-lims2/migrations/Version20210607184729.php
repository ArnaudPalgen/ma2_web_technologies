<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210607184729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE EVENT e_clear_product_usage_history ON SCHEDULE EVERY 1 day COMMENT 'Clears out old usage history.' DO DELETE FROM `usage` WHERE id IN(SELECT id  FROM (SELECT * FROM `usage` AS u1 WHERE u1.DATE < NOW() - INTERVAL 6 MONTH AND (u1.product_id IN (SELECT product_id FROM `usage` WHERE DATE >= NOW() - INTERVAL 6 MONTH) OR u1.`action`= 4)) AS c)");
        $this->addSql("CREATE EVENT e_clear_product ON SCHEDULE EVERY 1 day COMMENT 'Clears product delete in product.' DO DELETE FROM `product` WHERE id IN(SELECT id  FROM (SELECT * FROM `product`  WHERE id NOT IN (SELECT product_id FROM `usage`) ) AS c)");
        $this->addSql("CREATE EVENT e_clear_user ON SCHEDULE EVERY 1 day COMMENT 'Clears user delete.' DO DELETE FROM `user` WHERE id IN(SELECT id  FROM (SELECT * FROM `user` WHERE deleted_at IS NOT NULL AND id NOT IN (SELECT user_id FROM `usage`) ) AS c)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP EVENT e_clear_product_usage_history");
        $this->addSql("DROP EVENT e_clear_product");
        $this->addSql("DROP EVENT e_clear_user");

    }
}
