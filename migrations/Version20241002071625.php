<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241002071625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne username avec un index unique';
    }

    public function up(Schema $schema): void
    {
        // Vérifier d'abord si la colonne 'username' existe avant de l'ajouter
        if (!$schema->getTable('user')->hasColumn('username')) {
            $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL');
        }

        // Vérifier si l'index unique existe avant de l'ajouter
        if (!$schema->getTable('user')->hasIndex('UNIQ_8D93D649F85E0677')) {
            $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        }
    }

    public function down(Schema $schema): void
    {
        // Supprimer l'index unique
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON `user`');

        // Supprimer la colonne 'username'
        $this->addSql('ALTER TABLE `user` DROP username');
    }
}