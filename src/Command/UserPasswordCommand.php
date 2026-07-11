<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Change le mot de passe d'un utilisateur admin.
 *
 *   php bin/console app:user:password admin@agence.fr
 *   php bin/console app:user:password admin@agence.fr -p 'MonMotDePasse'
 */
#[AsCommand(
    name: 'app:user:password',
    description: "Change le mot de passe d'un utilisateur admin.",
)]
class UserPasswordCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, "Email de l'utilisateur")
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Nouveau mot de passe (sinon demandé de façon masquée)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email')
            ?: $io->ask("Email de l'utilisateur", 'admin@agence.fr');

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            $io->error(sprintf('Aucun utilisateur avec l\'email « %s ».', $email));

            return Command::FAILURE;
        }

        $plain = $input->getOption('password') ?: $io->askHidden('Nouveau mot de passe');
        if (!$plain || \strlen($plain) < 6) {
            $io->error('Mot de passe trop court (6 caractères minimum).');

            return Command::FAILURE;
        }

        $user->setPassword($this->hasher->hashPassword($user, $plain));
        $this->em->flush();

        $io->success(sprintf('Mot de passe mis à jour pour « %s ».', $email));

        return Command::SUCCESS;
    }
}
