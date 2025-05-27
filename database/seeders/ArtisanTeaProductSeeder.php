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
            // Small packaging P1 - Label 1
            ['sku' => 'BB40P', 'packaging' => 'P1', 'name_product' => 'BLUEBERRY BLACK TEA', 'label' => 1],
            ['sku' => 'CC40P', 'packaging' => 'P1', 'name_product' => 'CHOCOLATE OOLONG TEA', 'label' => 1],
            ['sku' => 'CO30P', 'packaging' => 'P1', 'name_product' => 'COCONUT OOLONG TEA', 'label' => 1],
            ['sku' => 'CF30P', 'packaging' => 'P1', 'name_product' => 'COFFEE & TEA', 'label' => 1],
            ['sku' => 'ERR40P', 'packaging' => 'P1', 'name_product' => 'FRENCH EARL GREY', 'label' => 1],
            ['sku' => 'GI30P', 'packaging' => 'P1', 'name_product' => 'GINGER BLACK TEA', 'label' => 1],
            ['sku' => 'GSP15P', 'packaging' => 'P1', 'name_product' => 'GINSENG PEPPERMINT', 'label' => 1],
            ['sku' => 'GNB15P', 'packaging' => 'P1', 'name_product' => 'GOLDEN & BLOOM', 'label' => 1],
            ['sku' => 'GS15P', 'packaging' => 'P1', 'name_product' => 'GOODNIGHT SLEEP', 'label' => 1],
            ['sku' => 'JG30P', 'packaging' => 'P1', 'name_product' => 'JASMINE GREEN TEA', 'label' => 1],
            ['sku' => 'LG40P', 'packaging' => 'P1', 'name_product' => 'LEMON GREEN TEA', 'label' => 1],
            ['sku' => 'LY40P', 'packaging' => 'P1', 'name_product' => 'LYCHEE GREEN TEA', 'label' => 1],
            ['sku' => 'ORB40P', 'packaging' => 'P1', 'name_product' => 'MANDARIN ORANGE BLACK TEA', 'label' => 1],
            ['sku' => 'MG40P', 'packaging' => 'P1', 'name_product' => 'MANGO GREEN TEA', 'label' => 1],
            ['sku' => 'MSC50P', 'packaging' => 'P1', 'name_product' => 'MASALA CHAI', 'label' => 1],
            ['sku' => 'MM40P', 'packaging' => 'P1', 'name_product' => 'MOROCCAN MINT', 'label' => 1],
            ['sku' => 'OSB30P', 'packaging' => 'P1', 'name_product' => 'OSMANTHUS BLACK TEA', 'label' => 1],
            ['sku' => 'PS40P', 'packaging' => 'P1', 'name_product' => 'PASSIONFRUIT GREEN TEA', 'label' => 1],
            ['sku' => 'PG30P', 'packaging' => 'P1', 'name_product' => 'PANDAN GREEN TEA', 'label' => 1],
            ['sku' => 'PH40P', 'packaging' => 'P1', 'name_product' => 'PEACH GREEN TEA', 'label' => 1],
            ['sku' => 'ST40P', 'packaging' => 'P1', 'name_product' => 'STRAWBERRY GREEN TEA', 'label' => 1],
            ['sku' => 'STB40P', 'packaging' => 'P1', 'name_product' => 'STRAWBERRY HIBISCUS', 'label' => 1],

            // Medium packaging P3/P4 - Label 2
            ['sku' => 'BB250P', 'packaging' => 'P3', 'name_product' => 'BLUEBERRY BLACK TEA', 'label' => 2],
            ['sku' => 'CC250P', 'packaging' => 'P3', 'name_product' => 'CHOCOLATE OOLONG TEA', 'label' => 2],
            ['sku' => 'CO250P', 'packaging' => 'P3', 'name_product' => 'COCONUT OOLONG TEA', 'label' => 2],
            ['sku' => 'CF250P', 'packaging' => 'P3', 'name_product' => 'COFFEE & TEA', 'label' => 2],
            ['sku' => 'DM250P', 'packaging' => 'P4', 'name_product' => 'DIVINE MERCY', 'label' => 2],
            ['sku' => 'ERR250P', 'packaging' => 'P3', 'name_product' => 'FRENCH EARL GREY', 'label' => 2],
            ['sku' => 'GI250P', 'packaging' => 'P3', 'name_product' => 'GINGER BLACK TEA', 'label' => 2],
            ['sku' => 'GSP250P', 'packaging' => 'P3', 'name_product' => 'GINSENG PEPPERMINT', 'label' => 2],
            ['sku' => 'GNB250P', 'packaging' => 'P4', 'name_product' => 'GOLDEN & BLOOM', 'label' => 2],
            ['sku' => 'GS250P', 'packaging' => 'P4', 'name_product' => 'GOODNIGHT SLEEP', 'label' => 2],
            ['sku' => 'IDB250P', 'packaging' => 'P4', 'name_product' => 'INDONESIAN BREAKFAST', 'label' => 2],
            ['sku' => 'JG125P', 'packaging' => 'P3', 'name_product' => 'JASMINE GREEN TEA', 'label' => 2],
            ['sku' => 'LOF250P', 'packaging' => 'P4', 'name_product' => 'LADY OF FATIMA', 'label' => 2],
            ['sku' => 'LG250P', 'packaging' => 'P3', 'name_product' => 'LEMON GREEN TEA', 'label' => 2],
            ['sku' => 'LBF250P', 'packaging' => 'P4', 'name_product' => 'LITTLE BIRTHDAY FLOWER', 'label' => 2],
            ['sku' => 'LY250P', 'packaging' => 'P3', 'name_product' => 'LYCHEE GREEN TEA', 'label' => 2],
            ['sku' => 'ORB250P', 'packaging' => 'P4', 'name_product' => 'MANDARIN ORANGE BLACK TEA', 'label' => 2],
            ['sku' => 'MG250P', 'packaging' => 'P3', 'name_product' => 'MANGO GREEN TEA', 'label' => 2],
            ['sku' => 'MGR250P', 'packaging' => 'P4', 'name_product' => 'MARY\'S GARDEN', 'label' => 2],
            ['sku' => 'MSC250P', 'packaging' => 'P3', 'name_product' => 'MASALA CHAI', 'label' => 2],
            ['sku' => 'MM250P', 'packaging' => 'P3', 'name_product' => 'MOROCCAN MINT', 'label' => 2],
            ['sku' => 'OSB250P', 'packaging' => 'P4', 'name_product' => 'OSMANTHUS BLACK TEA', 'label' => 2],
            ['sku' => 'PS250P', 'packaging' => 'P3', 'name_product' => 'PASSIONFRUIT GREEN TEA', 'label' => 2],
            ['sku' => 'PG125P', 'packaging' => 'P3', 'name_product' => 'PANDAN GREEN TEA', 'label' => 2],
            ['sku' => 'PH250P', 'packaging' => 'P3', 'name_product' => 'PEACH GREEN TEA', 'label' => 2],
            ['sku' => 'STN250P', 'packaging' => 'P4', 'name_product' => 'STARRY NIGHT', 'label' => 2],
            ['sku' => 'ST250P', 'packaging' => 'P3', 'name_product' => 'STRAWBERRY GREEN TEA', 'label' => 2],
            ['sku' => 'STB250P', 'packaging' => 'P4', 'name_product' => 'STRAWBERRY HIBISCUS', 'label' => 2],

            // Large packaging P4/P5/V0 - Label 3
            ['sku' => 'BB500P', 'packaging' => 'P5', 'name_product' => 'BLUEBERRY BLACK TEA', 'label' => 3],
            ['sku' => 'CC500P', 'packaging' => 'P4', 'name_product' => 'CHOCOLATE OOLONG TEA', 'label' => 3],
            ['sku' => 'CO500P', 'packaging' => 'P4', 'name_product' => 'COCONUT OOLONG TEA', 'label' => 3],
            ['sku' => 'CF500P', 'packaging' => 'P4', 'name_product' => 'COFFEE & TEA', 'label' => 3],
            ['sku' => 'DM500P', 'packaging' => 'P5', 'name_product' => 'DIVINE MERCY', 'label' => 3],
            ['sku' => 'ERR500P', 'packaging' => 'P4', 'name_product' => 'FRENCH EARL GREY', 'label' => 3],
            ['sku' => 'GI500P', 'packaging' => 'P4', 'name_product' => 'GINGER BLACK TEA', 'label' => 3],
            ['sku' => 'GSP500P', 'packaging' => 'P4', 'name_product' => 'GINSENG PEPPERMINT', 'label' => 3],
            ['sku' => 'GNB500P', 'packaging' => 'P5', 'name_product' => 'GOLDEN & BLOOM', 'label' => 3],
            ['sku' => 'GS500P', 'packaging' => 'V0', 'name_product' => 'GOODNIGHT SLEEP', 'label' => 3],
            ['sku' => 'IDB500P', 'packaging' => 'P5', 'name_product' => 'INDONESIAN BREAKFAST', 'label' => 3],
            ['sku' => 'JG500P', 'packaging' => 'P4', 'name_product' => 'JASMINE GREEN TEA', 'label' => 3],
            ['sku' => 'LOF500P', 'packaging' => 'P5', 'name_product' => 'LADY OF FATIMA', 'label' => 3],
            ['sku' => 'LG500P', 'packaging' => 'P4', 'name_product' => 'LEMON GREEN TEA', 'label' => 3],
            ['sku' => 'LBF500P', 'packaging' => 'P4', 'name_product' => 'LITTLE BIRTHDAY FLOWER', 'label' => 3],
            ['sku' => 'LY500P', 'packaging' => 'P4', 'name_product' => 'LYCHEE GREEN TEA', 'label' => 3],
            ['sku' => 'ORB500P', 'packaging' => 'P5', 'name_product' => 'MANDARIN ORANGE BLACK TEA', 'label' => 3],
            ['sku' => 'MG500P', 'packaging' => 'P4', 'name_product' => 'MANGO GREEN TEA', 'label' => 3],
            ['sku' => 'MGR500P', 'packaging' => 'P5', 'name_product' => 'MARY\'S GARDEN', 'label' => 3],
            ['sku' => 'MSC500P', 'packaging' => 'P4', 'name_product' => 'MASALA CHAI', 'label' => 3],
            ['sku' => 'MM500P', 'packaging' => 'P4', 'name_product' => 'MOROCCAN MINT', 'label' => 3],
            ['sku' => 'OSB500P', 'packaging' => 'P5', 'name_product' => 'OSMANTHUS BLACK TEA', 'label' => 3],
            ['sku' => 'PS500P', 'packaging' => 'P4', 'name_product' => 'PASSIONFRUIT GREEN TEA', 'label' => 3],
            ['sku' => 'PG500P', 'packaging' => 'P5', 'name_product' => 'PANDAN GREEN TEA', 'label' => 3],
            ['sku' => 'PH500P', 'packaging' => 'P4', 'name_product' => 'PEACH GREEN TEA', 'label' => 3],
            ['sku' => 'STN500P', 'packaging' => 'P5', 'name_product' => 'STARRY NIGHT', 'label' => 3],
            ['sku' => 'ST500P', 'packaging' => 'P4', 'name_product' => 'STRAWBERRY GREEN TEA', 'label' => 3],
            ['sku' => 'STB500P', 'packaging' => 'P4', 'name_product' => 'STRAWBERRY HIBISCUS', 'label' => 3],

            // Extra large packaging - Label 4
            ['sku' => 'CC1000P', 'packaging' => 'P5', 'name_product' => 'CHOCOLATE OOLONG TEA', 'label' => 4],
            ['sku' => 'CO1000P', 'packaging' => 'P5', 'name_product' => 'COCONUT OOLONG TEA', 'label' => 4],
            ['sku' => 'CF1000P', 'packaging' => 'P5', 'name_product' => 'COFFEE & TEA', 'label' => 4],
            ['sku' => 'ERR1000P', 'packaging' => 'P5', 'name_product' => 'FRENCH EARL GREY', 'label' => 4],
            ['sku' => 'GI1000P', 'packaging' => 'P5', 'name_product' => 'GINGER BLACK TEA', 'label' => 4],
            ['sku' => 'GSP1000P', 'packaging' => 'P5', 'name_product' => 'GINSENG PEPPERMINT', 'label' => 4],
            ['sku' => 'GSP1000F', 'packaging' => '-', 'name_product' => 'GINSENG PEPPERMINT', 'label' => 4],
            // ['sku' => 'GNB500FX2', 'packaging' => 'F0', 'name_product' => 'GOLDEN & BLOOM', 'label' => 4],
            ['sku' => 'LG1000P', 'packaging' => 'P5', 'name_product' => 'LEMON GREEN TEA', 'label' => 4],
            ['sku' => 'LY1000P', 'packaging' => 'P5', 'name_product' => 'LYCHEE GREEN TEA', 'label' => 4],
            ['sku' => 'MG1000P', 'packaging' => 'P5', 'name_product' => 'MANGO GREEN TEA', 'label' => 4],
            ['sku' => 'MSC1000P', 'packaging' => 'P5', 'name_product' => 'MASALA CHAI', 'label' => 4],
            ['sku' => 'MM1000P', 'packaging' => 'P5', 'name_product' => 'MOROCCAN MINT', 'label' => 4],
            ['sku' => 'PS1000P', 'packaging' => 'P4', 'name_product' => 'PASSIONFRUIT GREEN TEA', 'label' => 4],
            ['sku' => 'PH1000P', 'packaging' => 'P5', 'name_product' => 'PEACH GREEN TEA', 'label' => 4],
            ['sku' => 'ST1000P', 'packaging' => 'P5', 'name_product' => 'STRAWBERRY GREEN TEA', 'label' => 4],

            // Tea bags - Label 5
            ['sku' => 'BB70T', 'packaging' => 'T1', 'name_product' => 'BLUEBERRY BLACK TEA', 'label' => 5],
            ['sku' => 'CC100T', 'packaging' => 'T1', 'name_product' => 'CHOCOLATE OOLONG TEA', 'label' => 5],
            ['sku' => 'CO80T', 'packaging' => 'T1', 'name_product' => 'COCONUT OOLONG TEA', 'label' => 5],
            ['sku' => 'DM25TC', 'packaging' => 'T2', 'name_product' => 'DIVINE MERCY', 'label' => 5],
            ['sku' => 'ERR85T', 'packaging' => 'T1', 'name_product' => 'FRENCH EARL GREY', 'label' => 5],
            ['sku' => 'GNB40T', 'packaging' => 'T1', 'name_product' => 'GOLDEN & BLOOM', 'label' => 5],
            ['sku' => 'GS35T', 'packaging' => 'T1', 'name_product' => 'GOODNIGHT SLEEP', 'label' => 5],
            ['sku' => 'IDB35TC', 'packaging' => 'T2', 'name_product' => 'INDONESIAN BREAKFAST', 'label' => 5],
            ['sku' => 'JG50T', 'packaging' => 'T1', 'name_product' => 'JASMINE GREEN TEA', 'label' => 5],
            ['sku' => 'JB85T', 'packaging' => 'T1', 'name_product' => 'JASMINE PEARL', 'label' => 5],
            ['sku' => 'LOF60TC', 'packaging' => 'T2', 'name_product' => 'LADY OF FATIMA', 'label' => 5],
            ['sku' => 'LG70T', 'packaging' => 'T1', 'name_product' => 'LEMON GREEN TEA', 'label' => 5],
            ['sku' => 'LBF30TC', 'packaging' => 'T2', 'name_product' => 'LITTLE BIRTHDAY FLOWER', 'label' => 5],
            ['sku' => 'LY70T', 'packaging' => 'T1', 'name_product' => 'LYCHEE GREEN TEA', 'label' => 5],
            ['sku' => 'ORB30T', 'packaging' => 'T1', 'name_product' => 'MANDARIN ORANGE BLACK TEA', 'label' => 5],
            ['sku' => 'MG70T', 'packaging' => 'T1', 'name_product' => 'MANGO GREEN TEA', 'label' => 5],
            ['sku' => 'MGR45TC', 'packaging' => 'T2', 'name_product' => 'MARY\'S GARDEN', 'label' => 5],
            ['sku' => 'MSC90T', 'packaging' => 'T1', 'name_product' => 'MASALA CHAI', 'label' => 5],
            ['sku' => 'PS70T', 'packaging' => 'T1', 'name_product' => 'PASSIONFRUIT GREEN TEA', 'label' => 5],
            ['sku' => 'PG35T', 'packaging' => 'T1', 'name_product' => 'PANDAN GREEN TEA', 'label' => 5],
            ['sku' => 'PH70T', 'packaging' => 'T1', 'name_product' => 'PEACH GREEN TEA', 'label' => 5],
            ['sku' => 'RM40T', 'packaging' => 'T1', 'name_product' => 'ROSA MYSTICA', 'label' => 5],
            ['sku' => 'STN25TC', 'packaging' => 'T2', 'name_product' => 'STARRY NIGHT', 'label' => 5],
            ['sku' => 'ST70T', 'packaging' => 'T1', 'name_product' => 'STRAWBERRY GREEN TEA', 'label' => 5],

            // Special packaging
            ['sku' => 'MSC1000F', 'packaging' => 'FG2', 'name_product' => 'MASALA CHAI', 'label' => 10],
            ['sku' => 'MSC500F', 'packaging' => 'F3', 'name_product' => 'MASALA CHAI', 'label' => 10],
            ['sku' => 'LOF1000FC', 'packaging' => 'FG2', 'name_product' => 'LADY OF FATIMA (CUSTOM ALDI)', 'label' => 10],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($artisanTeaProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $categoryId,
                'sku' => $product['sku'],
                'packaging' => $product['packaging'],
                'name_product' => $product['name_product'],
                'label' => $product['label'] ?? null,
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
