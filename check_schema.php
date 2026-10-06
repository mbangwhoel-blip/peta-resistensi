<?php
require 'c:/xampp/htdocs/manrisv3/includes/functions.php';
$db = getDB();
echo "--- KKPR_RISIKO ---\n";
$res = $db->query("DESCRIBE kkpr_risiko");
while($r = $res->fetch_assoc()) echo $r['Field'] . "\n";

echo "\n--- MONEV_TRIWULAN ---\n";
$res2 = $db->query("DESCRIBE monev_triwulan");
while($r = $res2->fetch_assoc()) echo $r['Field'] . "\n";

echo "\n--- RISIKO ---\n";
$res3 = $db->query("DESCRIBE risiko");
while($r = $res3->fetch_assoc()) echo $r['Field'] . "\n";
