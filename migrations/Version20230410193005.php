<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230410193005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, tmdb_id INT NOT NULL, title VARCHAR(255) NOT NULL, original_title VARCHAR(255) DEFAULT NULL, original_language VARCHAR(255) DEFAULT NULL, overview LONGTEXT NOT NULL, release_date DATE DEFAULT NULL, poster_path VARCHAR(1023) DEFAULT NULL, runtime INT DEFAULT NULL, release_status VARCHAR(255) DEFAULT NULL, imdb_id VARCHAR(255) DEFAULT NULL, directors LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', cast LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_movie_meta (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_83F3F3D4A76ED395 (user_id), INDEX IDX_83F3F3D48F93B6FC (movie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE watch_list (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE watch_list_movie (watch_list_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_974429F2C4508918 (watch_list_id), INDEX IDX_974429F28F93B6FC (movie_id), PRIMARY KEY(watch_list_id, movie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_movie_meta ADD CONSTRAINT FK_83F3F3D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_movie_meta ADD CONSTRAINT FK_83F3F3D48F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE watch_list_movie ADD CONSTRAINT FK_974429F2C4508918 FOREIGN KEY (watch_list_id) REFERENCES watch_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE watch_list_movie ADD CONSTRAINT FK_974429F28F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_movie_meta DROP FOREIGN KEY FK_83F3F3D4A76ED395');
        $this->addSql('ALTER TABLE user_movie_meta DROP FOREIGN KEY FK_83F3F3D48F93B6FC');
        $this->addSql('ALTER TABLE watch_list_movie DROP FOREIGN KEY FK_974429F2C4508918');
        $this->addSql('ALTER TABLE watch_list_movie DROP FOREIGN KEY FK_974429F28F93B6FC');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE user_movie_meta');
        $this->addSql('DROP TABLE watch_list');
        $this->addSql('DROP TABLE watch_list_movie');
    }
}
