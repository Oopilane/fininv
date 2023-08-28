<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230826205556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE portfolio (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_A9ED1062A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_portfolio (id INT AUTO_INCREMENT NOT NULL, portfolio_id INT NOT NULL, stock_id INT NOT NULL, amount INT NOT NULL, INDEX IDX_3170F68AB96B5643 (portfolio_id), INDEX IDX_3170F68ADCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE portfolio ADD CONSTRAINT FK_A9ED1062A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stock_portfolio ADD CONSTRAINT FK_3170F68AB96B5643 FOREIGN KEY (portfolio_id) REFERENCES portfolio (id)');
        $this->addSql('ALTER TABLE stock_portfolio ADD CONSTRAINT FK_3170F68ADCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE stock ADD portfolio_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660B96B5643 FOREIGN KEY (portfolio_id) REFERENCES portfolio (id)');
        $this->addSql('CREATE INDEX IDX_4B365660B96B5643 ON stock (portfolio_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660B96B5643');
        $this->addSql('ALTER TABLE portfolio DROP FOREIGN KEY FK_A9ED1062A76ED395');
        $this->addSql('ALTER TABLE stock_portfolio DROP FOREIGN KEY FK_3170F68AB96B5643');
        $this->addSql('ALTER TABLE stock_portfolio DROP FOREIGN KEY FK_3170F68ADCD6110');
        $this->addSql('DROP TABLE portfolio');
        $this->addSql('DROP TABLE stock_portfolio');
        $this->addSql('DROP INDEX IDX_4B365660B96B5643 ON stock');
        $this->addSql('ALTER TABLE stock DROP portfolio_id');
    }
}
