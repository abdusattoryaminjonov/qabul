<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BgpuFormSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'bgpu@forms.uz'],
            [
                'name' => 'BGPU',
                'password' => Hash::make('bgpu2024'),
                'role' => User::ROLE_USER,
            ]
        );

        $form = Form::updateOrCreate(
            ['slug' => 'siz-aslida-kimsiz-xarakter'],
            [
                'user_id' => $user->id,
                'title' => 'Siz aslida kimsiz? Sizning xarakteringiz',
                'description' => 'Biz uchun va siz uchun muhim qadamlar. Javoblaringiz shaxsiy xarakter tipingizni aniqlashga yordam beradi.',
                'form_type' => Form::TYPE_PSYCHOLOGY,
                'is_quiz' => false,
                'theme_color' => '#1565c0',
                'is_active' => true,
                'accept_responses' => true,
                'show_progress_bar' => true,
                'confirmation_message' => 'Rahmat! Natijangiz quyida ko\'rsatilgan.',
                'settings' => [
                    'translations' => [
                        'ru' => [
                            'title' => 'Кто вы на самом деле? Ваш характер',
                            'description' => 'Важные шаги для нас и для вас. Ваши ответы помогут определить тип личности.',
                            'confirmation_message' => 'Спасибо! Ваш результат показан ниже.',
                        ],
                        'en' => [
                            'title' => 'Who Are You Really? Your Character',
                            'description' => 'Important steps for us and for you. Your answers will help identify your personality type.',
                            'confirmation_message' => 'Thank you! Your result is shown below.',
                        ],
                    ],
                    'psychology_results' => [
                        [
                            'min' => 5, 'max' => 9,
                            'title' => 'Flegmatik tip',
                            'description' => 'Siz xotirjam, barqaror va o\'ylangan qarorlar qabul qiluvchi insonsiz. Siz tinch muhitni va aniq rejalarni afzal ko\'rasiz.',
                            'translations' => [
                                'ru' => ['title' => 'Флегматический тип', 'description' => 'Вы спокойный, устойчивый человек, принимающий взвешенные решения. Вы предпочитаете спокойную обстановку и чёткие планы.'],
                                'en' => ['title' => 'Phlegmatic type', 'description' => 'You are calm, stable, and make thoughtful decisions. You prefer a peaceful environment and clear plans.'],
                            ],
                        ],
                        [
                            'min' => 10, 'max' => 14,
                            'title' => 'Melanxolik tip',
                            'description' => 'Siz chuqur his qiluvchi, samimiy va ijodkor insonsiz. Siz yaqin odamlar bilan munosabatlarni qadrlaysiz.',
                            'translations' => [
                                'ru' => ['title' => 'Меланхолический тип', 'description' => 'Вы глубоко чувствующий, искренний и творческий человек. Вы цените близкие отношения.'],
                                'en' => ['title' => 'Melancholic type', 'description' => 'You are deep-feeling, sincere, and creative. You value close relationships.'],
                            ],
                        ],
                        [
                            'min' => 15, 'max' => 17,
                            'title' => 'Sangvinik tip',
                            'description' => 'Siz ochiq, do\'stona va yangilikka intiluvchi insonsiz. Siz yangi g\'oyalar va ijtimoiy faollikni yaxshi ko\'rasiz.',
                            'translations' => [
                                'ru' => ['title' => 'Сангвинический тип', 'description' => 'Вы открытый, дружелюбный человек, стремящийся к новому. Вы любите новые идеи и общественную активность.'],
                                'en' => ['title' => 'Sanguine type', 'description' => 'You are open, friendly, and drawn to novelty. You enjoy new ideas and social activity.'],
                            ],
                        ],
                        [
                            'min' => 18, 'max' => 20,
                            'title' => 'Xolerik tip',
                            'description' => 'Siz faol, qat\'iyatli va maqsadga intiluvchi insonsiz. Siz tez qaror qabul qilasiz va natijaga erishishga intilasiz.',
                            'translations' => [
                                'ru' => ['title' => 'Холерический тип', 'description' => 'Вы активный, решительный человек, стремящийся к цели. Вы быстро принимаете решения и стремитесь к результату.'],
                                'en' => ['title' => 'Choleric type', 'description' => 'You are active, decisive, and goal-oriented. You make quick decisions and strive for results.'],
                            ],
                        ],
                    ],
                ],
                'published_at' => now(),
            ]
        );

        if ($form->questions()->exists()) {
            return;
        }

        $questions = require __DIR__.'/data/bgpu_personality_questions.php';

        foreach ($questions as $order => $qData) {
            $question = FormQuestion::create([
                'form_id' => $form->id,
                'type' => 'multiple_choice',
                'title' => $qData['title']['uz'],
                'description' => null,
                'is_required' => true,
                'order' => $order,
                'settings' => [
                    'translations' => [
                        'ru' => ['title' => $qData['title']['ru'], 'description' => ''],
                        'en' => ['title' => $qData['title']['en'], 'description' => ''],
                    ],
                ],
            ]);

            foreach ($qData['options'] as $i => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'text' => $opt['uz'],
                    'translations' => ['ru' => $opt['ru'], 'en' => $opt['en']],
                    'score_value' => $opt['score'],
                    'order' => $i,
                ]);
            }
        }
    }
}
