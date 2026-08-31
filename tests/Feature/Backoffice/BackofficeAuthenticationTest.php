<?php

namespace Tests\Feature\Backoffice;

use App\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

class BackofficeAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_reloading_login_page_does_not_invalidate_an_open_login_form_token(): void
    {
        $firstResponse = $this->get('/backoffice/login');
        $firstToken = session()->token();

        $secondResponse = $this->get('/backoffice/login');
        $secondToken = session()->token();

        $firstResponse->assertOk();
        $secondResponse->assertOk();
        $this->assertStringContainsString('no-store', (string) $firstResponse->headers->get('Cache-Control'));
        $this->assertNotEmpty($firstToken);
        $this->assertSame($firstToken, $secondToken);
    }

    public function test_logout_regenerates_token_and_subsequent_login_succeeds(): void
    {
        $admin = User::factory()->create([
            'login_id' => 'csrf-login-test',
            'role' => 'super_admin',
            'is_active' => true,
            'password' => bcrypt('backoffice-test-password'),
        ]);

        $this->get('/backoffice/login');
        $tokenBeforeLogout = session()->token();

        $this->actingAs($admin)->get('/backoffice/logout')
            ->assertRedirect('/backoffice/login');
        $this->assertGuest();

        $this->get('/backoffice/login')->assertOk();
        $this->assertNotSame($tokenBeforeLogout, session()->token());

        $this->post('/backoffice/login', [
            'login_id' => $admin->login_id,
            'password' => 'backoffice-test-password',
        ])->assertRedirect('/backoffice');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_expired_login_token_redirects_back_instead_of_rendering_419_page(): void
    {
        $this->get('/backoffice/login');
        $request = Request::create('/backoffice/login', 'POST', ['login_id' => 'admin']);
        $request->setLaravelSession(app('session.store'));

        $response = app(ExceptionHandler::class)->render($request, new TokenMismatchException('CSRF token mismatch.'));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(url('/backoffice/login'), $response->headers->get('Location'));
    }
}
