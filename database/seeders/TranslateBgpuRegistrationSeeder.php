<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class TranslateBgpuRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $form = Form::find(16);

        if (! $form) {
            $this->command?->warn('Form 16 topilmadi.');

            return;
        }

        $settings = $form->settings ?? [];
        $settings['translations'] = [
            'ru' => [
                'title' => 'Узбекско-Белорусский совместный факультет "Инновационная педагогика" НПУУ и БГПУ',
                'description' => $form->description,
                'confirmation_message' => 'Спасибо! Ваша заявка принята.',
            ],
            'en' => [
                'title' => 'Uzbek-Belarus Joint Faculty "Innovative Pedagogy" NPUU and BGPU',
                'description' => "The educational program lasts 4 years: 3 academic years (semesters 1, 2, 3, 4, 7, 8) students study at NPUU in the Republic of Uzbekistan, and 1 academic year (semesters 5, 6) at BGPU in the Republic of Belarus.\nContact phones of the joint faculty of Nizami National Pedagogical University: 71-276-83-32.\n\nBACHELOR'S (full-time):\n1. Psychology and pedagogy\n2. Preschool education\n3. Primary education\n\nMASTER'S:\n1. Inclusive education\n2. Psychology\n3. Biology\n\nGraduates receive bachelor's and master's diplomas from two leading universities: Nizami NPUU and Maxim Tank Belarusian State Pedagogical University.",
                'confirmation_message' => 'Thank you! Your application has been received.',
            ],
        ];

        $form->update([
            'title' => 'O\'zbekiston-Belarus qo\'shma fakulteti "Innovatsion pedagogika" NPUU va BGPU',
            'description' => "Ta'lim dasturi 4 yil davom etadi, shundan 3 o'quv yili (1, 2, 3, 4, 7, 8-semestrlar) talabalar NPUU O'zbekiston Respublikasida, 1 o'quv yili (5, 6-semestrlar) — BGPU Belarus Respublikasida o'qiydi.\nNizomiy nomidagi Milliy pedagogika universiteti qo'shma fakultetining aloqa telefonlari: 71-276-83-32.\n\nBAKALAVRIAT (kunduzgi ta'lim):\n1. Psixologiya-pedagogika\n2. Maktabgacha ta'lim\n3. Boshlang'ich ta'lim\n\nMAGISTRATURA:\n1. Inklyuziv ta'lim\n2. Psixologiya\n3. Biologiya\n\nBitiruvchilarga ikki yetakchi oliy o'quv yurtining — Nizomiy nomidagi NPUU va Maksim Tank nomidagi Belarus davlat pedagogika universitetining bakalavr va magistr diplomlari beriladi.",
            'confirmation_message' => 'Rahmat! Arizangiz qabul qilindi.',
            'settings' => $settings,
        ]);

        $questions = [
            29 => [
                'uz' => 'Yo\'nalishlar:',
                'ru' => 'Направления:',
                'en' => 'Directions:',
            ],
            30 => ['uz' => 'Familiya', 'ru' => 'Фамилия', 'en' => 'Last name'],
            31 => ['uz' => 'Ism', 'ru' => 'Имя', 'en' => 'First name'],
            32 => ['uz' => 'Otasining ismi', 'ru' => 'Отчество', 'en' => 'Patronymic'],
            33 => ['uz' => 'Tug\'ilgan sana:', 'ru' => 'Дата рождения:', 'en' => 'Date of birth:'],
            34 => ['uz' => 'Pasport seriyasi:', 'ru' => 'Серия паспорта:', 'en' => 'Passport series:'],
            35 => ['uz' => 'Pasport raqami:', 'ru' => 'Номер паспорта:', 'en' => 'Passport number:'],
            36 => ['uz' => 'JSHSHIR (PINFL) raqami:', 'ru' => 'Номер ПИНФЛ:', 'en' => 'PINFL number:'],
            37 => ['uz' => 'Tug\'ilgan joy. Viloyat:', 'ru' => 'Место рождения. Область:', 'en' => 'Place of birth. Region:'],
            38 => ['uz' => 'Tug\'ilgan joy. Shahar, tuman:', 'ru' => 'Место рождения. Город, Район:', 'en' => 'Place of birth. City, district:'],
            39 => ['uz' => 'Yashash manzili: *', 'ru' => 'Адрес проживания: *', 'en' => 'Residential address: *'],
            40 => ['uz' => 'Ma\'lumot:', 'ru' => 'Образование:', 'en' => 'Education:'],
            41 => ['uz' => 'Ta\'lim muassasasining nomi:', 'ru' => 'Наименование образовательного учреждения:', 'en' => 'Name of educational institution:'],
            42 => ['uz' => 'Bitirgan yili:', 'ru' => 'Год окончания:', 'en' => 'Year of graduation:'],
            43 => ['uz' => 'Attestat yoki diplom seriyasi:', 'ru' => 'Серия аттестата или диплома:', 'en' => 'Certificate or diploma series:'],
            44 => ['uz' => 'Attestat yoki diplom raqami:', 'ru' => 'Номер аттестата или диплома:', 'en' => 'Certificate or diploma number:'],
            45 => ['uz' => 'Ish joyi, lavozimi (kasbi):', 'ru' => 'Место работы, занимаемая должность (профессия):', 'en' => 'Place of work, position (profession):'],
            46 => ['uz' => 'Mutaxassislik bo\'yicha ish staji (yil):', 'ru' => 'Трудовой стаж по специальности (лет):', 'en' => 'Work experience in specialty (years):'],
            47 => ['uz' => 'Ota-onasi. Otasining F.I.Sh.:', 'ru' => 'Родители. Ф.И.О. отца:', 'en' => 'Parents. Father\'s full name:'],
            48 => ['uz' => 'Otasining yashash joyi:', 'ru' => 'Место жительство отца:', 'en' => 'Father\'s place of residence:'],
            49 => ['uz' => 'Ota-onasi. Onasining F.I.Sh.:', 'ru' => 'Родители. Ф.И.О. матери:', 'en' => 'Parents. Mother\'s full name:'],
            50 => ['uz' => 'Onasining yashash joyi:', 'ru' => 'Место жительство матери:', 'en' => 'Mother\'s place of residence:'],
            51 => ['uz' => 'Fotosurat 3,5 x 4,5', 'ru' => 'Фотография 3,5 х 4,5', 'en' => 'Photo 3.5 x 4.5'],
            52 => ['uz' => 'Aloqa telefonlari (mobil):', 'ru' => 'Контактные телефоны (мобильный):', 'en' => 'Contact phone (mobile):'],
            53 => ['uz' => 'Aloqa telefonlari (uy):', 'ru' => 'Контактные телефоны (домашний):', 'en' => 'Contact phone (home):'],
            54 => [
                'uz' => 'Belarus Respublikasida 3-kursda 3+1 formatida (magistratura 1+1), rus tilida pullik-kontrakt asosida o\'qish bilan roziman.',
                'ru' => 'С обучением на 3-м курсе в Республики Беларусь в формате 3+1 (магистратура 1+1),  обучением на русском языке на платно-контрактной основе.',
                'en' => 'I agree to study in the 3rd year in the Republic of Belarus in the 3+1 format (master\'s 1+1), studying in Russian on a paid-contract basis.',
                'type' => 'checkbox',
            ],
        ];

        foreach ($questions as $id => $data) {
            $question = FormQuestion::find($id);
            if (! $question) {
                continue;
            }

            $qSettings = $question->settings ?? [];
            $qSettings['translations'] = [
                'ru' => ['title' => $data['ru'], 'description' => ''],
                'en' => ['title' => $data['en'], 'description' => ''],
            ];

            $update = [
                'title' => $data['uz'],
                'settings' => $qSettings,
            ];

            if (! empty($data['type'])) {
                $update['type'] = $data['type'];
            }

            $question->update($update);
        }

        $options = [
            119 => ['uz' => 'Psixologiya-pedagogika (bakalavr)', 'ru' => 'Психология-педагогика (бакалавриат)', 'en' => 'Psychology and pedagogy (bachelor\'s)'],
            120 => ['uz' => 'Maktabgacha ta\'lim (bakalavr)', 'ru' => 'Дошкольное образование (бакалавриат)', 'en' => 'Preschool education (bachelor\'s)'],
            121 => ['uz' => 'Boshlang\'ich ta\'lim (bakalavr)', 'ru' => 'Начальное образование (бакалавриат)', 'en' => 'Primary education (bachelor\'s)'],
            122 => ['uz' => 'Inklyuziv ta\'lim (magistr)', 'ru' => 'Инклюзивное образование (магистратура)', 'en' => 'Inclusive education (master\'s)'],
            123 => ['uz' => 'Psixologiya (magistr)', 'ru' => 'Психология (магистратура)', 'en' => 'Psychology (master\'s)'],
            124 => ['uz' => 'Biologiya (magistr)', 'ru' => 'Биология (магистратура)', 'en' => 'Biology (master\'s)'],
            125 => ['uz' => 'Toshkent shahri', 'ru' => 'Город Ташкент', 'en' => 'Tashkent city'],
            126 => ['uz' => 'Andijon viloyati', 'ru' => 'Андижанская область', 'en' => 'Andijan region'],
            127 => ['uz' => 'Buxoro viloyati', 'ru' => 'Бухарская область', 'en' => 'Bukhara region'],
            128 => ['uz' => 'Jizzax viloyati', 'ru' => 'Джизакская область', 'en' => 'Jizzakh region'],
            129 => ['uz' => 'Qashqadaryo viloyati', 'ru' => 'Кашкадарьинская область', 'en' => 'Kashkadarya region'],
            130 => ['uz' => 'Navoiy viloyati', 'ru' => 'Навоийская область', 'en' => 'Navoiy region'],
            131 => ['uz' => 'Namangan viloyati', 'ru' => 'Наманганская область', 'en' => 'Namangan region'],
            132 => ['uz' => 'Samarqand viloyati', 'ru' => 'Самаркандская область', 'en' => 'Samarkand region'],
            133 => ['uz' => 'Surxondaryo viloyati', 'ru' => 'Сурхандарьинская область', 'en' => 'Surkhandarya region'],
            134 => ['uz' => 'Sirdaryo viloyati', 'ru' => 'Сурхандарьинская область', 'en' => 'Sirdarya region'],
            135 => ['uz' => 'Toshkent viloyati', 'ru' => '• Ташкентская область', 'en' => 'Tashkent region'],
            136 => ['uz' => 'Farg\'ona viloyati', 'ru' => 'Ферганская область', 'en' => 'Fergana region'],
            137 => ['uz' => 'Xorazm viloyati', 'ru' => 'Хорезмская область', 'en' => 'Khorezm region'],
            138 => ['uz' => 'Qoraqalpog\'iston Respublikasi', 'ru' => 'Республика Каракалпакстан', 'en' => 'Republic of Karakalpakstan'],
            139 => ['uz' => 'O\'rta', 'ru' => 'Среднее', 'en' => 'Secondary'],
            140 => ['uz' => 'O\'rta maxsus', 'ru' => 'Среднее специальное', 'en' => 'Secondary specialized'],
            141 => ['uz' => 'Kasb-hunar', 'ru' => 'Профессиональное', 'en' => 'Vocational'],
            142 => ['uz' => 'Roziman', 'ru' => 'Согласен(согласна).', 'en' => 'I agree'],
        ];

        foreach ($options as $id => $data) {
            $option = QuestionOption::find($id);
            if (! $option) {
                continue;
            }

            $option->update([
                'text' => $data['uz'],
                'translations' => [
                    'ru' => $data['ru'],
                    'en' => $data['en'],
                ],
            ]);
        }

        FormQuestion::find(54)?->update(['is_required' => true]);

        $this->command?->info('Form 16 tarjimalari yangilandi (UZ/RU/EN).');
    }
}
