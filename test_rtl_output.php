<?php
require 'c:/xampp/htdocs/manrisv3/includes/functions.php';
$db = getDB();

$units = [
    'Sub Bagian Administrasi Umum',
    'Tim Kerja Program Layanan',
    'Tim Kerja Mutu, Penguatan SDM dan Kemitraan',
    'Tim Kerja Surveilans Penyakit, Faktor Risiko, dan KLB',
    'Instalasi',
    'Gratifikasi'
];

function testPecah(array $rawList): array {
    $items = [];
    foreach ($rawList as $raw) {
        $raw = trim((string)$raw);
        if ($raw === '' || $raw === '-') continue;
        $normalized = preg_replace('/(?<!\n|^)\s+(\d+\.\s+)/u', "\n$1", $raw);
        $normalized = preg_replace('/(?<!\n|^)\s+([a-zA-Z]\.\s+)/u', "\n$1", $normalized);
        $lines = preg_split('/\r\n|\r|\n/', $normalized);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line === '-') continue;
            $clean = preg_replace('/^(\d+[\.\)]\s*|[a-zA-Z][\.\)]\s*|[-•*]\s*)/u', '', $line);
            $clean = trim($clean);
            if ($clean !== '' && !in_array($clean, $items, true)) {
                $items[] = $clean;
            }
        }
    }
    return $items;
}

foreach ($units as $u) {
    echo "=== $u ===\n";
    $prefix = '';
    if (strpos($u, 'Administrasi Umum') !== false) $prefix = 'A';
    elseif (strpos($u, 'Layanan') !== false) $prefix = 'L';
    elseif (strpos($u, 'Mutu') !== false) $prefix = 'M';
    elseif (strpos($u, 'Surveilans') !== false) $prefix = 'S';
    elseif (strpos($u, 'Instalasi') !== false) $prefix = 'I';
    elseif (strpos($u, 'Gratifikasi') !== false) $prefix = 'G';

    $prefixDot = $prefix . '.%';
    $prefixDash = $prefix . '-%';
    $sql = "SELECT DISTINCT 
        CASE
            WHEN TRIM(m.rencana_tindak_lanjut) IS NOT NULL AND TRIM(m.rencana_tindak_lanjut) != '' AND TRIM(m.rencana_tindak_lanjut) != '-' THEN TRIM(m.rencana_tindak_lanjut)
            WHEN TRIM(r.rpti_uraian) IS NOT NULL AND TRIM(r.rpti_uraian) != '' AND TRIM(r.rpti_uraian) != '-' THEN TRIM(r.rpti_uraian)
            ELSE ''
        END AS val
    FROM kkpr_risiko r
    INNER JOIN kkpr_header h ON h.id = r.id_kkpr
    LEFT JOIN monev_triwulan m ON m.id_risiko = r.id AND m.triwulan = 1
    WHERE h.tahun = '2026' AND (r.kode_risiko LIKE ? OR r.kode_risiko LIKE ? OR r.kode_risiko = ?)
    ORDER BY val ASC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('sss', $prefixDot, $prefixDash, $prefix);
    $stmt->execute();
    $res = $stmt->get_result();
    $raw = [];
    while ($r = $res->fetch_assoc()) {
        if ($r['val'] !== '') $raw[] = $r['val'];
    }
    $items = testPecah($raw);
    echo "Total items: " . count($items) . "\n";
    foreach ($items as $idx => $it) {
        echo "  " . chr(97 + ($idx % 26)) . ". $it\n";
    }
}
