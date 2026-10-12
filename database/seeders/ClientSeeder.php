<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            'ahmad' => ['أحمد العلي', 'ريف دمشق — قدسيا'],
            'layla' => ['ليلى حسن — استوديو تصوير', 'حمص — الإنشاءات'],
            'alnoor' => ['شركة النور للاستشارات', 'حلب — مركز المدينة'],
            'tannour' => ['مطعم تنور وحطب', 'دمشق — المزة'],
            'khayt' => ['متجر خيط للأزياء', 'حلب — العزيزية'],
            'sanabel' => ['سنابل للمنتجات الغذائية', 'حلب — المدينة الصناعية'],
            'almadar' => ['شركة المدار للتجارة', 'ريف دمشق — عدرا'],
            'wood' => ['خشب وزاوية للأثاث', 'حمص — المنطقة الصناعية'],
            'nawat' => ['نواة للاستشارات والتدريب', 'دمشق — مركز المدينة'],
        ];
        foreach (array_keys($clients) as $index => $key) {
            [$name, $address] = $clients[$key];
            Client::updateOrCreate(['email' => $key.'.demo@example.test'], [
                'name' => $name.' — تجريبي',
                'phone' => '+963000000'.str_pad((string) ($index + 101), 3, '0', STR_PAD_LEFT),
                'address' => $address.' — عنوان وهمي للتجربة',
            ]);
        }
    }
}
