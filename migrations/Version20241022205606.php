<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022205606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidacies DROP FOREIGN KEY FK_CA9ED74D4D18FAD3');
        $this->addSql('ALTER TABLE candidacies DROP FOREIGN KEY FK_CA9ED74D9D86650F');
        $this->addSql('ALTER TABLE relaunches DROP FOREIGN KEY FK_6E7B23D291A4BB23');
        $this->addSql('DROP TABLE platforms');
        $this->addSql('DROP TABLE relaunches');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP INDEX IDX_CA9ED74D4D18FAD3 ON candidacies');
        $this->addSql('DROP INDEX IDX_CA9ED74D9D86650F ON candidacies');
        $this->addSql('ALTER TABLE candidacies ADD type VARCHAR(255) NOT NULL, ADD candidacy_date DATE NOT NULL, ADD relaunch_date DATE DEFAULT NULL, ADD issue VARCHAR(255) DEFAULT NULL, ADD contract_searched VARCHAR(255) NOT NULL, DROP platform_id_id, DROP user_id_id, DROP date, CHANGE society society VARCHAR(255) NOT NULL, CHANGE comments comments LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE platforms (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE relaunches (id INT AUTO_INCREMENT NOT NULL, candidacy_id_id INT NOT NULL, date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_6E7B23D291A4BB23 (candidacy_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_admin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE relaunches ADD CONSTRAINT FK_6E7B23D291A4BB23 FOREIGN KEY (candidacy_id_id) REFERENCES candidacies (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE candidacies ADD platform_id_id INT NOT NULL, ADD user_id_id INT NOT NULL, ADD date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', DROP type, DROP candidacy_date, DROP relaunch_date, DROP issue, DROP contract_searched, CHANGE society society VARCHAR(100) NOT NULL, CHANGE comments comments LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE candidacies ADD CONSTRAINT FK_CA9ED74D4D18FAD3 FOREIGN KEY (platform_id_id) REFERENCES platforms (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE candidacies ADD CONSTRAINT FK_CA9ED74D9D86650F FOREIGN KEY (user_id_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CA9ED74D4D18FAD3 ON candidacies (platform_id_id)');
        $this->addSql('CREATE INDEX IDX_CA9ED74D9D86650F ON candidacies (user_id_id)');
    }
}
