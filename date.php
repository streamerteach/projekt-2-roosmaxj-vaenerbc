<?php
print("Today is ".date("l ").date("j.").date("m.").date("Y")." - ");

//Dagens datum
$today = time();
//dagen till händelsen 
$event = mktime(0,0,0,2,24,2026);
//Räknar hur många dagar det är tills valda datumet 
$countdown = round(($event - $today)/86400);
//Printar ut det!
echo "$countdown days until next lego building event.";
?>
