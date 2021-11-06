<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211024085818 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE cron_job (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, command VARCHAR(1024) NOT NULL, schedule VARCHAR(128) NOT NULL, description VARCHAR(128) DEFAULT NULL, enabled BOOLEAN DEFAULT \'0\' NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E6EB8E8ECAEAD4 ON cron_job (command)');
        $this->addSql('CREATE TABLE cron_report (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, due_time DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , finished BOOLEAN DEFAULT \'0\' NOT NULL, run_duration DOUBLE PRECISION DEFAULT NULL, exit_code INTEGER DEFAULT NULL, output CLOB DEFAULT NULL, job_id INTEGER NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_report');
    }
}
