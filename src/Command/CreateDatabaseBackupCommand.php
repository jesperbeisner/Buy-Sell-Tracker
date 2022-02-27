<?php

declare(strict_types=1);

namespace App\Command;

use App\Notifier\DiscordNotifier;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

#[AsCommand(name: 'app:create-database-backup', description: 'Creates a database backup and deletes old backups')]
class CreateDatabaseBackupCommand extends Command
{
    public function __construct(
        private ContainerBagInterface $containerBag,
        private DiscordNotifier $discordNotifier,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $varDir = $this->containerBag->get('kernel.project_dir') . '/var';
        $backupDir = $varDir . '/backup';

        if (!is_dir($varDir)) {
            $io->error('The var directory does not exist.');
            return Command::FAILURE;
        }

        if (!file_exists($varDir . '/data.db')) {
            $io->error('No SQLite data.db file found. Nothing to backup.');
            return Command::FAILURE;
        }

        if (!is_dir($backupDir)) {
            mkdir($backupDir);
        }

        $backupDbName = (new DateTime())->format('Y-m-d_H-i-s') . '_data.db';
        if (!copy($varDir . '/data.db', $backupDir . '/' . $backupDbName)) {
            $io->error('The database backup could not be copied.');
            return Command::FAILURE;
        }

        $io->success('Database backup was successfully created!');

        $this->discordNotifier->send(
            "Database backup created",
            "Database backup was successfully created",
            DiscordNotifier::COLOR_GREEN
        );

        $threeDaysTime = 60 * 60 * 24 * 3;

        /** @var string[] $backupFiles */
        $backupFiles = glob($backupDir . '/' . '*_data.db');
        foreach ($backupFiles as $backupFile) {
            $fileTime = filemtime($backupFile);
            if (time() - $fileTime > $threeDaysTime) {
                unlink($backupFile);
                $io->info("Old database backup '$backupFile' was successfully removed.");

                $this->discordNotifier->send(
                    "Database backup deleted",
                    "Old database backup '$backupFile' was successfully deleted",
                    DiscordNotifier::COLOR_GREEN
                );
            }
        }

        return Command::SUCCESS;
    }
}
