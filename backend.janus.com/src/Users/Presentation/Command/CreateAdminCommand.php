<?php

declare(strict_types=1);

namespace App\Users\Presentation\Command;

use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Creates an initial admin user for development / E2E testing.
 *
 * Usage:
 *   php bin/console janus:create-admin
 *   php bin/console janus:create-admin --email=admin@example.com --password=Secret1!
 *
 * The command is idempotent: if a user with that email already exists it
 * prints a notice and exits successfully without modifying anything.
 */
#[AsCommand(
    name: 'janus:create-admin',
    description: 'Seed an admin user (idempotent — skips if email already exists).',
)]
final class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly UserRepositoryInterface      $users,
        private readonly UserPasswordHasherInterface  $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email',    null, InputOption::VALUE_REQUIRED, 'Admin email address', 'admin@janus.com')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Admin password',       'Admin1234!');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $email    = (string) $input->getOption('email');
        $password = (string) $input->getOption('password');

        if ($this->users->findByEmail($email) !== null) {
            $io->note(sprintf('User "%s" already exists — skipping.', $email));
            return Command::SUCCESS;
        }

        $user = new User($email);
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setStatus('active');
        $user->setFirstName('Admin');
        $user->setLastName('Janus');

        $hashed = $this->hasher->hashPassword($user, $password);
        $user->setPassword($hashed);

        $this->users->save($user);

        $io->success(sprintf('Admin user "%s" created successfully.', $email));
        return Command::SUCCESS;
    }
}
