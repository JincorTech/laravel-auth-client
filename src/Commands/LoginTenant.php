<?php

namespace JincorTech\AuthClient\Commands;

use Exception;
use Illuminate\Console\Command;
use JincorTech\AuthClient\AuthServiceInterface;

/**
 * Class LoginTenant
 * @package JincorTech\AuthClient\Commands
 */
class LoginTenant extends Command
{
    protected $signature = 'auth:login:tenant 
        {email : Email of the tenant} 
        {password : Password of the tenant}';

    protected $description = 'Tenant login';

    private $auth;

    public function __construct(AuthServiceInterface $auth)
    {
        parent::__construct();

        $this->auth = $auth;
    }

    public function handle()
    {
        try {
            $token = $this->auth->loginTenant(
                $this->argument('email'),
                $this->argument('password')
            );

            if (!config('jincor-auth.jwt')) {
                $this->setIdentityJWTInEnvironmentFile($token);
                $this->info('The token is set to .env file');
            } else {
                $this->info('The token cannot be set to .env file, since the IDENTITY_JWT is not empty.');
            }

            $this->info('token: ' . $token);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function setIdentityJWTInEnvironmentFile($token)
    {
        file_put_contents($this->laravel->environmentFilePath(), str_replace(
            'IDENTITY_JWT=',
            'IDENTITY_JWT='.$token,
            file_get_contents($this->laravel->environmentFilePath())
        ));
    }
}
