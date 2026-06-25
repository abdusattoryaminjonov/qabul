<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_other_users_form(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);

        $form = Form::create([
            'user_id' => $owner->id,
            'title' => 'Maxfiy forma',
            'slug' => 'maxfiy-forma',
            'published_at' => now(),
        ]);

        $this->actingAs($other)
            ->get("/manage/{$form->id}/edit")
            ->assertForbidden();
    }

    public function test_super_admin_can_view_all_forms(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_USER]);

        Form::create([
            'user_id' => $owner->id,
            'title' => 'Boshqa forma',
            'slug' => 'boshqa-forma',
            'published_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->get('/manage')
            ->assertOk()
            ->assertSee('Boshqa forma');
    }

    public function test_public_register_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_super_admin_can_create_users(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)
            ->post('/users', [
                'name' => 'Yangi user',
                'email' => 'user@test.uz',
                'password' => 'Password1',
                'password_confirmation' => 'Password1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'user@test.uz',
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_user_can_update_password_in_profile(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'password' => 'Oldpass1',
        ]);

        $this->actingAs($user)
            ->put('/profile/password', [
                'current_password' => 'Oldpass1',
                'password' => 'Newpass1',
                'password_confirmation' => 'Newpass1',
            ])
            ->assertRedirect(route('admin.profile.edit'));

        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('Newpass1', $user->fresh()->password)
        );
    }

    public function test_user_with_view_all_forms_permission_sees_other_forms(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $viewer = User::factory()->create([
            'role' => User::ROLE_USER,
            'permissions' => ['view_all_forms'],
        ]);

        Form::create([
            'user_id' => $owner->id,
            'title' => 'Boshqa user formasi',
            'slug' => 'boshqa-user-formasi',
            'published_at' => now(),
        ]);

        $this->actingAs($viewer)
            ->get('/manage')
            ->assertOk()
            ->assertSee('Boshqa user formasi');
    }

    public function test_user_with_edit_any_form_can_edit_other_form(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $editor = User::factory()->create([
            'role' => User::ROLE_USER,
            'permissions' => ['edit_any_form'],
        ]);

        $form = Form::create([
            'user_id' => $owner->id,
            'title' => 'Tahrirlanadigan',
            'slug' => 'tahrirlanadigan',
            'published_at' => now(),
        ]);

        $this->actingAs($editor)
            ->get("/manage/{$form->id}/edit")
            ->assertOk();
    }

    public function test_user_without_edit_permission_cannot_edit_other_form(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $viewer = User::factory()->create([
            'role' => User::ROLE_USER,
            'permissions' => ['view_all_forms'],
        ]);

        $form = Form::create([
            'user_id' => $owner->id,
            'title' => 'Faqat ko\'rish',
            'slug' => 'faqat-korish',
            'published_at' => now(),
        ]);

        $this->actingAs($viewer)
            ->get("/manage/{$form->id}/edit")
            ->assertForbidden();
    }

    public function test_view_all_forms_alone_cannot_access_responses(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $viewer = User::factory()->create([
            'role' => User::ROLE_USER,
            'permissions' => ['view_all_forms'],
        ]);

        $form = Form::create([
            'user_id' => $owner->id,
            'title' => 'Javobsiz',
            'slug' => 'javobsiz',
            'published_at' => now(),
        ]);

        $this->actingAs($viewer)
            ->get("/manage/{$form->id}/responses")
            ->assertForbidden();
    }
}
