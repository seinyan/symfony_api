<?php

namespace App\Command;

use App\Services\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class UserChangePassCommand extends Command
{
    protected static $defaultName = 'z:user:change_pass';

    /** @var UserService */
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('User change pass');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $question = new Question('email:');
        $email = $helper->ask($input, $output, $question);
        if(!$email) {
            $io->note(sprintf('Error: %s', $email));
            return Command::FAILURE;
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser($email);
        if(!$user) { dump('NOT FOUNT! '); exit; }

        $question = new Question('password:');
        $password = $helper->ask($input, $output, $question);
        if(!$password) {
            $io->note(sprintf('Error: %s', $password));
            return Command::FAILURE;
        }

        $user->setPassword($password);
        $password1 = $this->userService->encodePassword($user);
        $user->setPassword($password1);

        $em = $this->em();
        $em->flush();

        $output->writeln('Email: '.$user->getEmail());
        $output->writeln('Password: '.$password);

        return Command::SUCCESS;
    }
}
