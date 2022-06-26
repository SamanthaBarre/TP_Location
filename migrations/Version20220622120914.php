<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220622120914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_6EEAA67D4A4A3511');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__commande AS SELECT id, vehicule_id, user_id, dt_heure_depart, dt_heure_fin, prix_total, dt_enregistrement FROM commande');
        $this->addSql('DROP TABLE commande');
        $this->addSql('CREATE TABLE commande (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, vehicule_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, dt_heure_depart DATETIME NOT NULL, dt_heure_fin DATETIME NOT NULL, prix_total INTEGER NOT NULL, dt_enregistrement DATETIME NOT NULL, CONSTRAINT FK_6EEAA67D4A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO commande (id, vehicule_id, user_id, dt_heure_depart, dt_heure_fin, prix_total, dt_enregistrement) SELECT id, vehicule_id, user_id, dt_heure_depart, dt_heure_fin, prix_total, dt_enregistrement FROM __temp__commande');
        $this->addSql('DROP TABLE __temp__commande');
        $this->addSql('CREATE INDEX IDX_6EEAA67D4A4A3511 ON commande (vehicule_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('ALTER TABLE vehicule ADD COLUMN photo VARCHAR(200) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_6EEAA67D4A4A3511');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__commande AS SELECT id, vehicule_id, user_id, dt_heure_depart, dt_heure_fin, prix_total, dt_enregistrement FROM commande');
        $this->addSql('DROP TABLE commande');
        $this->addSql('CREATE TABLE commande (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, vehicule_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, dt_heure_depart DATETIME NOT NULL, dt_heure_fin DATETIME NOT NULL, prix_total INTEGER NOT NULL, dt_enregistrement DATETIME NOT NULL)');
        $this->addSql('INSERT INTO commande (id, vehicule_id, user_id, dt_heure_depart, dt_heure_fin, prix_total, dt_enregistrement) SELECT id, vehicule_id, user_id, dt_heure_depart, dt_heure_fin, prix_total, dt_enregistrement FROM __temp__commande');
        $this->addSql('DROP TABLE __temp__commande');
        $this->addSql('CREATE INDEX IDX_6EEAA67D4A4A3511 ON commande (vehicule_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__vehicule AS SELECT id, titre, marque, modele, description, prix_journalier, dt_enregistrement FROM vehicule');
        $this->addSql('DROP TABLE vehicule');
        $this->addSql('CREATE TABLE vehicule (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(200) NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(50) NOT NULL, description CLOB DEFAULT NULL, prix_journalier INTEGER NOT NULL, dt_enregistrement DATETIME NOT NULL)');
        $this->addSql('INSERT INTO vehicule (id, titre, marque, modele, description, prix_journalier, dt_enregistrement) SELECT id, titre, marque, modele, description, prix_journalier, dt_enregistrement FROM __temp__vehicule');
        $this->addSql('DROP TABLE __temp__vehicule');
    }
}
