<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210305213500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Produit (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ref VARCHAR(20) NOT NULL, nomPod VARCHAR(20) NOT NULL, qtStock DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_E618D5BB146F3EA3 (ref), INDEX IDX_E618D5BBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(20) NOT NULL, nom VARCHAR(15) NOT NULL, email VARCHAR(30) NOT NULL, password VARCHAR(255) NOT NULL, telephone VARCHAR(30) DEFAULT NULL, etat VARCHAR(30) DEFAULT NULL, UNIQUE INDEX UNIQ_2DA17977E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Produit ADD CONSTRAINT FK_E618D5BBA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Produit DROP FOREIGN KEY FK_E618D5BBA76ED395');
        $this->addSql('DROP TABLE Produit');
        $this->addSql('DROP TABLE User');
    }
}
