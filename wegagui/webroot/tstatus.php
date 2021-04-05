<?php
if ($OutDate < $Max_OutDate){$OutDate_Status = "Норма";} else {$OutDate_Status = "Проблема";}
if ($AirHum < $Max_AirHum and $AirHum > $Min_AirHum ) {$AirHum_Status = "Норма";} else {$AirHum_Status = "Проблема";}
if ($AirTemp < $Max_AirTemp and $AirTemp > $Min_AirTemp ) {$AirTemp_Status = "Норма";} else {$AirTemp_Status = "Проблема";}
if ($RootTemp < $Max_RootTemp and $RootTemp > $Min_RootTemp ) {$RootTemp_Status = "Норма";} else {$RootTemp_Status = "Проблема";}
if ($WaterTemp < $Max_WaterTemp and $WaterTemp > $Min_WaterTemp ) {$WaterTemp_Status = "Норма";} else {$WaterTemp_Status = "Проблема";}
if ($ec < $Max_EC and $ec > $Min_EC ) {$ec_Status = "Норма";} else {$ec_Status = "Проблема";}
if ($ph < $Max_pH and $ph > $Min_pH ) {$pH_Status = "Норма";} else {$pH_Status = "Проблема";}
if ($lev < $Max_Level and $lev > $Min_Level ) {$lev_Status = "Норма";} else {$lev_Status = "Проблема";}
if ($lev < $Crit_Level ) {$lev_Status = "АВАРИЯ";}

echo "<table border=1><tr>";
echo "<th>Параметр<th>Значение<th>Статус</tr>";
echo "<td>Дата и время замера<td>".$dt. "<td>".$OutDate_Status."</tr>";
echo "<td><a href=plotdb.php?ns=".$ns."&cl=".$p_AirHum.">Влажность воздуха</a><td>".round($AirHum,1)."%<td>".$AirHum_Status."</tr>";
echo "<td><a href=plotdb.php?ns=".$ns."&cl=".$p_AirTemp.">Температура воздуха</a><td>".round($AirTemp,2)."°C <td>".$AirTemp_Status."</tr>";
echo "<td><a href=plotdb.php?ns=".$ns."&cl=".$p_RootTemp.">Температура в зоне корней</a><td>".round($RootTemp,2)."°C <td>".$RootTemp_Status."</tr>";
echo "<td><a href=plotdb.php?ns=".$ns."&cl=".$p_ECtemp.">Температура раствора в баке</a><td>".round($tempEC,2)."°C <td>".$WaterTemp_Status."</tr>";
if ($Lux){     echo "<td><a href=plotdb.php?ns=".$ns."&cl=".$p_Lux.">Освещенность</a><td>".round($Lux,3)." kLux<td></tr>";}
    echo "<td><a href=plotdb.php?ns=".$ns."&cl=".$p_EC.">Удельная электропроводность ЕС</a><td>".round($ec,3)." mS/cm <td>".$ec_Status."</tr>";
if($ph){      echo "<td><a href=plotdb.php?ns=".$ns."&cl=".$p_pH.">Водородный показатель pH</a><td>".round($ph,3)." <td>".$pH_Status."</tr>";}
echo "<td><a href=plotdb.php?ns=".$ns."&cl=".$p_lev.">Уровень раствора в баке </a><td>".round($lev,1)." литр. <td>".$lev_Status."</tr>";
echo "<td>Общий остаток раствора в системе <td>".round($L1,1)." литр. <td></tr>";

echo "</table>";

?>