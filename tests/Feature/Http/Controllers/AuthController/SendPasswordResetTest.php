<?php

namespace Tests\Feature\Http\Controllers\AuthController;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendPasswordResetTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * パスワードリセットを実行する（成功）
     *
     * @return void
     */
    public function test_send_reset_password()
    {
        Notification::fake();

        $user = User::factory()->create();

        $params = ['email' => $user->email, 'reset_url' => 'http://localhost:3001/reset_password'];
        $response = $this->post('/api/auth/send_reset_password_token', $params);
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Notification::assertSentTo(
            [$user],
            ResetPasswordNotification::class
        );
    }

     /**
     * パスワードリセットを実行する（失敗：DBには存在しないメールアドレス）
     *
     * @return void
     */
    public function test_send_reset_password_with_invalid_email()
    {
        Notification::fake();

        $user = User::factory()->create();

        $params = ['email' => $this->faker->email, 'reset_url' => 'http://localhost:3001/reset_password'];
        $response = $this->post('/api/auth/send_reset_password_token', $params);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        Notification::assertNothingSent();
    }
}
