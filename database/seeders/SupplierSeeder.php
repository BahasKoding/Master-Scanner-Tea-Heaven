<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\CategorySupplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data supplier dengan format [code => product_name]
        $suppliers = [
            // CRAFTED TEAS
            'BF' => 'BLOOMING TEA',
            'CHMC' => 'CHRYSANTHEMUM MINI CAKE',
            'ORMC' => 'ORANGE PEEL MINI CAKE',
            'PB' => 'PU-ERH TEA BALL',

            // LOOSE LEAF TEA
            'AV' => 'ASSAM VANILLA',
            'BB' => 'BLUEBERRY BLACK TEA',
            'BN' => 'BANCHA',
            'BT' => 'BROKEN ORANGE PEKOE (BOP)',
            'BTC' => 'ORGANIC BLACK TEA',
            'BTD' => 'DUST I',
            'CC' => 'CHOCOLATE TEA',
            'CF' => 'COFFEE TEA',
            'CO' => 'COCONUT OOLONG',
            'CT' => 'CTC GRADE BP1',
            'DCT' => 'DUST CTC',
            'EB' => 'BROKEN ORANGE PEKOE (BOP)',
            'ER' => 'EARL GREY',
            'FD' => 'DUST IV',
            'GC' => 'GENMAICHA',
            'GI' => 'GINGER BLACK TEA',
            'GM' => 'PEKOE',
            'GMS' => 'GREEN MASTER',
            'GO' => 'GREEN GINSENG OOLONG',
            'GP' => 'GREEN TEA POWDER',
            'GTC' => 'ORGANIC GREEN TEA',
            'GYA' => 'GYOKURO MIDDLE GRADE',
            'GYB' => 'GYOKURO LOWER GRADE',
            'HO' => 'HOJICHA',
            'IN' => 'INDIAN ASSAM',
            'JA' => 'JAVANESE JASMINE',
            'JB' => 'JASMINE PEARL',
            'JG' => 'JASMINE GREEN TEA',
            'LL' => 'LOOSE LEAF PU-ERH',
            'LG' => 'LEMON GREEN TEA',
            'LY' => 'LYCHEE GREEN TEA',
            'MJ' => 'FRAGRANT ROLL',
            'MO' => 'MILKY OOLONG',
            'OC' => 'OCHA',
            'OP' => 'ORANGE PEKOE (OP)',
            'PG' => 'PANDAN GREEN TEA',
            'PKS' => 'PEKOE SUPER',
            'PR' => 'ROYAL PEARL GUNPOWDER',
            'PS' => 'PASSIONFRUIT GREEN TEA',
            'RD' => 'HOJICHA',
            'SC' => 'SENCHA',
            'SL' => 'SILVER NEEDLE',
            'ST' => 'STRAWBERRY GREEN TEA',
            'SN' => 'SNAIL',
            'TG' => 'TIE GUAN YIN',
            'WP' => 'BAI MU DAN (WHITE BEAUTY)',
            'WS' => 'WILD SHOU MEI',
            'YL' => 'YELLOW TEA',

            // PURE TISANE
            'BP' => 'TELANG',
            'BR' => 'BORICHA',
            'CH' => 'CHRYSANTHEMUM',
            'CM' => 'CHAMOMILE',
            'DN' => 'DANDELION',
            'FO' => 'FORGET ME NOT',
            'GL' => 'GLOBE AMARANTH',
            'JF' => 'JASMINE BUDS',
            'LR' => 'LEMONGRASS IMPORT',
            'LRK' => 'LEMONGRASS LOKAL',
            'LS' => 'DRIED LEMON SLICE',
            'LV' => 'LAVENDER',
            'MGD' => 'MARIGOLD',
            'MT' => 'MINT',
            'OK' => 'SEMPIO ROASTED CORN TEA',
            'OR' => 'ORANGE PEEL',
            'OS' => 'OSMANTHUS',
            'PBL' => 'PEACH BLOSSOM',
            'PD' => 'DRIED PANDAN',
            'RB' => 'ROSE BUDS',
            'RE' => 'ROSELLA EGYPT',
            'RS' => 'ROSELLA',
            'RU' => 'ROSELLA UNGU',

            // DRIED FRUIT & SPICES
            'BBP' => 'BLACKPEPPER',
            'CDM' => 'CARDAMOM',
            'CLS' => 'CLOVES',
            'CNM' => 'BARK CINNAMON',
            'DBB' => 'DRIED BLUEBERRY 500GR',
            'DCC' => 'CACAO NIBS 250GR',
            'DCF' => 'EXCELSO ROBUSTA 200GR',
            'DCH' => 'CACAO HUSK 200GR',
            'DMG' => 'MANGO BITS 250GR',
            'DPH' => 'DRIED PEACH 500GR',
            'DPP' => 'PINK PEPPERCORN 252GR',
            'DST' => 'DRIED STRAWBERRY',
            'GIS' => 'GINGER SLICES',
            'GJ' => 'GOJI BERRY',
            'GR' => 'GINSENG ROOT 500GR',
            'STA' => 'STAR ANISE',

            // PURE POWDER
            'MA' => 'MATCHA 02',
            'MB' => 'MATCHA 01',
            'MC' => 'UJI CEREMONIAL MATCHA',
            'MCF' => 'CEREMONIAL MATCHA FINEST',
            'MCJ' => 'CEREMONIAL MATCHA TIN',
            'MU' => 'UJI CULINARY MATCHA',
            'PC' => 'PURE CHAI POWDER',
            'PER' => 'PURE EARL GREY',
            'HP' => 'PURE HOJICHA',
            'PTR' => 'PURE TARO',
            'PTT' => 'PURE THAI TEA',
            'SW' => 'SWISS CHOCOLATE',

            // SWEET POWDER
            'AS' => 'GULA AREN',
            'HPS' => 'SWEET HOJICHA',
            'MGI' => 'SWEET MATCHA GINGER',
            'MS' => 'SWEET MATCHA',

            // LATTE POWDER
            'BNL' => 'BANANA LATTE',
            'BSL' => 'BLACK SESAME LATTE',
            'CL' => 'CHAI LATTE',
            'CR' => 'CHARCOAL LATTE',
            'CCH' => 'CREAM CHEESE',
            'ERL' => 'EARL GREY LATTE',
            'GCL' => 'GENMAICHA LATTE',
            'GIL' => 'GINGER LATTE',
            'TUL' => 'GOLDEN TURMERIC LATTE',
            'HL' => 'HOJICHA LATTE',
            'MGL' => 'MANGO LATTE',
            'ML' => 'MATCHA LATTE',
            'PDL' => 'PANDAN LATTE',
            'RVL' => 'RED VELVET LATTE',
            'RMT' => 'ROYAL MILK TEA',
            'SA' => 'SAKURA LATTE',
            'STL' => 'STRAWBERRY LATTE',
            'TR' => 'TARO LATTE',
            'TT' => 'THAI TEA LATTE',

            // JAPANESE TEA BAGS
            'GCTB' => 'GENMAICHA TEABAGS',
            'HOTB' => 'HOJICHA TEABAGS',
            'SCTB' => 'KABUSECHA TEABAGS',

            // TEAWARE
            'TA00' => 'TEA BAG',
            'TA01' => 'INFUSER BOTTLE 420 ML SILVER',
            'TA02' => 'STRAINER STAINLESS',
            'TA03' => 'IRON TEAPOT 600 ML',
            'TA04' => 'IRON TEAPOT 800 ML',
            'TA05' => 'IRON TEAPOT 12 LITER',
            'TA06' => 'MATCHA WHISK ELECTRIC',
            'TA07' => 'STAINLESS STRAW',
            'TA08' => 'MATCHA WHISK 100 PRONGS',
            'TA09' => 'TEKO PITCHER 1 L',
            'TA10' => 'TOPLES KACA 280 ML',
            'TA11' => 'GELAS 3IN1 320 ML',
            'TA12' => 'MATCHA SPOON CHASAKU',

            // ESSENCE
            'BB01' => 'BLUEBBERY ESSENCE',
            'CC01' => 'CHOCOLATE ESSENCE',
            'CO01' => 'KELAPA BAKAR ESSENCE',
            'CF01' => 'COFFEE NOIR ESSENCE',
            'GI02' => 'GINGER ESSENCE',
            'LG02' => 'LEMON ESSENCE',
            'LY02' => 'LYCHEE ESSENCE',
            'OR02' => 'ORANGE ESSENCE',
            'MG01' => 'MANGO PURE ESSENCE',
            'MT01' => 'PEPPERMINT ESSENCE',
            'PS01' => 'PASSIONFRUIT ESSENCE',
            'PH01' => 'PEACH ESSENCE',
            'ST01' => 'STRAWBERRY PURE ESSENCE',
            'VA01' => 'VANILLA PURE ESSENCE',

            // PACKAGING- TEA HEAVEN POUCH
            'P0' => '9 x 15',
            'P1' => '12 x 20',
            'P2' => '14 x 22',
            'P3' => '18 x 26',
            'P4' => '22 X 32',
            'P5' => '25 X 35',
            'PC1' => '12 X 20 (CUSTOM-BLACK)',

            // PACKAGING- FOIL FLAT BOTTOM
            'F0' => '9.5 X 15',
            'F1' => '11.5 x 18',
            'F2' => '15.5 x 23.5',
            'F3' => '20 x 32',
            'F4' => '17.5 x 30',

            // PACKAGING- FOIL GUSSET / SACHET
            'FG0' => '7.5 x 17',
            'FG1' => '9 x 18',
            'FG2' => '24 X 40',
            'FG3' => '13 X 18',

            // PACKAGING- TRANSMETZ ZIPPER
            'Z0' => '8 X 13',
            'Z1' => '25 X 35',
            'Z2' => '28 X 38',
            'Z3' => '6 X 22',

            // PACKAGING- VACCUM
            'V0' => '28 X 40',

            // PACKAGING- TIN CANISTER
            'T1' => 'TEA HEAVEN SQUARE METAL TIN',
            'T2' => 'ALUMUNIUM TIN CANISTER',
            'T3' => 'MATCHA JAR POT ALUMUNIUM',

            // BOX
            'B1' => 'TEA HEAVEN BAMBOO',
            'BXS' => 'TEA HEAVEN SMALL HARDBOX 15 x 9 x 8',
            'BS' => 'TEA HEAVEN BIG HARDBOX 22 x 18 x 9',
            'BXM' => 'TEA HEAVEN MATCHA GREY BOX 19 x 16 x 8',

            // PRINTING & LABELLING
            'S1' => 'STICKER POUCH 5 x 17 14/A3',
            'S2' => 'STICKER TIN 11.5 x 5.7 17/A3',
            'S3' => 'STICKER TIN CANISTER 13 x 5 18/A3',
            'S4' => 'STICKER MATCHA 10 x 3 42/A3',
            'S5' => 'SAMPLE STICKER 3 x 3 126/A3',
            'S6' => 'TEA GUIDE BROSUR A6 MATTE 150GSM',
            'S7' => 'THERMAL STICKER 60 x 30MM 500 PCS',
            'S8' => 'THERMAL RESI 80 x 120MM 500 PCS',
            'S9' => 'UNBOXING LABEL 7 x 3CM 1000PCS',
            'S10' => 'RIBBON TAPE CODING 30MMx100M',

            // OUTER PACKAGING
            'O1' => 'TEA HEAVEN RIBBON',
            'O2' => 'BUBBLEWRAP 1 ROLL 3KG',
            'O3' => 'SACHET SAMPLE',
            'L1' => 'SOLATIP 24 x 80M',
            'L2' => 'LAKBAN BENING / COKLAT BESAR',
            'L3' => 'LAKBAN MERAH FRAGILE',
            'PM1' => 'POLYMAILER 17 X 30 100PCS',
            'PM2' => 'POLYMAILER 20 X 30 100PCS',
            'PM3' => 'POLYMAILER 25 X 40 100PCS',
            'PM4' => 'POLYMAILER 28 X 40 100PCS',
            'PM5' => 'POLYMAILER 34 X 46 100PCS',
            'PM6' => 'POLYMAILER 38 X 52 100PCS',
            'BX1' => 'BOX 9X9X23',
            'BX2' => 'BOX 10X10X8',
            'BX3' => 'BOX 20X11X11',
            'BX4' => 'BOX 20X20X15',
            'BX5' => 'BOX 21X14X8',
            'BX6' => 'BOX 25X17X10',
            'BX7' => 'BOX 35X25X20',
            'BX8' => 'BOX 50X35X25',
        ];

        // Mapping kategori
        $categories = [
            'CRAFTED TEAS' => ['BF', 'CHMC', 'ORMC', 'PB'],
            'LOOSE LEAF TEA' => ['AV', 'BB', 'BN', 'BT', 'BTC', 'BTD', 'CC', 'CF', 'CO', 'CT', 'DCT', 'EB', 'ER', 'FD', 'GC', 'GI', 'GM', 'GMS', 'GO', 'GP', 'GTC', 'GYA', 'GYB', 'HO', 'IN', 'JA', 'JB', 'JG', 'LL', 'LG', 'LY', 'MJ', 'MO', 'OC', 'OP', 'PG', 'PKS', 'PR', 'PS', 'RD', 'SC', 'SL', 'ST', 'SN', 'TG', 'WP', 'WS', 'YL'],
            'PURE TISANE' => ['BP', 'BR', 'CH', 'CM', 'DN', 'FO', 'GL', 'JF', 'LR', 'LRK', 'LS', 'LV', 'MGD', 'MT', 'OK', 'OR', 'OS', 'PBL', 'PD', 'RB', 'RE', 'RS', 'RU'],
            'DRIED FRUIT & SPICES' => ['BBP', 'CDM', 'CLS', 'CNM', 'DBB', 'DCC', 'DCF', 'DCH', 'DMG', 'DPH', 'DPP', 'DST', 'GIS', 'GJ', 'GR', 'STA'],
            'PURE POWDER' => ['MA', 'MB', 'MC', 'MCF', 'MCJ', 'MU', 'PC', 'PER', 'HP', 'PTR', 'PTT', 'SW'],
            'SWEET POWDER' => ['AS', 'HPS', 'MGI', 'MS'],
            'LATTE POWDER' => ['BNL', 'BSL', 'CL', 'CR', 'CCH', 'ERL', 'GCL', 'GIL', 'TUL', 'HL', 'MGL', 'ML', 'PDL', 'RVL', 'RMT', 'SA', 'STL', 'TR', 'TT'],
            'JAPANESE TEA BAGS' => ['GCTB', 'HOTB', 'SCTB'],
            'TEAWARE' => ['TA00', 'TA01', 'TA02', 'TA03', 'TA04', 'TA05', 'TA06', 'TA07', 'TA08', 'TA09', 'TA10', 'TA11', 'TA12'],
            'ESSENCE' => ['BB01', 'CC01', 'CO01', 'CF01', 'GI02', 'LG02', 'LY02', 'OR02', 'MG01', 'MT01', 'PS01', 'PH01', 'ST01', 'VA01'],
            'PACKAGING- TEA HEAVEN POUCH' => ['P0', 'P1', 'P2', 'P3', 'P4', 'P5', 'PC1'],
            'PACKAGING- FOIL FLAT BOTTOM' => ['F0', 'F1', 'F2', 'F3', 'F4'],
            'PACKAGING- FOIL GUSSET / SACHET' => ['FG0', 'FG1', 'FG2', 'FG3'],
            'PACKAGING- TRANSMETZ ZIPPER' => ['Z0', 'Z1', 'Z2', 'Z3'],
            'PACKAGING- VACCUM' => ['V0'],
            'PACKAGING- TIN CANISTER' => ['T1', 'T2', 'T3'],
            'BOX' => ['B1', 'BXS', 'BS', 'BXM'],
            'PRINTING & LABELLING' => ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'S9', 'S10'],
            'OUTER PACKAGING' => ['O1', 'O2', 'O3', 'L1', 'L2', 'L3', 'PM1', 'PM2', 'PM3', 'PM4', 'PM5', 'PM6', 'BX1', 'BX2', 'BX3', 'BX4', 'BX5', 'BX6', 'BX7', 'BX8'],
        ];

        // Unit default berdasarkan kategori
        $unitByCategory = [
            'CRAFTED TEAS' => 'PCS',
            'LOOSE LEAF TEA' => 'GRAM',
            'PURE TISANE' => 'GRAM',
            'DRIED FRUIT & SPICES' => 'GRAM',
            'PURE POWDER' => 'GRAM',
            'SWEET POWDER' => 'GRAM',
            'LATTE POWDER' => 'GRAM',
            'JAPANESE TEA BAGS' => 'PCS',
            'TEAWARE' => 'PCS',
            'ESSENCE' => 'PCS',
            'PACKAGING- TEA HEAVEN POUCH' => 'PCS',
            'PACKAGING- FOIL FLAT BOTTOM' => 'PCS',
            'PACKAGING- FOIL GUSSET / SACHET' => 'PCS',
            'PACKAGING- TRANSMETZ ZIPPER' => 'PCS',
            'PACKAGING- VACCUM' => 'PCS',
            'PACKAGING- TIN CANISTER' => 'PCS',
            'BOX' => 'PCS',
            'PRINTING & LABELLING' => 'PCS',
            'OUTER PACKAGING' => 'PCS',
        ];

        // Loop melalui semua produk
        foreach ($suppliers as $code => $productName) {
            // Cari kategori untuk kode ini
            $categoryName = null;
            foreach ($categories as $catName => $codes) {
                if (in_array($code, $codes)) {
                    $categoryName = $catName;
                    break;
                }
            }

            if (!$categoryName) {
                continue; // Skip jika tidak ada kategori yang cocok
            }

            // Cari ID kategori
            $category = CategorySupplier::where('name', $categoryName)->first();
            if (!$category) {
                continue; // Skip jika kategori tidak ditemukan
            }

            // Tentukan unit berdasarkan kategori
            $unit = $unitByCategory[$categoryName] ?? 'PCS';

            // Simpan data supplier
            Supplier::updateOrCreate(
                ['code' => $code],
                [
                    'category_supplier_id' => $category->id,
                    'code' => $code,
                    'product_name' => $productName,
                    'unit' => $unit
                ]
            );
        }
    }
}
