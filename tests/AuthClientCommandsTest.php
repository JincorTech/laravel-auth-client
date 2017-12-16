<?php

namespace JincorTech\AuthClient\Tests;

use Config;
use Dotenv\Dotenv;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Artisan;
use JincorTech\AuthClient\AuthServiceInterface;
use JincorTech\AuthClient\TenantRegistrationResult;
use JincorTech\AuthClient\UserRegistrationResult;
use Mockery;

class AuthClientCommandsTest extends TestCase
{
    /**
     * @var Dotenv
     */
    private $dotEnv;

    protected function setUp()
    {
        parent::setUp();

        $this->app->useEnvironmentPath(__DIR__);

        // create .env
        $fh = fopen(
            $this->app->environmentFilePath(),
            'w'
        );

        fputs($fh, "IDENTITY_SCHEME=http\nIDENTITY_HOST=localhost\nIDENTITY_PORT=3000\nIDENTITY_JWT=\n");
        fclose($fh);

        $this->dotEnv = new Dotenv(__DIR__);
        $this->dotEnv->load();
    }

    protected function tearDown()
    {
        parent::tearDown();
        Mockery::close();
        unlink(__DIR__.'/.env');
    }

    /**
     * @test
     */
    public function registerTenantTest()
    {
        list($email, $password) = ['test@test.com', 'Password1'];

        $this->app->bind(AuthServiceInterface::class, function ($app) use ($email, $password) {
            return Mockery::mock(AuthServiceInterface::class)
                ->shouldReceive('registerTenant')
                ->withArgs([
                    $email,
                    $password
                ])
                ->andReturn(
                    new TenantRegistrationResult([
                        'id' => 'uuid',
                        'email' => $email,
                        'login' => 'tenant-login'
                    ])
                )
                ->getMock();
        });

        $this->artisan('auth:register:tenant', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    /**
     * @test
     */
    public function registerTenantWithWrongDataTest()
    {
        list($email, $password) = ['test@test.com', 'Password1'];

        $mock = Mockery::mock(AuthServiceInterface::class);
        $mock->shouldReceive('registerTenant')
        ->withArgs([
            'testtest.com',
            $password,
        ])
        ->andThrow(new ClientException('Error', new Request('POST', 'http://auth')));

        $this->app->bind(AuthServiceInterface::class, function ($app) use ($email, $password, $mock) {
            return $mock;
        });

        $this->artisan('auth:register:tenant', [
            'password' => $password,
            'email' => 'testtest.com',
        ]);

        $this->assertEquals('Error' . PHP_EOL, Artisan::output());
    }

    /**
     * @test
     */
    public function loginTenantTest()
    {
        list($email, $password) = ['test@test.com', 'Password1'];

        $this->app->bind(AuthServiceInterface::class, function ($app) use ($email, $password) {
            return Mockery::mock(AuthServiceInterface::class)
                ->shouldReceive('loginTenant')
                ->withArgs([
                    $email,
                    $password
                ])
                ->andReturn('jwt-token')
                ->getMock();
        });

        $this->artisan('auth:login:tenant', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertEquals(
            'The token is set to .env file'.PHP_EOL.'token: jwt-token'.PHP_EOL,
            Artisan::output()
        );

        $this->dotEnv->overload();
        $token = env('IDENTITY_JWT');
        $this->assertEquals('jwt-token', $token);
    }

    /**
     * @test
     */
    public function loginTenantWithWrongDataTest()
    {
        list($email, $password) = ['testtest.com', 'Password1'];

        $this->app->bind(AuthServiceInterface::class, function ($app) use ($email, $password) {
            return Mockery::mock(AuthServiceInterface::class)
                ->shouldReceive('loginTenant')
                ->withArgs([
                    $email,
                    $password
                ])
                ->andThrow(new ClientException('Error', new Request('POST', 'http://auth')))
                ->getMock();
        });

        $this->artisan('auth:login:tenant', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertEquals('Error' . PHP_EOL, Artisan::output());
    }

    /**
     * @test
     */
    public function createUserTest()
    {
        Config::set('jincor-auth.jwt', 'token');

        list($email, $login, $password, $sub, $scope) = ['test@test.com', 'test@test.com', 'Password1', '123', 'admin'];

        $this->app->bind(AuthServiceInterface::class, function ($app) use ($email, $password, $login, $sub, $scope) {
            return Mockery::mock(AuthServiceInterface::class)
                ->shouldReceive('createUser')
                ->withArgs([
                    [
                        'email' => $email,
                        'login' => $login,
                        'password' => $password,
                        'sub' => $sub,
                        'scope' => $scope,
                    ],
                    'token'
                ])
                ->andReturn(
                    new UserRegistrationResult([
                        'id' => 'uuid',
                        'email' => $email,
                        'login' => $login,
                        'tenant' => 'tenant',
                        'sub' => '123',
                    ])
                )
                ->getMock();
        });

        $this->artisan('auth:create:user', [
            'email' => $email,
            'login' => $login,
            'password' => $password,
            'sub' => $sub,
            '--scope' => $scope,
        ]);

        Config::set('jincor-auth.jwt', '');
    }
}
