<?php
require 'c:/xampp/htdocs/manrisv3/includes/functions.php';
$db = getDB();
$res = $db->query("SELECT konten_html FROM laporan_monev_draft WHERE tahun='2026' AND triwulan=1");
$row = $res->fetch_assoc();
$html = $row['konten_html'];
$pos = strpos($html, 'B. RENCANA TINDAK LANJUT');
if ($pos !== false) {
    echo "Found B. RENCANA TINDAK LANJUT in draft:\n";
    echo substr($html, $pos, 2500);
} else {
    echo "Not found in draft.\n";
}
