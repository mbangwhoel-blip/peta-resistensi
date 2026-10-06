<?php
require 'c:/xampp/htdocs/manrisv3/includes/functions.php';
$db = getDB();

$sql = "SELECT r.kode_risiko, m.rencana_tindak_lanjut as m_rtl, r.rpti_uraian as r_rpti 
FROM kkpr_risiko r 
INNER JOIN kkpr_header h ON h.id=r.id_kkpr 
LEFT JOIN monev_triwulan m ON m.id_risiko=r.id AND m.triwulan=1 
WHERE h.tahun='2026' AND r.kode_risiko LIKE 'A.%' 
ORDER BY CAST(SUBSTRING_INDEX(r.kode_risiko, '.', -1) AS UNSIGNED) ASC";

$res = $db->query($sql);
while($r = $res->fetch_assoc()) {
    echo "[{$r['kode_risiko']}] monev_rtl: '{$r['m_rtl']}' | kkpr_rpti: '{$r['r_rpti']}'\n";
}
