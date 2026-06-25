<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoFormSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@forms.uz'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );

        $form = Form::firstOrCreate(
            ['slug' => 'mijozlar-so-rovnomasi'],
            [
                'user_id' => $user->id,
                'title' => 'Mijozlar so\'rovnomasi',
                'description' => 'Xizmatimiz haqida fikringizni bildiring. Javoblaringiz biz uchun muhim!',
                'theme_color' => '#673ab7',
                'is_quiz' => false,
                'is_active' => true,
                'accept_responses' => true,
                'show_progress_bar' => true,
                'confirmation_message' => 'Rahmat! Javoblaringiz qabul qilindi.',
                'published_at' => now(),
            ]
        );

        if ($form->questions()->exists()) {
            return;
        }

        $name = FormQuestion::create([
            'form_id' => $form->id,
            'type' => 'short_text',
            'title' => 'Ismingiz',
            'is_required' => true,
            'order' => 0,
        ]);

        $age = FormQuestion::create([
            'form_id' => $form->id,
            'type' => 'multiple_choice',
            'title' => 'Yoshingiz',
            'is_required' => true,
            'order' => 1,
        ]);

        foreach (['18-25', '26-35', '36-45', '46+'] as $i => $text) {
            QuestionOption::create([
                'question_id' => $age->id,
                'text' => $text,
                'order' => $i,
            ]);
        }

        FormQuestion::create([
            'form_id' => $form->id,
            'type' => 'long_text',
            'title' => 'Xizmatimiz haqida fikringiz',
            'description' => 'Istagan fikringizni yozing',
            'is_required' => false,
            'order' => 2,
        ]);

        FormQuestion::create([
            'form_id' => $form->id,
            'type' => 'linear_scale',
            'title' => 'Xizmatdan qanchalik mamnunsiz?',
            'is_required' => true,
            'order' => 3,
            'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Yomon', 'max_label' => 'A\'lo'],
        ]);

        unset($name);
    }
}
