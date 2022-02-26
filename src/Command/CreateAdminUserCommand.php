<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin-user', description: 'Creates a new admin user')]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::REQUIRED, 'Username for the new user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $username */
        $username = $input->getArgument('username');

        if (null !== $this->entityManager->getRepository(User::class)->findByUsername($username)) {
            $io->error("Ein User mit dem Usernamen '$username' existiert bereits!");

            return Command::FAILURE;
        }

        $user = new User();
        $user->setUsername($username);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'Passwort123'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success("Der neue Admin-User '$username' mit dem Passwort 'Passwort123' wurde erfolgreich angelegt!");
        $io->info("Nicht vergessen das Passwort nach der ersten Anmeldung zu ändern!");

        return Command::SUCCESS;
    }
}
