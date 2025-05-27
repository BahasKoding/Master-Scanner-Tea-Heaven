<?php

/**
 * Script untuk memproses data sales dan mengidentifikasi SKU duplikat
 * Jika ada SKU yang double dalam satu No Resi, maka qty akan diset menjadi 2
 */

// Data yang diberikan user
$salesData = [
    'SPXID058775903995' => ['MU100P'],
    'SPXID054746407365' => ['MU100P'],
    'SPXID054033323465' => ['MU100P'],
    'SPXID050065877445' => ['MU100P'],
    '250525RQNAB6HY' => ['MC100P'],
    '250524Q7CBUHW2' => ['MC30T'],
    'SPXID054752339915' => ['MA250F'],
    'SPXID053378941005' => ['MA250F'],
    'SPXID055350108745' => ['MA250F'],
    'SPXID050068077025' => ['MU250F'],
    'SPXID054551081915' => ['MA250F'],
    'SPXID056520603805' => ['MS100P'],
    'SPXID051048244305' => ['ML100P'],
    'SPXID056287860715' => ['MU100P'],
    'SPXID051027890445' => ['MU100P'],
    'SPXID057823635445' => ['MU100P'],
    'SPXID053256286095' => ['MU100P'],
    'SPXID058713930525' => ['MA250F'],
    'SPXID054168159785' => ['ERL100P', 'MA100P'],
    'SPXID058148825435' => ['MA100P'],
    'SPXID059142638255' => ['MA100P'],
    'SPXID057312060045' => ['MU100P', 'MA100P'],
    'SPXID055176294365' => ['MA100P'],
    'SPXID053490642465' => ['MA100P'],
    'SPXID057835781575' => ['MA100P'],
    'SPXID055469660765' => ['MA100P'],
    'JY46405565766' => ['MA100P'],
    'SPXID053383868375' => ['MA100P'],
    'SPXID052339749575' => ['RD1000P'],
    'SPXID056207815625' => ['GM500P'],
    'SPXID052146092315' => ['ER1000P'],
    'SPXID052434528615' => ['SW1000F'],
    'SPXID053582811025' => ['ER250P', 'ER250P'], // SKU duplikat
    'JY50489643200' => ['CM50P', 'CM50P'], // SKU duplikat
    'SPXID057631841955' => ['MB250F', 'MB250F'], // SKU duplikat
];

echo "=== ANALISIS DATA SALES ===\n\n";

$processedData = [];
$duplicateFound = [];

foreach ($salesData as $noResi => $skus) {
    echo "No Resi: $noResi\n";

    // Hitung kemunculan setiap SKU
    $skuCount = array_count_values($skus);

    $processedSkus = [];
    foreach ($skuCount as $sku => $count) {
        $qty = $count; // Qty sesuai dengan jumlah kemunculan SKU
        $processedSkus[] = [
            'sku' => $sku,
            'qty' => $qty
        ];

        if ($count > 1) {
            $duplicateFound[] = [
                'no_resi' => $noResi,
                'sku' => $sku,
                'qty' => $count
            ];
            echo "  - $sku (Qty: $qty) *** DUPLIKAT DITEMUKAN ***\n";
        } else {
            echo "  - $sku (Qty: $qty)\n";
        }
    }

    $processedData[$noResi] = $processedSkus;
    echo "\n";
}

echo "=== RINGKASAN DUPLIKAT ===\n";
if (empty($duplicateFound)) {
    echo "Tidak ada SKU duplikat ditemukan.\n";
} else {
    echo "SKU duplikat yang ditemukan:\n";
    foreach ($duplicateFound as $duplicate) {
        echo "- No Resi: {$duplicate['no_resi']}, SKU: {$duplicate['sku']}, Qty: {$duplicate['qty']}\n";
    }
}

echo "\n=== DATA UNTUK INSERT KE DATABASE ===\n";
echo "Format: INSERT INTO history_sales (no_resi, no_sku, qty, created_at, updated_at) VALUES\n";

$insertStatements = [];
$baseDate = '2025-05-26 07:00:00';

foreach ($processedData as $noResi => $skus) {
    foreach ($skus as $skuData) {
        $insertStatements[] = sprintf(
            "('%s', '%s', %d, '%s', '%s')",
            $noResi,
            $skuData['sku'],
            $skuData['qty'],
            $baseDate,
            $baseDate
        );
    }
}

echo implode(",\n", $insertStatements) . ";\n";

echo "\n=== STATISTIK ===\n";
echo "Total No Resi: " . count($salesData) . "\n";
echo "Total SKU entries: " . array_sum(array_map('count', array_values($salesData))) . "\n";
echo "Total SKU unik setelah proses: " . count($insertStatements) . "\n";
echo "Total duplikat ditemukan: " . count($duplicateFound) . "\n";
