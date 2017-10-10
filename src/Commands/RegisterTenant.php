<?php

namespace JincorTech\AuthClient\Commands;

use Exception;
use Illuminate\Console\Command;
use JincorTech\AuthClient\AuthServiceInterface;

/**
 * Class RegisterTenant
 * @package JincorTech\AuthClient\Commands
 */
class RegisterTenant extends Command
{
    protected $signature = 'auth:register:tenant 
        {email : Email of the tenant}
        {password : Password of the tenant}';

    protected $description = 'Tenant registration';

    private $auth;

    public function __construct(AuthServiceInterface $auth)
    {
        parent::__construct();

        $this->auth = $auth;
    }

    public function handle()
    {
        try {
            $tenant = $this->auth->registerTenant(
                $this->argument('email'),
                $this->argument('password')
            );

            $this->info(sprintf("Tenant registered:\nID: %s\nEmail: %s\nLogin: %s",
                $tenant->getId(),
                $tenant->getEmail(),
                $tenant->getLogin()
            ));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
