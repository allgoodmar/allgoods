<?php

namespace Database\Seeders;

use App\StaticText;
use Illuminate\Database\Seeder;

class StaticTextsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // for ($i = 1; $i <= 4; $i++) {
        //     StaticText::factory()->create([
        //         'key' => 'footer_text_' . $i,
        //     ]);
        // }

        // no_products_text
        StaticText::factory()->create([
            'name' => 'Текст нет товаров',
            'description' => 'Упс, Вы зашли на категорию где мы только загружаем товар. Через 3 дня тут уже будет товар, а пока предлагаем перейти в наш основной <a href="/categories" class="text-primary">Каталог</a>',
            'key' => 'no_products_text',
        ]);

        // 404
        StaticText::factory()->create([
            'name' => 'ВОЛШЕБНИКИ ЗА ШИРМОЙ?',
            'description' => 'Все прямо как в сказке о стране Оз',
            'key' => '404_page',
            'image' => 'static_texts/404-img.png'
        ]);

        // principles
        StaticText::factory()->create([
            'name' => 'Бесплатная доставка',
            'description' => 'По Ташкенту',
            'key' => 'principle_1',
            'image' => 'static_texts/principle-01.png'
        ]);
        StaticText::factory()->create([
            'name' => 'Онлайн оплата',
            'description' => 'Принимаем Payme, Click',
            'key' => 'principle_2',
            'image' => 'static_texts/principle-02.png'
        ]);
        StaticText::factory()->create([
            'name' => 'Возник вопрос?',
            'description' => '+99890 850-55-50',
            'key' => 'principle_3',
            'image' => 'static_texts/principle-03.png'
        ]);
        StaticText::factory()->create([
            'name' => 'Рассрочка 0%',
            'description' => 'Без предоплаты ',
            'key' => 'principle_4',
            'image' => 'static_texts/principle-04.png'
        ]);

        // steps
        StaticText::factory()->create([
            'name' => 'Шаг 1',
            'description' => 'Описание',
            'key' => 'step_1',
            'image' => 'static_texts/step-01.png'
        ]);
        StaticText::factory()->create([
            'name' => 'Шаг 2',
            'description' => 'Описание',
            'key' => 'step_2',
            'image' => 'static_texts/step-02.png'
        ]);
        StaticText::factory()->create([
            'name' => 'Шаг 3',
            'description' => 'Описание',
            'key' => 'step_3',
            'image' => 'static_texts/step-03.png'
        ]);
        StaticText::factory()->create([
            'name' => 'Шаг 4',
            'description' => 'Описание',
            'key' => 'step_4',
            'image' => 'static_texts/step-04.png'
        ]);

        StaticText::factory()->create([
            'name' => 'Адрес',
            'key' => 'contact_address',
            'description' => 'г.Ташкент, ул.Саларская наб, 35а',
        ]);

        StaticText::factory()->create([
            'name' => 'Режим работы',
            'key' => 'work_hours',
            'description' => 'Пн-Вс: 9:00 - 18:00',
        ]);

        StaticText::factory()->create([
            'name' => 'Текст доставки (страница товара)',
            'key' => 'delivery_text',
            'description' => '
                Доставка по Узбекистану

                На сайте Вы можете размещать фотографии как на текстовых страницах, так и создавать фотогалереи. Наша система управления позволяет автоматически уменьшать фотографии, и все же мы рекомендуем при работе с изображениями в графических редакторах пользоваться функцией «Сохранить для Web». Она позволяет значительно сжимать размеры изображений. Благодаря этому быстрее загружается сайт и меньше расходуется трафик, что актуально для пользователей, которые не имеют доступа к безлимитному Интернету.

                Самовывоз

                Обращаем Ваше внимание, что текстовая информация на сайте должна быть индивидуальной, не скопированной с других Интернет-ресурсов, о чем указано в рекомендациях Яндекса: «Мы стараемся не индексировать или не ранжировать высоко сайты, копирующие информацию с других ресурсов и не создающие оригинального контента или сервиса».
            ',
        ]);

        StaticText::factory()->create([
            'name' => 'Zoodpay payment description',
            'key' => 'zoodpay_payment_description',
            'description' => 'Рассрочка - 4 платежа',
        ]);

        StaticText::factory()->create([
            'name' => 'Zoodpay payment terms and conditions',
            'key' => 'zoodpay_payment_terms_and_conditions',
            'description' => '<b>Условия обслуживания</b><br>\r\n• Услуга ZoodPay дает возможность оплатить покупку, поделив общую сумму платежа на 4 части в течение 90 дней, без учета процентов и без комиссий. <br>\r\n• Вы должны быть старше 18 лет и быть авторизованным владельцем банковской карты для подачи заявки.<br>\r\n• Все заказы подлежат подтверждению системой. При наличии у Вас просроченных платежей, услуга ZoodPay будет недоступна.<br>\r\n• ZoodPay автоматически удержит сумму платежа с Вашей карты согласно графику. Если платеж не будет обработан в установленный срок, к Вам будет применён штраф за просрочку в размере 7 долларов США.<br>\r\n• В случае невозможности своевременной оплаты, просим связаться с нами незамедлительно.<br>\r\n• Продавец несет ответственность за доставку, качество товара и за осуществление возврата.\r\n<br><br>\r\n<b>ВАЖНАЯ ИНФОРМАЦИЯ О ПРЕДВАРИТЕЛЬНОЙ АВТОРИЗАЦИИ КАРТЫ:</b><br>\r\nВ рамках процесса утверждения и оценки Вашей возможности выполнения своих обязательств по услуге ZoodPay в соответствии с графиком платежей, мы оставляем за собой право провести предварительную авторизацию Вашего заявленного источника платежей. \r\nЭта процедура может включать в себя блокировку средств на счете, каждый раз, когда вы совершаете онлайн-покупку или добавляете новую карту в свою учетную запись ZoodPay.   <br>\r\nДля онлайн-покупок мы немедленно уведомим банк о необходимости аннулирования транзакций предварительной авторизации. В течение этого процесса ZoodPay не удерживает никаких средств. Мы не можем гарантировать сроки, необходимые Вашему банку для обработки этого действия и предоставления ваших средств.\r\n<br><br>\r\nУсловия использования и доступ к нашим Услугам. <br>\r\n1.1 Стороны настоящего соглашения<br>\r\nНастоящее Соглашение является договором между Вами («Вы» или «Ваш») и ZoodPay LLC OrientSwiss («ZoodPay», «мы», «наш»).  <br>\r\n\r\n1.2.  Правила настоящего соглашения<br>\r\nПри несогласии с условиями данного Соглашения, Вам не следует совершать покупки с использованием Сервиса ZoodPay.<br>\r\nПрежде чем воспользоваться какой-либо нашей услугой, Вам необходимо ознакомиться с настоящим Соглашением, а также с Политикой конфиденциальности ZoodPay и другими правилами на сайте / мобильном приложении, которые включены в настоящее Соглашение посредством ссылки.<br>\r\nМы рекомендуем Вам сохранить копию этого соглашения (включая все правила). <br><br>\r\n\r\n\r\n2. Обязанности сторон<br>\r\n2.1 ZoodPay позволяет Вам покупать (a) товары или услуги, предлагаемые онлайн-продавцами, включая зарубежных продавцов, рекомендованных ZoodPay,  (б) а также приобретать товары у сторонних поставщиков.<br>\r\n2.2 Размещая Заказ у Продавца и используя наши услуги, Вы предоставляете нам безоговорочное согласие на проведение нами оплаты товара/услуги от Вашего имени в обмен на Ваше согласие и обязательство погасить оплаченную нами сумму или оплатить нам в соответствии с настоящим соглашением согласованные суммы (которые могут включать любые применимые налоги, пошлины или другие связанные суммы, взимаемые Продавцом) и в сроки, указанные в Вашем Графике платежей, а также любые дополнительные применимые сборы, включая поздние сборы если Вы пропустите возврат в запланированный срок.<br>\r\n2.3 Размещая Заказ через наши услуги на товары от третьих лиц, Вы соглашаетесь вернуть или оплатить нам назначенные суммы в соответствии с настоящим Соглашением, которые могут включать любые применимые налоги или сборы, взимаемые Сторонним поставщиком, и в сроки, указанные в Вашем Графике платежей, а также любые дополнительные применимые сборы, включая поздние сборы, если вы пропустите платеж в назначенный день.<br>\r\n2.4 Вы признаете, что мы не контролируем и не несем ответственности за продукты или услуги, приобретенные у Продавцов, оплаченных с помощью ZoodPay. Мы не можем гарантировать, что Продавец, у которого Вы совершаете покупку, выполнит все свои обязательства.<br>\r\n2.5 Вы признаете, что мы действуем в качестве агента для Сторонних поставщиков, когда мы обрабатываем Заказы на Сторонние товары. Доставка, выполнение и поддержка клиентов для Сторонних Товаров будет обеспечиваться Сторонним Поставщиком. Вы соглашаетесь соблюдать условия и положения Стороннего поставщика, указанного Вами на момент покупки. Пожалуйста, ознакомьтесь со всеми применимыми условиями Стороннего поставщика.',
        ]);

        /*// footer text
        StaticText::factory()->create([
            'name' => 'Footer Text 1',
            'key' => 'footer_text_1',
            'description' => '',
        ]);
        StaticText::factory()->create([
            'name' => 'Footer Text 2',
            'key' => 'footer_text_2',
            'description' => '',
        ]);
        StaticText::factory()->create([
            'name' => 'Footer Text 3',
            'key' => 'footer_text_3',
            'description' => 'Все права защищены.',
        ]);
        StaticText::factory()->create([
            'name' => 'Footer Text 4',
            'key' => 'footer_text_4',
            'description' => 'Копирование материалов с сайта без согласования с администрацией ресурса запрещено',
        ]);

        // add items text
        StaticText::factory()->create([
            'name' => 'Текст "Добавить товар" на странице категории. Шаблоны: {category_name}',
            'key' => 'add_product_text',
            'description' => 'Добавить товар {category_name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Текст "Добавить компанию" на странице рубрики компаний. Шаблоны: {rubric_name}',
            'key' => 'add_company_text',
            'description' => 'Добавить компанию {rubric_name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Текст "Добавить услугу" на странице рубрики услуг. Шаблоны: {rubric_name}',
            'key' => 'add_service_text',
            'description' => 'Добавить услугу {rubric_name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Текст "Добавить публикацию" на странице рубрики публикации. Шаблоны: {rubric_name}',
            'key' => 'add_publication_text',
            'description' => 'Добавить публикацию {rubric_name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Текст "Добавить вакансию" на странице вакансии.',
            'key' => 'add_vacancy_text',
            'description' => 'Добавить вакансию',
        ]);
        StaticText::factory()->create([
            'name' => 'Текст "Добавить резюме" на странице резюме.',
            'key' => 'add_cv_text',
            'description' => 'Добавить резюме',
        ]);

        // SEO meta text Product
        StaticText::factory()->create([
            'name' => 'Meta title товара. Шаблоны: {name}',
            'key' => 'seo_template_product_seo_title',
            'description' => 'Product meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description товара. Шаблоны: {name}',
            'key' => 'seo_template_product_meta_description',
            'description' => 'Product meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords товара. Шаблоны: {name}',
            'key' => 'seo_template_product_meta_keywords',
            'description' => 'Product meta keywords {name}',
        ]);

        // SEO meta text Category
        StaticText::factory()->create([
            'name' => 'Meta title категории. Шаблоны: {name}',
            'key' => 'seo_template_category_seo_title',
            'description' => 'Category meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description категории. Шаблоны: {name}',
            'key' => 'seo_template_category_meta_description',
            'description' => 'Category meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords категории. Шаблоны: {name}',
            'key' => 'seo_template_category_meta_keywords',
            'description' => 'Category meta keywords {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Короткое описание категории. Шаблоны: {name}',
            'key' => 'seo_template_category_description',
            'description' => 'Category page description text {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Полное описание категории. Шаблоны: {name}',
            'key' => 'seo_template_category_body',
            'description' => 'Category page body text {name}',
        ]);

        // SEO meta text Company
        StaticText::factory()->create([
            'name' => 'Meta title компании. Шаблоны: {name}',
            'key' => 'seo_template_company_seo_title',
            'description' => 'Company meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description компании. Шаблоны: {name}',
            'key' => 'seo_template_company_meta_description',
            'description' => 'Company meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords компании. Шаблоны: {name}',
            'key' => 'seo_template_company_meta_keywords',
            'description' => 'Company meta keywords {name}',
        ]);

        // SEO meta text Rubric
        StaticText::factory()->create([
            'name' => 'Meta title рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_seo_title',
            'description' => 'Rubric meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_meta_description',
            'description' => 'Rubric meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_meta_keywords',
            'description' => 'Rubric meta keywords {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Короткое описание рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_description',
            'description' => 'Rubric page description text {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Полное описание рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_body',
            'description' => 'Rubric page body text {name}',
        ]);

        // SEO meta text Service
        StaticText::factory()->create([
            'name' => 'Meta title услуги. Шаблоны: {name}',
            'key' => 'seo_template_service_seo_title',
            'description' => 'Service meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description услуги. Шаблоны: {name}',
            'key' => 'seo_template_service_meta_description',
            'description' => 'Service meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords услуги. Шаблоны: {name}',
            'key' => 'seo_template_service_meta_keywords',
            'description' => 'Service meta keywords {name}',
        ]);

        // SEO meta text Serrubric
        StaticText::factory()->create([
            'name' => 'Meta title рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_seo_title',
            'description' => 'Serrubric meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_meta_description',
            'description' => 'Serrubric meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_meta_keywords',
            'description' => 'Serrubric meta keywords {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Короткое описание рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_description',
            'description' => 'Serrubric page description text {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Полное описание рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_body',
            'description' => 'Serrubric page body text {name}',
        ]);

        // SEO meta text Publication
        StaticText::factory()->create([
            'name' => 'Meta title публикации. Шаблоны: {name}',
            'key' => 'seo_template_publication_seo_title',
            'description' => 'Publication meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description публикации. Шаблоны: {name}',
            'key' => 'seo_template_publication_meta_description',
            'description' => 'Publication meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords публикации. Шаблоны: {name}',
            'key' => 'seo_template_publication_meta_keywords',
            'description' => 'Publication meta keywords {name}',
        ]);

        // SEO meta text Pubrubric
        StaticText::factory()->create([
            'name' => 'Meta title рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_seo_title',
            'description' => 'Pubrubric meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_meta_description',
            'description' => 'Pubrubric meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_meta_keywords',
            'description' => 'Pubrubric meta keywords {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Короткое описание рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_description',
            'description' => 'Pubrubric page description text {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Полное описание рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_body',
            'description' => 'Pubrubric page body text {name}',
        ]);

        // SEO meta text Vacancy
        StaticText::factory()->create([
            'name' => 'Meta title вакансии. Шаблоны: {name}',
            'key' => 'seo_template_vacancy_seo_title',
            'description' => 'Vacancy meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description вакансии. Шаблоны: {name}',
            'key' => 'seo_template_vacancy_meta_description',
            'description' => 'Vacancy meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords вакансии. Шаблоны: {name}',
            'key' => 'seo_template_vacancy_meta_keywords',
            'description' => 'Vacancy meta keywords {name}',
        ]);

        // SEO meta text
        StaticText::factory()->create([
            'name' => 'Meta title резюме. Шаблоны: {name}',
            'key' => 'seo_template_cv_seo_title',
            'description' => 'CV meta title {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta description резюме. Шаблоны: {name}',
            'key' => 'seo_template_cv_meta_description',
            'description' => 'CV meta description {name}',
        ]);
        StaticText::factory()->create([
            'name' => 'Meta keywords резюме. Шаблоны: {name}',
            'key' => 'seo_template_cv_meta_keywords',
            'description' => 'CV meta keywords {name}',
        ]);
        */
    }
}
