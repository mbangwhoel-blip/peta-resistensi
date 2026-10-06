<?php
require 'c:/xampp/htdocs/manrisv3/includes/functions.php';
$db = getDB();

$q = $db->query("SELECT count(*) as c FROM monev_triwulan m JOIN kkpr_risiko r ON r.id=m.id_risiko JOIN kkpr_header h ON h.id=r.id_kkpr WHERE h.tahun='2026'");
echo "Total monev_triwulan rows 2026: " . $q->fetch_assoc()['c'] . "\n";

$q2 = $db->query("SELECT count(*) as c FROM monev_triwulan m JOIN kkpr_risiko r ON r.id=m.id_risiko JOIN kkpr_header h ON h.id=r.id_kkpr WHERE h.tahun='2026' AND (m.rencana_tindak_lanjut IS NOT NULL AND TRIM(m.rencana_tindak_lanjut) != '' AND m.rencana_tindak_lanjut != '-')");
echo "Filled rencana_tindak_lanjut in monev_triwulan: " . $q2->fetch_assoc()['c'] . "\n";

$q3 = $db->query("SELECT m.triwulan, r.kode_risiko, m.rencana_tindak_lanjut, r.rpti_uraian 
FROM monev_triwulan m 
JOIN kkpr_risiko r ON r.id=m.id_risiko 
JOIN kkpr_header h ON h.id=r.id_kkpr 
WHERE h.tahun='2026' 
LIMIT 20");
while ($row = $q3->fetch_assoc()) {
    echo "TW{$row['triwulan']} - {$row['kode_risiko']}:\n";
    echo "   monev.rencana_tindak_lanjut: '" . $row['rencana_tindak_lanjut'] . "'\n";
    echo "   kkpr.rpti_uraian: '" . $row['rpti_uraian'] . "'\n";
}
