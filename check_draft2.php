<?php
require 'c:/xampp/htdocs/manrisv3/includes/functions.php';
$db = getDB();
$res = $db->query("SELECT konten_html FROM laporan_monev_draft WHERE tahun='2026' AND triwulan=1");
$row = $res->fetch_assoc();
$html = $row['konten_html'];
$pos = strpos($html, 'B. RENCANA TINDAK LANJUT');
echo substr($html, $pos, 6000);
