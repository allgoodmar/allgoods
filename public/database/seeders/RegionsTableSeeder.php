<?php

namespace Database\Seeders;

use App\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('regions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $regionsCSV = '1;Qoraqalpog‘iston Respublikasi;Қорақалпоғистон Республикаси;Республика Каракалпакстан;Qoraqalpog‘iston;Қорақалпоғистон;Каракалпакстан
2;Andijon viloyati;Андижон вилояти;Андижанская область;Andijon;Андижон;Андижан
3;Buxoro viloyati;Бухоро вилояти;Бухарская область;Buxoro;Бухоро;Бухара
4;Jizzax viloyati;Жиззах вилояти;Джизакская область;Jizzax;Жиззах;Джизак
5;Qashqadaryo viloyati;Қашқадарё вилояти;Кашкадарьинская область;Qashqadaryo;Қашқадарё;Кашкадарья
6;Navoiy viloyati;Навоий вилояти;Навоийская область;Navoiy;Навоий;Навоий
7;Namangan viloyati;Наманган вилояти;Наманганская область;Namangan;Наманган;Наманган
8;Samarqand viloyati;Самарқанд вилояти;Самаркандская область;Samarqand;Самарқанд;Самарканд
9;Surxandaryo viloyati;Сурхандарё вилояти;Сурхандарьинская область;Surxandaryo;Сурхандарё;Сурхандарья
10;Sirdaryo viloyati;Сирдарё вилояти;Сырдарьинская область;Sirdaryo;Сирдарё;Сырдарья
11;Toshkent viloyati;Тошкент вилояти;Ташкентская область;Toshkent vil;Тошкент вил;Ташкентская обл
12;Farg‘ona viloyati;Фарғона вилояти;Ферганская область;Farg‘ona;Фарғона;Фергана
13;Xorazm viloyati;Хоразм вилояти;Хорезмская область;Xorazm;Хоразм;Хорезм
14;Toshkent shahri;Тошкент шаҳри;Город Ташкент;Toshkent;Тошкент;Ташкент
';

        $regionsRaw = [];
        $rows = str_getcsv($regionsCSV, "\n"); // Parses the rows. Treats the rows as a CSV with \n as a delimiter
        foreach ($rows as $row) {
            $regionsRaw[] = str_getcsv($row, ';'); // Parses individual rows. Now treats a row as a regular CSV with ',' as a delimiter
        }

        foreach($regionsRaw as $regionRaw) {
            $region = Region::create([
                'id' => (int)trim($regionRaw[0]),
                'name' => trim($regionRaw[3]),
                'short_name' => trim($regionRaw[6]),
            ]);

            $region = $region->translate('uz');
            $region->name = trim($regionRaw[1]);
            $region->short_name = trim($regionRaw[4]);
            $region->save();
        }

    }
}
