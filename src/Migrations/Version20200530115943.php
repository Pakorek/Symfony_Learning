<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200530115943 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE api_season (id INT AUTO_INCREMENT NOT NULL, program_id INT NOT NULL, year INT NOT NULL, number INT NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_76131C293EB8070A (program_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actor_program (actor_id INT NOT NULL, program_id INT NOT NULL, INDEX IDX_B01827EE10DAF24A (actor_id), INDEX IDX_B01827EE3EB8070A (program_id), PRIMARY KEY(actor_id, program_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_creator (id INT AUTO_INCREMENT NOT NULL, api_id VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_creator_api_program (api_creator_id INT NOT NULL, api_program_id INT NOT NULL, INDEX IDX_B704C7C9A68872B7 (api_creator_id), INDEX IDX_B704C7C9F9127B1B (api_program_id), PRIMARY KEY(api_creator_id, api_program_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_category_api_program (api_category_id INT NOT NULL, api_program_id INT NOT NULL, INDEX IDX_2484C1DE7831176C (api_category_id), INDEX IDX_2484C1DEF9127B1B (api_program_id), PRIMARY KEY(api_category_id, api_program_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_episode (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, title VARCHAR(255) NOT NULL, released DATE NOT NULL, image VARCHAR(255) NOT NULL, plot LONGTEXT NOT NULL, number INT NOT NULL, INDEX IDX_309468BD4EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_program (id INT AUTO_INCREMENT NOT NULL, api_id VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, year INT NOT NULL, plot LONGTEXT NOT NULL, poster VARCHAR(255) NOT NULL, runtime INT NOT NULL, awards LONGTEXT DEFAULT NULL, nb_seasons INT NOT NULL, end_year INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_actor (id INT AUTO_INCREMENT NOT NULL, api_id VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_actor_api_program (api_actor_id INT NOT NULL, api_program_id INT NOT NULL, INDEX IDX_971B6560C3E138C5 (api_actor_id), INDEX IDX_971B6560F9127B1B (api_program_id), PRIMARY KEY(api_actor_id, api_program_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_season ADD CONSTRAINT FK_76131C293EB8070A FOREIGN KEY (program_id) REFERENCES api_program (id)');
        $this->addSql('ALTER TABLE actor_program ADD CONSTRAINT FK_B01827EE10DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE actor_program ADD CONSTRAINT FK_B01827EE3EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_creator_api_program ADD CONSTRAINT FK_B704C7C9A68872B7 FOREIGN KEY (api_creator_id) REFERENCES api_creator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_creator_api_program ADD CONSTRAINT FK_B704C7C9F9127B1B FOREIGN KEY (api_program_id) REFERENCES api_program (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_category_api_program ADD CONSTRAINT FK_2484C1DE7831176C FOREIGN KEY (api_category_id) REFERENCES api_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_category_api_program ADD CONSTRAINT FK_2484C1DEF9127B1B FOREIGN KEY (api_program_id) REFERENCES api_program (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_episode ADD CONSTRAINT FK_309468BD4EC001D1 FOREIGN KEY (season_id) REFERENCES api_season (id)');
        $this->addSql('ALTER TABLE api_actor_api_program ADD CONSTRAINT FK_971B6560C3E138C5 FOREIGN KEY (api_actor_id) REFERENCES api_actor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_actor_api_program ADD CONSTRAINT FK_971B6560F9127B1B FOREIGN KEY (api_program_id) REFERENCES api_program (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE api_episode DROP FOREIGN KEY FK_309468BD4EC001D1');
        $this->addSql('ALTER TABLE actor_program DROP FOREIGN KEY FK_B01827EE10DAF24A');
        $this->addSql('ALTER TABLE api_creator_api_program DROP FOREIGN KEY FK_B704C7C9A68872B7');
        $this->addSql('ALTER TABLE api_category_api_program DROP FOREIGN KEY FK_2484C1DE7831176C');
        $this->addSql('ALTER TABLE api_season DROP FOREIGN KEY FK_76131C293EB8070A');
        $this->addSql('ALTER TABLE api_creator_api_program DROP FOREIGN KEY FK_B704C7C9F9127B1B');
        $this->addSql('ALTER TABLE api_category_api_program DROP FOREIGN KEY FK_2484C1DEF9127B1B');
        $this->addSql('ALTER TABLE api_actor_api_program DROP FOREIGN KEY FK_971B6560F9127B1B');
        $this->addSql('ALTER TABLE api_actor_api_program DROP FOREIGN KEY FK_971B6560C3E138C5');
        $this->addSql('DROP TABLE api_season');
        $this->addSql('DROP TABLE actor');
        $this->addSql('DROP TABLE actor_program');
        $this->addSql('DROP TABLE api_creator');
        $this->addSql('DROP TABLE api_creator_api_program');
        $this->addSql('DROP TABLE api_category');
        $this->addSql('DROP TABLE api_category_api_program');
        $this->addSql('DROP TABLE api_episode');
        $this->addSql('DROP TABLE api_program');
        $this->addSql('DROP TABLE api_actor');
        $this->addSql('DROP TABLE api_actor_api_program');
    }
}
