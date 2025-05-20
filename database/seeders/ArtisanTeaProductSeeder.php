<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArtisanTeaProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category ID for Artisan Tea (3)
        $categoryId = 3;

        // Clear existing products in this category to avoid duplicates
        Product::where('category_product', $categoryId)->delete();

        $artisanTeaProducts = [
            // Small packaging P1
            ['sku' => 'BB40P', 'packaging' => 'P1', 'name_product' => 'BLUEBERRY BLACK TEA'],
            ['sku' => 'CC40P', 'packaging' => 'P1', 'name_product' => 'CHOCOLATE OOLONG TEA'],
            ['sku' => 'CO30P', 'packaging' => 'P1', 'name_product' => 'COCONUT OOLONG TEA'],
            ['sku' => 'CF30P', 'packaging' => 'P1', 'name_product' => 'COFFEE & TEA'],
            ['sku' => 'ERR40P', 'packaging' => 'P1', 'name_product' => 'FRENCH EARL GREY'],
            ['sku' => 'GI30P', 'packaging' => 'P1', 'name_product' => 'GINGER BLACK TEA'],
            ['sku' => 'GSP15P', 'packaging' => 'P1', 'name_product' => 'GINSENG PEPPERMINT'],
            ['sku' => 'GNB15P', 'packaging' => 'P1', 'name_product' => 'GOLDEN & BLOOM'],
            ['sku' => 'GS15P', 'packaging' => 'P1', 'name_product' => 'GOODNIGHT SLEEP'],
            ['sku' => 'JG30P', 'packaging' => 'P1', 'name_product' => 'JASMINE GREEN TEA'],
            ['sku' => 'LG40P', 'packaging' => 'P1', 'name_product' => 'LEMON GREEN TEA'],
            ['sku' => 'LY40P', 'packaging' => 'P1', 'name_product' => 'LYCHEE GREEN TEA'],
            ['sku' => 'ORB40P', 'packaging' => 'P1', 'name_product' => 'MANDARIN ORANGE BLACK TEA'],
            ['sku' => 'MG40P', 'packaging' => 'P1', 'name_product' => 'MANGO GREEN TEA'],
            ['sku' => 'MSC50P', 'packaging' => 'P1', 'name_product' => 'MASALA CHAI'],
            ['sku' => 'MM40P', 'packaging' => 'P1', 'name_product' => 'MOROCCAN MINT'],
            ['sku' => 'OSB30P', 'packaging' => 'P1', 'name_product' => 'OSMANTHUS BLACK TEA'],
            ['sku' => 'PS40P', 'packaging' => 'P1', 'name_product' => 'PASSIONFRUIT GREEN TEA'],
            ['sku' => 'PG30P', 'packaging' => 'P1', 'name_product' => 'PANDAN GREEN TEA'],
            ['sku' => 'PH40P', 'packaging' => 'P1', 'name_product' => 'PEACH GREEN TEA'],
            ['sku' => 'ST40P', 'packaging' => 'P1', 'name_product' => 'STRAWBERRY GREEN TEA'],
            ['sku' => 'STB40P', 'packaging' => 'P1', 'name_product' => 'STRAWBERRY HIBISCUS'],

            // Medium packaging P3/P4
            ['sku' => 'BB250P', 'packaging' => 'P3', 'name_product' => 'BLUEBERRY BLACK TEA'],
            ['sku' => 'CC250P', 'packaging' => 'P3', 'name_product' => 'CHOCOLATE OOLONG TEA'],
            ['sku' => 'CO250P', 'packaging' => 'P3', 'name_product' => 'COCONUT OOLONG TEA'],
            ['sku' => 'CF250P', 'packaging' => 'P3', 'name_product' => 'COFFEE & TEA'],
            ['sku' => 'DM250P', 'packaging' => 'P4', 'name_product' => 'DIVINE MERCY'],
            ['sku' => 'ERR250P', 'packaging' => 'P3', 'name_product' => 'FRENCH EARL GREY'],
            ['sku' => 'GI250P', 'packaging' => 'P3', 'name_product' => 'GINGER BLACK TEA'],
            ['sku' => 'GSP250P', 'packaging' => 'P3', 'name_product' => 'GINSENG PEPPERMINT'],
            ['sku' => 'GNB250P', 'packaging' => 'P4', 'name_product' => 'GOLDEN & BLOOM'],
            ['sku' => 'GS250P', 'packaging' => 'P4', 'name_product' => 'GOODNIGHT SLEEP'],
            ['sku' => 'IDB250P', 'packaging' => 'P4', 'name_product' => 'INDONESIAN BREAKFAST'],
            ['sku' => 'JG125P', 'packaging' => 'P3', 'name_product' => 'JASMINE GREEN TEA'],
            ['sku' => 'LOF250P', 'packaging' => 'P4', 'name_product' => 'LADY OF FATIMA'],
            ['sku' => 'LG250P', 'packaging' => 'P3', 'name_product' => 'LEMON GREEN TEA'],
            ['sku' => 'LBF250P', 'packaging' => 'P4', 'name_product' => 'LITTLE BIRTHDAY FLOWER'],
            ['sku' => 'LY250P', 'packaging' => 'P3', 'name_product' => 'LYCHEE GREEN TEA'],
            ['sku' => 'ORB250P', 'packaging' => 'P4', 'name_product' => 'MANDARIN ORANGE BLACK TEA'],
            ['sku' => 'MG250P', 'packaging' => 'P3', 'name_product' => 'MANGO GREEN TEA'],
            ['sku' => 'MGR250P', 'packaging' => 'P4', 'name_product' => 'MARY\'S GARDEN'],
            ['sku' => 'MSC250P', 'packaging' => 'P3', 'name_product' => 'MASALA CHAI'],
            ['sku' => 'MM250P', 'packaging' => 'P3', 'name_product' => 'MOROCCAN MINT'],
            ['sku' => 'OSB250P', 'packaging' => 'P4', 'name_product' => 'OSMANTHUS BLACK TEA'],
            ['sku' => 'PS250P', 'packaging' => 'P3', 'name_product' => 'PASSIONFRUIT GREEN TEA'],
            ['sku' => 'PG125P', 'packaging' => 'P3', 'name_product' => 'PANDAN GREEN TEA'],
            ['sku' => 'PH250P', 'packaging' => 'P3', 'name_product' => 'PEACH GREEN TEA'],
            ['sku' => 'STN250P', 'packaging' => 'P4', 'name_product' => 'STARRY NIGHT'],
            ['sku' => 'ST250P', 'packaging' => 'P3', 'name_product' => 'STRAWBERRY GREEN TEA'],
            ['sku' => 'STB250P', 'packaging' => 'P4', 'name_product' => 'STRAWBERRY HIBISCUS'],

            // Large packaging P4/P5/V0
            ['sku' => 'BB500P', 'packaging' => 'P5', 'name_product' => 'BLUEBERRY BLACK TEA'],
            ['sku' => 'CC500P', 'packaging' => 'P4', 'name_product' => 'CHOCOLATE OOLONG TEA'],
            ['sku' => 'CO500P', 'packaging' => 'P4', 'name_product' => 'COCONUT OOLONG TEA'],
            ['sku' => 'CF500P', 'packaging' => 'P4', 'name_product' => 'COFFEE & TEA'],
            ['sku' => 'DM500P', 'packaging' => 'P5', 'name_product' => 'DIVINE MERCY'],
            ['sku' => 'ERR500P', 'packaging' => 'P4', 'name_product' => 'FRENCH EARL GREY'],
            ['sku' => 'GI500P', 'packaging' => 'P4', 'name_product' => 'GINGER BLACK TEA'],
            ['sku' => 'GSP500P', 'packaging' => 'P4', 'name_product' => 'GINSENG PEPPERMINT'],
            ['sku' => 'GNB500P', 'packaging' => 'P5', 'name_product' => 'GOLDEN & BLOOM'],
            ['sku' => 'GS500P', 'packaging' => 'V0', 'name_product' => 'GOODNIGHT SLEEP'],
            ['sku' => 'IDB500P', 'packaging' => 'P5', 'name_product' => 'INDONESIAN BREAKFAST'],
            ['sku' => 'JG500P', 'packaging' => 'P4', 'name_product' => 'JASMINE GREEN TEA'],
            ['sku' => 'LOF500P', 'packaging' => 'P5', 'name_product' => 'LADY OF FATIMA'],
            ['sku' => 'LG500P', 'packaging' => 'P4', 'name_product' => 'LEMON GREEN TEA'],
            ['sku' => 'LBF500P', 'packaging' => 'P4', 'name_product' => 'LITTLE BIRTHDAY FLOWER'],
            ['sku' => 'LY500P', 'packaging' => 'P4', 'name_product' => 'LYCHEE GREEN TEA'],
            ['sku' => 'ORB500P', 'packaging' => 'P5', 'name_product' => 'MANDARIN ORANGE BLACK TEA'],
            ['sku' => 'MG500P', 'packaging' => 'P4', 'name_product' => 'MANGO GREEN TEA'],
            ['sku' => 'MGR500P', 'packaging' => 'P5', 'name_product' => 'MARY\'S GARDEN'],
            ['sku' => 'MSC500P', 'packaging' => 'P4', 'name_product' => 'MASALA CHAI'],
            ['sku' => 'MM500P', 'packaging' => 'P4', 'name_product' => 'MOROCCAN MINT'],
            ['sku' => 'OSB500P', 'packaging' => 'P5', 'name_product' => 'OSMANTHUS BLACK TEA'],
            ['sku' => 'PS500P', 'packaging' => 'P4', 'name_product' => 'PASSIONFRUIT GREEN TEA'],
            ['sku' => 'PG500P', 'packaging' => 'P5', 'name_product' => 'PANDAN GREEN TEA'],
            ['sku' => 'PH500P', 'packaging' => 'P4', 'name_product' => 'PEACH GREEN TEA'],
            ['sku' => 'STN500P', 'packaging' => 'P5', 'name_product' => 'STARRY NIGHT'],
            ['sku' => 'ST500P', 'packaging' => 'P4', 'name_product' => 'STRAWBERRY GREEN TEA'],
            ['sku' => 'STB500P', 'packaging' => 'P4', 'name_product' => 'STRAWBERRY HIBISCUS'],

            // Extra large packaging
            ['sku' => 'BB500PX2', 'packaging' => 'P5', 'name_product' => 'BLUEBERRY BLACK TEA'],
            ['sku' => 'CC1000P', 'packaging' => 'P5', 'name_product' => 'CHOCOLATE OOLONG TEA'],
            ['sku' => 'CO1000P', 'packaging' => 'P5', 'name_product' => 'COCONUT OOLONG TEA'],
            ['sku' => 'CF1000P', 'packaging' => 'P5', 'name_product' => 'COFFEE & TEA'],
            ['sku' => 'DM500PX2', 'packaging' => 'P5', 'name_product' => 'DIVINE MERCY'],
            ['sku' => 'ERR1000P', 'packaging' => 'P5', 'name_product' => 'FRENCH EARL GREY'],
            ['sku' => 'GI1000P', 'packaging' => 'P5', 'name_product' => 'GINGER BLACK TEA'],
            ['sku' => 'GSP1000P', 'packaging' => 'P5', 'name_product' => 'GINSENG PEPPERMINT'],
            ['sku' => 'GSP1000F', 'packaging' => '-', 'name_product' => 'GINSENG PEPPERMINT'],
            ['sku' => 'GNB500FX2', 'packaging' => 'F0', 'name_product' => 'GOLDEN & BLOOM'],
            ['sku' => 'GS500ZX2', 'packaging' => 'V0', 'name_product' => 'GOODNIGHT SLEEP'],
            ['sku' => 'IDB500PX2', 'packaging' => 'P5', 'name_product' => 'INDONESIAN BREAKFAST'],
            ['sku' => 'JG500PX2', 'packaging' => 'P4', 'name_product' => 'JASMINE GREEN TEA'],
            ['sku' => 'LOF500PX2', 'packaging' => 'P5', 'name_product' => 'LADY OF FATIMA'],
            ['sku' => 'LG1000P', 'packaging' => 'P5', 'name_product' => 'LEMON GREEN TEA'],
            ['sku' => 'LBF500PX2', 'packaging' => 'P4', 'name_product' => 'LITTLE BIRTHDAY FLOWER'],
            ['sku' => 'LY1000P', 'packaging' => 'P5', 'name_product' => 'LYCHEE GREEN TEA'],
            ['sku' => 'ORB500PX2', 'packaging' => 'P5', 'name_product' => 'MANDARIN ORANGE BLACK TEA'],
            ['sku' => 'MG1000P', 'packaging' => 'P5', 'name_product' => 'MANGO GREEN TEA'],
            ['sku' => 'MGR500PX2', 'packaging' => 'P5', 'name_product' => 'MARY\'S GARDEN'],
            ['sku' => 'MSC1000P', 'packaging' => 'P5', 'name_product' => 'MASALA CHAI'],
            ['sku' => 'MM1000P', 'packaging' => 'P5', 'name_product' => 'MOROCCAN MINT'],
            ['sku' => 'OSB500PX2', 'packaging' => 'P5', 'name_product' => 'OSMANTHUS BLACK TEA'],
            ['sku' => 'PS1000P', 'packaging' => 'P4', 'name_product' => 'PASSIONFRUIT GREEN TEA'],
            ['sku' => 'PG500PX2', 'packaging' => 'P5', 'name_product' => 'PANDAN GREEN TEA'],
            ['sku' => 'PH1000P', 'packaging' => 'P5', 'name_product' => 'PEACH GREEN TEA'],
            ['sku' => 'STN500PX2', 'packaging' => 'P5', 'name_product' => 'STARRY NIGHT'],
            ['sku' => 'ST1000P', 'packaging' => 'P5', 'name_product' => 'STRAWBERRY GREEN TEA'],
            ['sku' => 'STB500PX2', 'packaging' => 'P5', 'name_product' => 'STRAWBERRY HIBISCUS'],

            // Tea bags
            ['sku' => 'BB70T', 'packaging' => 'T1', 'name_product' => 'BLUEBERRY BLACK TEA'],
            ['sku' => 'CC100T', 'packaging' => 'T1', 'name_product' => 'CHOCOLATE OOLONG TEA'],
            ['sku' => 'CO80T', 'packaging' => 'T1', 'name_product' => 'COCONUT OOLONG TEA'],
            ['sku' => 'DM25TC', 'packaging' => 'T2', 'name_product' => 'DIVINE MERCY'],
            ['sku' => 'ERR85T', 'packaging' => 'T1', 'name_product' => 'FRENCH EARL GREY'],
            ['sku' => 'GNB40T', 'packaging' => 'T1', 'name_product' => 'GOLDEN & BLOOM'],
            ['sku' => 'GS35T', 'packaging' => 'T1', 'name_product' => 'GOODNIGHT SLEEP'],
            ['sku' => 'IDB35TC', 'packaging' => 'T2', 'name_product' => 'INDONESIAN BREAKFAST'],
            ['sku' => 'JG50T', 'packaging' => 'T1', 'name_product' => 'JASMINE GREEN TEA'],
            ['sku' => 'JB85T', 'packaging' => 'T1', 'name_product' => 'JASMINE PEARL'],
            ['sku' => 'LOF60TC', 'packaging' => 'T2', 'name_product' => 'LADY OF FATIMA'],
            ['sku' => 'LG70T', 'packaging' => 'T1', 'name_product' => 'LEMON GREEN TEA'],
            ['sku' => 'LBF30TC', 'packaging' => 'T2', 'name_product' => 'LITTLE BIRTHDAY FLOWER'],
            ['sku' => 'LY70T', 'packaging' => 'T1', 'name_product' => 'LYCHEE GREEN TEA'],
            ['sku' => 'ORB30T', 'packaging' => 'T1', 'name_product' => 'MANDARIN ORANGE BLACK TEA'],
            ['sku' => 'MG70T', 'packaging' => 'T1', 'name_product' => 'MANGO GREEN TEA'],
            ['sku' => 'MGR45TC', 'packaging' => 'T2', 'name_product' => 'MARY\'S GARDEN'],
            ['sku' => 'MSC90T', 'packaging' => 'T1', 'name_product' => 'MASALA CHAI'],
            ['sku' => 'PS70T', 'packaging' => 'T1', 'name_product' => 'PASSIONFRUIT GREEN TEA'],
            ['sku' => 'PG35T', 'packaging' => 'T1', 'name_product' => 'PANDAN GREEN TEA'],
            ['sku' => 'PH70T', 'packaging' => 'T1', 'name_product' => 'PEACH GREEN TEA'],
            ['sku' => 'RM40T', 'packaging' => 'T1', 'name_product' => 'ROSA MYSTICA'],
            ['sku' => 'STN25TC', 'packaging' => 'T2', 'name_product' => 'STARRY NIGHT'],
            ['sku' => 'ST70T', 'packaging' => 'T1', 'name_product' => 'STRAWBERRY GREEN TEA'],

            // Special packaging
            ['sku' => 'MSC1000F', 'packaging' => 'FG2', 'name_product' => 'MASALA CHAI'],
            ['sku' => 'MSC500F', 'packaging' => 'F3', 'name_product' => 'MASALA CHAI'],
            ['sku' => 'LOF1000FC', 'packaging' => 'FG2', 'name_product' => 'LADY OF FATIMA (CUSTOM ALDI)'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($artisanTeaProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $categoryId,
                'sku' => $product['sku'],
                'packaging' => $product['packaging'],
                'name_product' => $product['name_product'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($productsToInsert, 100) as $chunk) {
            DB::table('products')->insert($chunk);
        }

        $this->command->info(count($artisanTeaProducts) . ' ARTISAN TEA products seeded successfully.');
    }
}
