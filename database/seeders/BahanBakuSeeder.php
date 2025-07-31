<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate the table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bahan_bakus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /**
         * Kategori mapping:
         * 1 - CRAFTED TEAS
         * 2 - LOOSE LEAF TEA
         * 3 - PURE TISANE
         * 4 - DRIED FRUIT & SPICES
         * 5 - PURE POWDER
         * 6 - SWEET POWDER
         * 7 - LATTE POWDER
         * 8 - JAPANESE TEA BAGS
         * 9 - TEAWARE
         * 10 - ESSENCE
         * 11 - PACKAGING- TEA HEAVEN POUCH
         * 12 - PACKAGING- FOIL FLAT BOTTOM
         * 13 - PACKAGING- FOIL GUSSET / SACHET
         * 14 - PACKAGING- TRANSMETZ ZIPPER
         * 15 - PACKAGING- VACCUM
         * 16 - PACKAGING- TIN CANISTER
         * 17 - BOX
         * 18 - PRINTING & LABELLING
         * 19 - OUTER PACKAGING
         */

        // Sample bahan baku data
        $bahan_bakus = [
            // 1 - CRAFTED TEAS
            ['kategori' => 1, 'sku_induk' => 'BF', 'nama_barang' => 'BLOOMING TEA', 'satuan' => 'PCS'],
            ['kategori' => 1, 'sku_induk' => 'CHMC', 'nama_barang' => 'CHRYSANTHEMUM MINI CAKE', 'satuan' => 'PCS'],
            ['kategori' => 1, 'sku_induk' => 'ORMC', 'nama_barang' => 'ORANGE PEEL MINI CAKE', 'satuan' => 'PCS'],
            ['kategori' => 1, 'sku_induk' => 'PB', 'nama_barang' => 'PU-ERH TEA BALL', 'satuan' => 'PCS'],

            // 2 - LOOSE LEAF TEA
            ['kategori' => 2, 'sku_induk' => 'AV', 'nama_barang' => 'ASSAM VANILLA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'BB', 'nama_barang' => 'BLUEBERRY BLACK TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'BN', 'nama_barang' => 'BANCHA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'BT', 'nama_barang' => 'BROKEN ORANGE PEKOE (BOP)', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'BTC', 'nama_barang' => 'ORGANIC BLACK TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'BTD', 'nama_barang' => 'DUST I', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'CC', 'nama_barang' => 'CHOCOLATE TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'CF', 'nama_barang' => 'COFFEE TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'CO', 'nama_barang' => 'COCONUT OOLONG', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'CT', 'nama_barang' => 'CTC GRADE BP1', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'DCT', 'nama_barang' => 'DUST CTC', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'EB', 'nama_barang' => 'BROKEN ORANGE PEKOE (BOP)', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'ER', 'nama_barang' => 'EARL GREY', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'FD', 'nama_barang' => 'DUST IV', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'GC', 'nama_barang' => 'GENMAICHA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'GI', 'nama_barang' => 'GINGER BLACK TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'GM', 'nama_barang' => 'PEKOE', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'GMS', 'nama_barang' => 'GREEN MASTER', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'GO', 'nama_barang' => 'GREEN GINSENG OOLONG', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'GP', 'nama_barang' => 'GREEN TEA POWDER', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'GTC', 'nama_barang' => 'ORGANIC GREEN TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'GYA', 'nama_barang' => 'GYOKURO MIDDLE GRADE', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'GYB', 'nama_barang' => 'GYOKURO LOWER GRADE', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'HO', 'nama_barang' => 'HOJICHA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'IN', 'nama_barang' => 'INDIAN ASSAM', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'JA', 'nama_barang' => 'JAVANESE JASMINE', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'JB', 'nama_barang' => 'JASMINE PEARL', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'JG', 'nama_barang' => 'JASMINE GREEN TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'LL', 'nama_barang' => 'LOOSE LEAF PU-ERH', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'LG', 'nama_barang' => 'LEMON GREEN TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'LY', 'nama_barang' => 'LYCHEE GREEN TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'MJ', 'nama_barang' => 'FRAGRANT ROLL', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'MO', 'nama_barang' => 'MILKY OOLONG', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'OC', 'nama_barang' => 'OCHA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'OP', 'nama_barang' => 'ORANGE PEKOE (OP)', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'PG', 'nama_barang' => 'PANDAN GREEN TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'PKS', 'nama_barang' => 'PEKOE SUPER', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'PR', 'nama_barang' => 'ROYAL PEARL GUNPOWDER', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'PS', 'nama_barang' => 'PASSIONFRUIT GREEN TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'RD', 'nama_barang' => 'HOJICHA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'SC', 'nama_barang' => 'SENCHA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'SL', 'nama_barang' => 'SILVER NEEDLE', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'ST', 'nama_barang' => 'STRAWBERRY GREEN TEA', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'SN', 'nama_barang' => 'SNAIL', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'TG', 'nama_barang' => 'TIE GUAN YIN', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'WP', 'nama_barang' => 'BAI MU DAN (WHITE BEAUTY)', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'WS', 'nama_barang' => 'WILD SHOU MEI', 'satuan' => 'GRAM'],
            ['kategori' => 2, 'sku_induk' => 'YL', 'nama_barang' => 'YELLOW TEA', 'satuan' => 'GRAM'],

            // 3 - PURE TISANE
            ['kategori' => 3, 'sku_induk' => 'BP', 'nama_barang' => 'TELANG', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'BR', 'nama_barang' => 'BORICHA', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'CH', 'nama_barang' => 'CHRYSANTHEMUM', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'CM', 'nama_barang' => 'CHAMOMILE', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'DN', 'nama_barang' => 'DANDELION', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'FO', 'nama_barang' => 'FORGET ME NOT', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'GL', 'nama_barang' => 'GLOBE AMARANTH', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'JF', 'nama_barang' => 'JASMINE BUDS', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'LR', 'nama_barang' => 'LEMONGRASS IMPORT', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'LRK', 'nama_barang' => 'LEMONGRASS LOKAL', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'LS', 'nama_barang' => 'DRIED LEMON SLICE', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'LV', 'nama_barang' => 'LAVENDER', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'MGD', 'nama_barang' => 'MARIGOLD', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'MT', 'nama_barang' => 'MINT', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'OK', 'nama_barang' => 'SEMPIO ROASTED CORN TEA', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'OR', 'nama_barang' => 'ORANGE PEEL', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'OS', 'nama_barang' => 'OSMANTHUS', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'PBL', 'nama_barang' => 'PEACH BLOSSOM', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'PD', 'nama_barang' => 'DRIED PANDAN', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'RB', 'nama_barang' => 'ROSE BUDS', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'RE', 'nama_barang' => 'ROSELLA EGYPT', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'RS', 'nama_barang' => 'ROSELLA', 'satuan' => 'GRAM'],
            ['kategori' => 3, 'sku_induk' => 'RU', 'nama_barang' => 'ROSELLA UNGU', 'satuan' => 'GRAM'],

            // 4 - DRIED FRUIT & SPICES
            ['kategori' => 4, 'sku_induk' => 'BBP', 'nama_barang' => 'BLACKPEPPER', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'CDM', 'nama_barang' => 'CARDAMOM', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'CLS', 'nama_barang' => 'CLOVES', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'CNM', 'nama_barang' => 'BARK CINNAMON', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'DBB', 'nama_barang' => 'DRIED BLUEBERRY 500GR', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'DCC', 'nama_barang' => 'CACAO NIBS 250GR', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'DCF', 'nama_barang' => 'EXCELSO ROBUSTA 200GR', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'DCH', 'nama_barang' => 'CACAO HUSK 200GR', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'DMG', 'nama_barang' => 'MANGO BITS 250GR', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'DPH', 'nama_barang' => 'DRIED PEACH 500GR', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'DPP', 'nama_barang' => 'PINK PEPPERCORN 252GR', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'DST', 'nama_barang' => 'DRIED STRAWBERRY', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'GIS', 'nama_barang' => 'GINGER SLICES', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'GJ', 'nama_barang' => 'GOJI BERRY', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'GR', 'nama_barang' => 'GINSENG ROOT 500GR', 'satuan' => 'GRAM'],
            ['kategori' => 4, 'sku_induk' => 'STA', 'nama_barang' => 'STAR ANISE', 'satuan' => 'GRAM'],

            // 5 - PURE POWDER
            ['kategori' => 5, 'sku_induk' => 'MA', 'nama_barang' => 'MATCHA 02', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'MB', 'nama_barang' => 'MATCHA 01', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'MC', 'nama_barang' => 'UJI CEREMONIAL MATCHA', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'MCF', 'nama_barang' => 'CEREMONIAL MATCHA FINEST', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'MCJ', 'nama_barang' => 'CEREMONIAL MATCHA TIN', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'MU', 'nama_barang' => 'UJI CULINARY MATCHA', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'PC', 'nama_barang' => 'PURE CHAI POWDER', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'PER', 'nama_barang' => 'PURE EARL GREY', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'HP', 'nama_barang' => 'PURE HOJICHA', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'PTR', 'nama_barang' => 'PURE TARO', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'PTT', 'nama_barang' => 'PURE THAI TEA', 'satuan' => 'GRAM'],
            ['kategori' => 5, 'sku_induk' => 'SW', 'nama_barang' => 'SWISS CHOCOLATE', 'satuan' => 'GRAM'],

            // 6 - SWEET POWDER
            ['kategori' => 6, 'sku_induk' => 'AS', 'nama_barang' => 'GULA AREN', 'satuan' => 'GRAM'],
            ['kategori' => 6, 'sku_induk' => 'HPS', 'nama_barang' => 'SWEET HOJICHA', 'satuan' => 'GRAM'],
            ['kategori' => 6, 'sku_induk' => 'MGI', 'nama_barang' => 'SWEET MATCHA GINGER', 'satuan' => 'GRAM'],
            ['kategori' => 6, 'sku_induk' => 'MS', 'nama_barang' => 'SWEET MATCHA', 'satuan' => 'GRAM'],

            // 7 - LATTE POWDER
            ['kategori' => 7, 'sku_induk' => 'BNL', 'nama_barang' => 'BANANA LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'BSL', 'nama_barang' => 'BLACK SESAME LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'CL', 'nama_barang' => 'CHAI LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'CR', 'nama_barang' => 'CHARCOAL LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'CCH', 'nama_barang' => 'CREAM CHEESE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'ERL', 'nama_barang' => 'EARL GREY LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'GCL', 'nama_barang' => 'GENMAICHA LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'GIL', 'nama_barang' => 'GINGER LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'TUL', 'nama_barang' => 'GOLDEN TURMERIC LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'HL', 'nama_barang' => 'HOJICHA LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'MGL', 'nama_barang' => 'MANGO LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'ML', 'nama_barang' => 'MATCHA LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'PDL', 'nama_barang' => 'PANDAN LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'RVL', 'nama_barang' => 'RED VELVET LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'RMT', 'nama_barang' => 'ROYAL MILK TEA', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'SA', 'nama_barang' => 'SAKURA LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'STL', 'nama_barang' => 'STRAWBERRY LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'TR', 'nama_barang' => 'TARO LATTE', 'satuan' => 'GRAM'],
            ['kategori' => 7, 'sku_induk' => 'TT', 'nama_barang' => 'THAI TEA LATTE', 'satuan' => 'GRAM'],

            // 8 - JAPANESE TEA BAGS
            ['kategori' => 8, 'sku_induk' => 'GCTB', 'nama_barang' => 'GENMAICHA TEABAGS', 'satuan' => 'PCS'],
            ['kategori' => 8, 'sku_induk' => 'HOTB', 'nama_barang' => 'HOJICHA TEABAGS', 'satuan' => 'PCS'],
            ['kategori' => 8, 'sku_induk' => 'SCTB', 'nama_barang' => 'KABUSECHA TEABAGS', 'satuan' => 'PCS'],

            // 9 - TEAWARE
            // ['kategori' => 9, 'sku_induk' => 'TA00', 'nama_barang' => 'TEA BAG', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA01', 'nama_barang' => 'INFUSER BOTTLE 420 ML SILVER', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA02', 'nama_barang' => 'STRAINER STAINLESS', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA03', 'nama_barang' => 'IRON TEAPOT 600 ML', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA04', 'nama_barang' => 'IRON TEAPOT 800 ML', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA05', 'nama_barang' => 'IRON TEAPOT 12 LITER', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA06', 'nama_barang' => 'MATCHA WHISK ELECTRIC', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA07', 'nama_barang' => 'STAINLESS STRAW', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA08', 'nama_barang' => 'MATCHA WHISK 100 PRONGS', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA09', 'nama_barang' => 'TEKO PITCHER 1 L', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA10', 'nama_barang' => 'TOPLES KACA 280 ML', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA11', 'nama_barang' => 'GELAS 3IN1 320 ML', 'satuan' => 'PCS'],
            // ['kategori' => 9, 'sku_induk' => 'TA12', 'nama_barang' => 'MATCHA SPOON CHASAKU', 'satuan' => 'PCS'],

            // 10 - ESSENCE
            ['kategori' => 10, 'sku_induk' => 'BB01', 'nama_barang' => 'BLUEBBERY ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'CC01', 'nama_barang' => 'CHOCOLATE ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'CO01', 'nama_barang' => 'KELAPA BAKAR ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'CF01', 'nama_barang' => 'COFFEE NOIR ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'GI02', 'nama_barang' => 'GINGER ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'LG02', 'nama_barang' => 'LEMON ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'LY02', 'nama_barang' => 'LYCHEE ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'OR02', 'nama_barang' => 'ORANGE ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'MG01', 'nama_barang' => 'MANGO PURE ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'MT01', 'nama_barang' => 'PEPPERMINT ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'PS01', 'nama_barang' => 'PASSIONFRUIT ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'PH01', 'nama_barang' => 'PEACH ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'ST01', 'nama_barang' => 'STRAWBERRY PURE ESSENCE', 'satuan' => 'PCS'],
            ['kategori' => 10, 'sku_induk' => 'VA01', 'nama_barang' => 'VANILLA PURE ESSENCE', 'satuan' => 'PCS'],

            // 11 - PACKAGING- TEA HEAVEN POUCH
            ['kategori' => 11, 'sku_induk' => 'P0', 'nama_barang' => '9 x 15', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'P1', 'nama_barang' => '12 x 20', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'P2', 'nama_barang' => '14 x 22', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'P3', 'nama_barang' => '18 x 26', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'P4', 'nama_barang' => '22 X 32', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'P5', 'nama_barang' => '25 X 35', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'PC1', 'nama_barang' => '12 X 20 (CUSTOM-BLACK)', 'satuan' => 'PCS'],

            // 12 - PACKAGING- FOIL FLAT BOTTOM
            ['kategori' => 12, 'sku_induk' => 'F0', 'nama_barang' => '9.5 X 15', 'satuan' => 'PCS'],
            ['kategori' => 12, 'sku_induk' => 'F1', 'nama_barang' => '11.5 x 18', 'satuan' => 'PCS'],
            ['kategori' => 12, 'sku_induk' => 'F2', 'nama_barang' => '15.5 x 23.5', 'satuan' => 'PCS'],
            ['kategori' => 12, 'sku_induk' => 'F3', 'nama_barang' => '20 x 32', 'satuan' => 'PCS'],
            ['kategori' => 12, 'sku_induk' => 'F4', 'nama_barang' => '17.5 x 30', 'satuan' => 'PCS'],

            // 13 - PACKAGING- FOIL GUSSET / SACHET
            ['kategori' => 13, 'sku_induk' => 'FG0', 'nama_barang' => '7.5 x 17', 'satuan' => 'PCS'],
            ['kategori' => 13, 'sku_induk' => 'FG1', 'nama_barang' => '9 x 18', 'satuan' => 'PCS'],
            ['kategori' => 13, 'sku_induk' => 'FG2', 'nama_barang' => '24 X 40', 'satuan' => 'PCS'],
            ['kategori' => 13, 'sku_induk' => 'FG3', 'nama_barang' => '13 X 18', 'satuan' => 'PCS'],

            // 14 - PACKAGING- TRANSMETZ ZIPPER
            ['kategori' => 14, 'sku_induk' => 'Z0', 'nama_barang' => '8 X 13', 'satuan' => 'PCS'],
            ['kategori' => 14, 'sku_induk' => 'Z1', 'nama_barang' => '25 X 35', 'satuan' => 'PCS'],
            ['kategori' => 14, 'sku_induk' => 'Z2', 'nama_barang' => '28 X 38', 'satuan' => 'PCS'],
            ['kategori' => 14, 'sku_induk' => 'Z3', 'nama_barang' => '6 X 22', 'satuan' => 'PCS'],

            // 15 - PACKAGING- VACCUM
            ['kategori' => 15, 'sku_induk' => 'V0', 'nama_barang' => '28 X 40', 'satuan' => 'PCS'],

            // 16 - PACKAGING- TIN CANISTER
            ['kategori' => 16, 'sku_induk' => 'T1', 'nama_barang' => 'TEA HEAVEN SQUARE METAL TIN', 'satuan' => 'PCS'],
            ['kategori' => 16, 'sku_induk' => 'T2', 'nama_barang' => 'ALUMUNIUM TIN CANISTER', 'satuan' => 'PCS'],
            ['kategori' => 16, 'sku_induk' => 'T3', 'nama_barang' => 'MATCHA JAR POT ALUMUNIUM', 'satuan' => 'PCS'],

            // 17 - BOX
            ['kategori' => 17, 'sku_induk' => 'B1', 'nama_barang' => 'TEA HEAVEN BAMBOO', 'satuan' => 'PCS'],
            ['kategori' => 17, 'sku_induk' => 'BXS', 'nama_barang' => 'TEA HEAVEN SMALL HARDBOX 15 x 9 x 8', 'satuan' => 'PCS'],
            ['kategori' => 17, 'sku_induk' => 'BS', 'nama_barang' => 'TEA HEAVEN BIG HARDBOX 22 x 18 x 9', 'satuan' => 'PCS'],
            ['kategori' => 17, 'sku_induk' => 'BXM', 'nama_barang' => 'TEA HEAVEN MATCHA GREY BOX 19 x 16 x 8', 'satuan' => 'PCS'],

            // 18 - PRINTING & LABELLING
            ['kategori' => 18, 'sku_induk' => 'S1', 'nama_barang' => 'STICKER POUCH 5 x 17 14/A3', 'satuan' => 'PCS'],
            ['kategori' => 18, 'sku_induk' => 'S2', 'nama_barang' => 'STICKER TIN 11.5 x 5.7 17/A3', 'satuan' => 'PCS'],
            ['kategori' => 18, 'sku_induk' => 'S3', 'nama_barang' => 'STICKER TIN CANISTER 13 x 5 18/A3', 'satuan' => 'PCS'],
            ['kategori' => 18, 'sku_induk' => 'S4', 'nama_barang' => 'STICKER MATCHA 10 x 3 42/A3', 'satuan' => 'PCS'],
            ['kategori' => 18, 'sku_induk' => 'S5', 'nama_barang' => 'SAMPLE STICKER 3 x 3 126/A3', 'satuan' => 'PCS'],
            ['kategori' => 18, 'sku_induk' => 'S6', 'nama_barang' => 'TEA GUIDE BROSUR A6 MATTE 150GSM', 'satuan' => 'PCS'],
            ['kategori' => 18, 'sku_induk' => 'S7', 'nama_barang' => 'THERMAL STICKER 60 x 30MM 500 PCS', 'satuan' => 'PCS'],
            ['kategori' => 18, 'sku_induk' => 'S8', 'nama_barang' => 'THERMAL RESI 80 x 120MM 500 PCS', 'satuan' => 'PCS'],
            ['kategori' => 18, 'sku_induk' => 'S9', 'nama_barang' => 'UNBOXING LABEL 7 x 3CM 1000PCS', 'satuan' => 'PCS'],
            ['kategori' => 18, 'sku_induk' => 'S10', 'nama_barang' => 'RIBBON TAPE CODING 30MMx100M', 'satuan' => 'PCS'],

            // 19 - OUTER PACKAGING
            ['kategori' => 19, 'sku_induk' => 'O1', 'nama_barang' => 'TEA HEAVEN RIBBON', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'O2', 'nama_barang' => 'BUBBLEWRAP 1 ROLL 3KG', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'O3', 'nama_barang' => 'SACHET SAMPLE', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'L1', 'nama_barang' => 'SOLATIP 24 x 80M', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'L2', 'nama_barang' => 'LAKBAN BENING / COKLAT BESAR', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'L3', 'nama_barang' => 'LAKBAN MERAH FRAGILE', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'PM1', 'nama_barang' => 'POLYMAILER 17 X 30 100PCS', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'PM2', 'nama_barang' => 'POLYMAILER 20 X 30 100PCS', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'PM3', 'nama_barang' => 'POLYMAILER 25 X 40 100PCS', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'PM4', 'nama_barang' => 'POLYMAILER 28 X 40 100PCS', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'PM5', 'nama_barang' => 'POLYMAILER 34 X 46 100PCS', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'PM6', 'nama_barang' => 'POLYMAILER 38 X 52 100PCS', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'BX1', 'nama_barang' => 'BOX 9X9X23', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'BX2', 'nama_barang' => 'BOX 10X10X8', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'BX3', 'nama_barang' => 'BOX 20X11X11', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'BX4', 'nama_barang' => 'BOX 20X20X15', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'BX5', 'nama_barang' => 'BOX 21X14X8', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'BX6', 'nama_barang' => 'BOX 25X17X10', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'BX7', 'nama_barang' => 'BOX 35X25X20', 'satuan' => 'PCS'],
            ['kategori' => 19, 'sku_induk' => 'BX8', 'nama_barang' => 'BOX 50X35X25', 'satuan' => 'PCS'],

            // Additional entries
            ['kategori' => 11, 'sku_induk' => 'LC', 'nama_barang' => 'LOW CAFFEINE', 'satuan' => 'GRAM'],
            ['kategori' => 11, 'sku_induk' => 'EP1', 'nama_barang' => 'ALU FOIL 12X18', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'EP5', 'nama_barang' => 'ALU FOIL 25 X 35', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'EPMA', 'nama_barang' => '12 X 18 BARISTA MATCHA A', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'EPMB', 'nama_barang' => '12 X 18 BAKER MATCHA B', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'EPMU', 'nama_barang' => '12 X 18 UJI CULINARY MATCHA', 'satuan' => 'PCS'],
            ['kategori' => 11, 'sku_induk' => 'EPMC', 'nama_barang' => '12 X 18 CEREMONIAL MATCHA', 'satuan' => 'PCS'],
        ];

        // Insert data with progress reporting
        $totalToInsert = count($bahan_bakus);
        $insertedCount = 0;

        $this->command->info("Starting to seed $totalToInsert bahan baku records...");

        foreach ($bahan_bakus as $bahan_baku) {
            BahanBaku::create($bahan_baku);
            $insertedCount++;

            if ($insertedCount % 100 === 0) {
                $this->command->info("Processed $insertedCount / $totalToInsert records...");
            }
        }

        $this->command->info("Finished seeding. Total records inserted: $insertedCount");
    }
}
