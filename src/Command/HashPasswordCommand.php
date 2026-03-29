<?php

namespace App\Command;

use App\Security\Sha256PasswordHasher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'security:hash-password',
    description: 'Genera un hash SHA2(256) de una contraseña para testing',
    hidden: false,
)]
class HashPasswordCommand extends Command
{
    public function __construct(private Sha256PasswordHasher $hasher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                '¿Cuál es la contraseña a hashear?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $password = $input->getArgument('password');

        try {
            $hashed = $this->hasher->hash($password);

            $io->success('Hash SHA2(256) generado exitosamente');
            $io->writeln('');
            $io->writeln("<fg=cyan>Contraseña (texto plano):</> <info>$password</info>");
            $io->writeln("<fg=cyan>Hash SHA2(256):</> <comment>$hashed</comment>");
            $io->writeln('');
            $io->note('Copia el hash y úsalo en la BD (tabla personal, columna contrasena)');

            // Test de verificación
            $io->writeln('');
            $io->section('Verificación de seguridad');
            $verify = $this->hasher->verify($hashed, $password);
            if ($verify) {
                $io->success('✓ La verificación funciona correctamente');
            } else {
                $io->error('✗ Hubo un error en la verificación');
                return Command::FAILURE;
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
