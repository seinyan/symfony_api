<?php

namespace App\Command;

use App\AppConsts;
use App\Entity\User;
use App\Services\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class UserRegisterCommand extends Command
{
    protected static $defaultName = 'z:user:register';

    /** @var UserService */
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('User Register');
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

        $question = new Question('password:');
        $password = $helper->ask($input, $output, $question);
        if(!$password) {
            $io->note(sprintf('Error: %s', $password));
            return Command::FAILURE;
        }

        $question = new Question('phone:');
        $phone = $helper->ask($input, $output, $question);
        if(!$phone) {
            $io->note(sprintf('Error: %s', $phone));
            return Command::FAILURE;
        }

        $question = new ChoiceQuestion(
            'User - roles',
            [
                User::ROLE_ADMIN,
            ],
            0
        );

        $question->setErrorMessage('Role  %s is invalid.');
        $role = $helper->ask($input, $output, $question);
        $output->writeln('role: '.$role);

        $user = new User();
        $user->setRole($role);
        $user->setIsActive(true);
        $user->setPhone($phone);
        $user->setEmail($email);
        $user->setPassword($password);

        $this->userService->registerByAdmin($user);

        $output->writeln('Role: '.$user->getRole());
        $output->writeln('Phone: '.$user->getPhone());
        $output->writeln('Email: '.$user->getEmail());
        $output->writeln('Password: 111111');

        return Command::SUCCESS;
    }
}
