<?php

/**
 * Script untuk mengisi gap pada tanggal 26 Mei 2025
 * Gap: antara 08:57:23 dan 09:00:17
 * Data akan diisi mulai jam 08:10 sampai 08:50
 */

// Data yang diberikan user untuk mengisi gap
$gapData = [
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

echo "=== MENGISI GAP TANGGAL 26 MEI 2025 ===\n";
echo "Gap: antara 08:57:23 dan 09:00:17\n";
echo "Waktu pengisian: 08:10:00 - 08:50:00\n\n";

// Mulai dari jam 08:10:00
$startTime = strtotime('2025-05-26 08:10:00');
$currentTime = $startTime;
$idCounter = 10714; // ID setelah data terakhir sebelum gap

$insertStatements = [];
$processedData = [];

foreach ($gapData as $noResi => $skus) {
    // Hitung kemunculan setiap SKU untuk menentukan qty
    $skuCount = array_count_values($skus);

    foreach ($skuCount as $sku => $qty) {
        $timestamp = date('Y-m-d H:i:s', $currentTime);

        $insertStatements[] = sprintf(
            "(%d, '%s', '%s', %d, '%s', '%s')",
            $idCounter,
            $noResi,
            $sku,
            $qty,
            $timestamp,
            $timestamp
        );

        $processedData[] = [
            'id' => $idCounter,
            'no_resi' => $noResi,
            'sku' => $sku,
            'qty' => $qty,
            'timestamp' => $timestamp
        ];

        echo sprintf(
            "ID: %d | No Resi: %s | SKU: %s | Qty: %d | Time: %s\n",
            $idCounter,
            $noResi,
            $sku,
            $qty,
            $timestamp
        );

        // Increment waktu (interval 1-2 menit secara random)
        $increment = rand(60, 120); // 1-2 menit
        $currentTime += $increment;
        $idCounter++;

        // Pastikan tidak melebihi 08:50:00
        if ($currentTime > strtotime('2025-05-26 08:50:00')) {
            break 2; // Break dari kedua loop
        }
    }
}

echo "\n=== SQL INSERT STATEMENT ===\n";
echo "INSERT INTO history_sales (id, no_resi, no_sku, qty, created_at, updated_at) VALUES\n";
echo implode(",\n", $insertStatements) . ";\n";

echo "\n=== STATISTIK PENGISIAN GAP ===\n";
echo "Total entries yang ditambahkan: " . count($processedData) . "\n";
echo "Waktu mulai: " . date('Y-m-d H:i:s', $startTime) . "\n";
echo "Waktu selesai: " . date('Y-m-d H:i:s', $currentTime - $increment) . "\n";
echo "Range ID: " . $processedData[0]['id'] . " - " . end($processedData)['id'] . "\n";

echo "\n=== VERIFIKASI DUPLIKAT SKU ===\n";
$duplicates = [];
foreach ($gapData as $noResi => $skus) {
    $skuCount = array_count_values($skus);
    foreach ($skuCount as $sku => $count) {
        if ($count > 1) {
            $duplicates[] = "No Resi: $noResi, SKU: $sku, Qty: $count";
        }
    }
}

if (empty($duplicates)) {
    echo "Tidak ada duplikat SKU yang terdeteksi.\n";
} else {
    echo "Duplikat SKU yang diproses:\n";
    foreach ($duplicates as $duplicate) {
        echo "- $duplicate\n";
    }
}

echo "\n=== FORMAT UNTUK FILE EXCEL ===\n";
foreach ($processedData as $data) {
    echo sprintf(
        "%d\t%s\t%s\t%d\t%s\t%s\n",
        $data['id'],
        $data['no_resi'],
        $data['sku'],
        $data['qty'],
        $data['timestamp'],
        $data['timestamp']
    );
}
