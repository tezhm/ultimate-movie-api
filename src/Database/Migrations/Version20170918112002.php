<?php
namespace Uma\Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170918112002 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE actors (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, birth DATETIME NOT NULL, bio VARCHAR(3000) DEFAULT NULL, image LONGBLOB DEFAULT NULL, UNIQUE INDEX actors_name_unique (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genres (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX genres_name_unique (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre_movies (genre_id INT UNSIGNED NOT NULL, movie_id INT UNSIGNED NOT NULL, INDEX IDX_2DADDD104296D31F (genre_id), INDEX IDX_2DADDD108F93B6FC (movie_id), PRIMARY KEY(genre_id, movie_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre_actors (genre_id INT UNSIGNED NOT NULL, actor_id INT UNSIGNED NOT NULL, INDEX IDX_3498C0C54296D31F (genre_id), INDEX IDX_3498C0C510DAF24A (actor_id), PRIMARY KEY(genre_id, actor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movies (id INT UNSIGNED AUTO_INCREMENT NOT NULL, genre_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, characters LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', rating LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', description VARCHAR(3000) DEFAULT NULL, image LONGBLOB DEFAULT NULL, INDEX IDX_C61EED304296D31F (genre_id), UNIQUE INDEX movies_name_unique (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_actors (movie_id INT UNSIGNED NOT NULL, actor_id INT UNSIGNED NOT NULL, INDEX IDX_26EC6D908F93B6FC (movie_id), INDEX IDX_26EC6D9010DAF24A (actor_id), PRIMARY KEY(movie_id, actor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX users_username_unique (username), UNIQUE INDEX users_api_token_unique (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_favourites (user_id INT UNSIGNED NOT NULL, movie_id INT UNSIGNED NOT NULL, INDEX IDX_D86003EDA76ED395 (user_id), INDEX IDX_D86003ED8F93B6FC (movie_id), PRIMARY KEY(user_id, movie_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE genre_movies ADD CONSTRAINT FK_2DADDD104296D31F FOREIGN KEY (genre_id) REFERENCES genres (id)');
        $this->addSql('ALTER TABLE genre_movies ADD CONSTRAINT FK_2DADDD108F93B6FC FOREIGN KEY (movie_id) REFERENCES movies (id)');
        $this->addSql('ALTER TABLE genre_actors ADD CONSTRAINT FK_3498C0C54296D31F FOREIGN KEY (genre_id) REFERENCES genres (id)');
        $this->addSql('ALTER TABLE genre_actors ADD CONSTRAINT FK_3498C0C510DAF24A FOREIGN KEY (actor_id) REFERENCES actors (id)');
        $this->addSql('ALTER TABLE movies ADD CONSTRAINT FK_C61EED304296D31F FOREIGN KEY (genre_id) REFERENCES genres (id)');
        $this->addSql('ALTER TABLE movie_actors ADD CONSTRAINT FK_26EC6D908F93B6FC FOREIGN KEY (movie_id) REFERENCES movies (id)');
        $this->addSql('ALTER TABLE movie_actors ADD CONSTRAINT FK_26EC6D9010DAF24A FOREIGN KEY (actor_id) REFERENCES actors (id)');
        $this->addSql('ALTER TABLE user_favourites ADD CONSTRAINT FK_D86003EDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_favourites ADD CONSTRAINT FK_D86003ED8F93B6FC FOREIGN KEY (movie_id) REFERENCES movies (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genre_actors DROP FOREIGN KEY FK_3498C0C510DAF24A');
        $this->addSql('ALTER TABLE movie_actors DROP FOREIGN KEY FK_26EC6D9010DAF24A');
        $this->addSql('ALTER TABLE genre_movies DROP FOREIGN KEY FK_2DADDD104296D31F');
        $this->addSql('ALTER TABLE genre_actors DROP FOREIGN KEY FK_3498C0C54296D31F');
        $this->addSql('ALTER TABLE movies DROP FOREIGN KEY FK_C61EED304296D31F');
        $this->addSql('ALTER TABLE genre_movies DROP FOREIGN KEY FK_2DADDD108F93B6FC');
        $this->addSql('ALTER TABLE movie_actors DROP FOREIGN KEY FK_26EC6D908F93B6FC');
        $this->addSql('ALTER TABLE user_favourites DROP FOREIGN KEY FK_D86003ED8F93B6FC');
        $this->addSql('ALTER TABLE user_favourites DROP FOREIGN KEY FK_D86003EDA76ED395');
        $this->addSql('DROP TABLE actors');
        $this->addSql('DROP TABLE genres');
        $this->addSql('DROP TABLE genre_movies');
        $this->addSql('DROP TABLE genre_actors');
        $this->addSql('DROP TABLE movies');
        $this->addSql('DROP TABLE movie_actors');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_favourites');
    }
}
