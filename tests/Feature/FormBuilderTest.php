<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\FormResponse;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@test.uz',
            'password' => 'password',
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_home_redirects_to_admin_login(): void
    {
        $this->get('/')->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_and_view_dashboard(): void
    {
        $this->post('/login', [
            'email' => 'admin@test.uz',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($this->admin);

        $this->get('/dashboard')->assertOk()->assertSee('FormCraft');
    }

    public function test_admin_can_create_form_with_questions_and_receive_responses(): void
    {
        $this->actingAs($this->admin);

        $this->post('/manage', [
            'title' => 'Test so\'rovnoma',
            'description' => 'Tavsif',
            'form_type' => 'quiz',
        ])->assertRedirect();

        $form = Form::first();
        $this->assertNotNull($form);
        $this->assertSame('Test so\'rovnoma', $form->title);

        $this->postJson("/manage/{$form->id}/structure", [
            'sections' => [],
            'questions' => [
                [
                    'type' => 'short_text',
                    'title' => 'Ismingiz',
                    'description' => null,
                    'is_required' => true,
                    'order' => 0,
                    'points' => 0,
                    'settings' => [],
                    'options' => [],
                ],
                [
                    'type' => 'multiple_choice',
                    'title' => 'Yoshingiz',
                    'description' => null,
                    'is_required' => true,
                    'order' => 1,
                    'points' => 2,
                    'settings' => [],
                    'options' => [
                        ['text' => '18-25', 'is_correct' => true, 'order' => 0],
                        ['text' => '26-35', 'is_correct' => false, 'order' => 1],
                    ],
                ],
            ],
        ])->assertOk()->assertJson(['success' => true]);

        $form->refresh()->load('questions.options');
        $this->assertCount(2, $form->questions);

        $this->get("/f/{$form->slug}")->assertOk()->assertSee('Test so\'rovnoma');

        $mcQuestion = $form->questions->firstWhere('type', 'multiple_choice');
        $textQuestion = $form->questions->firstWhere('type', 'short_text');

        $this->post("/f/{$form->slug}", [
            'answers' => [
                $textQuestion->id => 'Ali',
                $mcQuestion->id => '18-25',
            ],
        ])->assertRedirect(route('forms.thankyou', $form->slug));

        $this->assertSame(1, FormResponse::count());
        $response = FormResponse::first();
        $this->assertSame(2, $response->score);
        $this->assertSame(2, $response->max_score);

        $this->get("/manage/{$form->id}/responses")
            ->assertOk()
            ->assertSee(__('app.responses.count', ['count' => 1]));

        $this->get("/manage/{$form->id}/analytics")
            ->assertOk()
            ->assertSee('Ismingiz');

        $this->get("/manage/{$form->id}/responses/export")
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_closed_form_does_not_accept_responses(): void
    {
        $form = Form::create([
            'user_id' => $this->admin->id,
            'title' => 'Yopiq forma',
            'slug' => 'yopiq-forma',
            'accept_responses' => false,
            'published_at' => now(),
        ]);

        FormQuestion::create([
            'form_id' => $form->id,
            'type' => 'short_text',
            'title' => 'Savol',
            'is_required' => false,
            'order' => 0,
        ]);

        $this->get("/f/{$form->slug}")
            ->assertOk()
            ->assertSee(__('app.public.form_closed'));
    }
}
