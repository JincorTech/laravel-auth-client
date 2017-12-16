<?php

namespace JincorTech\AuthClient\Commands;

use Exception;
use Illuminate\Console\Command;
use JincorTech\AuthClient\AuthServiceInterface;

/**
 * Class CreateUser
 * @package JincorTech\AuthClient\Commands
 */
class CreateUser extends Command
{
    protected $signature = 'auth:create:user 
        {email : Email of the user}
        {login : Login of the user}
        {password : Password of the user}
        {sub : Sub of the user}
        {--scope= : Scope of the user}';

    protected $description = 'User creation';

    private $auth;

    public function __construct(AuthServiceInterface $auth)
    {
        parent::__construct();

        $this->auth = $auth;
    }

    public function handle()
    {
        try {
            $user = $this->auth->createUser([
                'email'     => $this->argument('email'),
                'login'     => $this->argument('login'),
                'password'  => $this->argument('password'),
                'sub'       => $this->argument('sub'),
                'scope'     => $this->option('scope'),
            ], config('jincor-auth.jwt'));

            $this->info(sprintf("User created:\nID: %s\nEmail: %s\nLogin: %s",
                $user->getId(),
                $user->getEmail(),
                $user->getLogin()
            ));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
