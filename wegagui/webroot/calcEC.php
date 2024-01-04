<?php
  if ( $_GET['act'] == 'set' )
  {
    $ns=$_GET['ns'];
    include "sqvar.php";
    $EC_Rx1_comment = $_GET['EC_Rx1_comment'];
    $EC_Rx2_comment = $_GET['EC_Rx2_comment'];
    setdbval($ns,"EC_Rx1",$_GET['set_rx1'],$EC_Rx1_comment);
    setdbval($ns,"EC_Rx2",$_GET['set_rx2'],$EC_Rx2_comment);

    setdbval($ns,"EC_val_p1",$_GET['EC_val_p1_set'],$_GET['EC_val_p1_comment']);
    setdbval($ns,"EC_R2_p1",$_GET['EC_R2_p1_set'],$_GET['EC_R2_p1_comment']);

    setdbval($ns,"EC_val_p2",$_GET['EC_val_p2_set'],$_GET['EC_val_p2_comment']);
    setdbval($ns,"EC_R2_p2",$_GET['EC_R2_p2_set'],$_GET['EC_R2_p2_comment']);

    header("Location: ".$_GET['return_url']);
  }

include "menu.php";

if ( $_GET['ns'] )
{
  include "sqvar.php";
  echo "<h1>".$namesys;
  echo "</h1>";
  echo $comment;
  echo "<br>";
  echo "<h2>База ".$my_db."</h2>";
  echo "<br>";
  //include "datetime.php";
  // Подключаемся к базе
  $link = mysqli_connect("$dbhost", "$login", "$password", "$my_db");
  if (!$link) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
  }

  //$strSQL ="select * from $tb where dt  >  '".$wsdt."' and  dt  <  '".$wpdt."' order by dt limit $limit";

  $eckorr=floatval(dbval("EC_val_korr",$ns));
  $kt=floatval(dbval("EC_kT",$ns));

  $dateval=dbval("EC_date1",$ns);
  echo "<br>";
  echo "Дата точки 1 = ".$dateval;
  echo "<br>";
  echo "EC точки 1 = ".round(valdate($p_EC,$dateval,$ns) -> value, 3);
  echo "<br>";
  echo "R2 в точке 1 = ".round(valdate($P_R2,$dateval,$ns) -> value, 3);
  echo "<br>";
  echo "Температура в точке 1 = ".round(valdate($p_ECtemp,$dateval,$ns) -> value, 3);
  echo "<br>";
  echo "EC калибровочного раствора при 25 градусах = ".round(dbcomment("EC_date1",$ns),3);
  echo "<br>";
  $point1 = $dateval;
  $temp1 = round(valdate($p_ECtemp,$dateval,$ns) -> value, 3);
  $curEC1 = round(valdate($p_EC,$dateval,$ns) -> value, 3);
  $target1 = round(dbcomment("EC_date1",$ns),3);
  $point1A1 = round(valdate($P_A1,$dateval,$ns) -> value, 3);
  $point1A2 = round(valdate($P_A2,$dateval,$ns) -> value, 3);




  $dateval=dbval("EC_date3",$ns);
  echo "<br>";
  echo "Дата точки 3 = ".$dateval;
  echo "<br>";
  echo "EC точки 3 = ".round(valdate($p_EC,$dateval,$ns) -> value, 3);
  echo "<br>";
  echo "R2 в точке 3 = ".round(valdate($P_R2,$dateval,$ns) -> value, 3);
  echo "<br>";
  echo "Температура в точке 3 = ".round(valdate($p_ECtemp,$dateval,$ns) -> value, 3);
  echo "<br>";
  echo "EC калибровочного раствора при 25 градусах = ".round(dbcomment("EC_date3",$ns),3);
  echo "<br>";
  $point3 = $dateval;
  $temp3 = round(valdate($p_ECtemp,$dateval,$ns) -> value, 3);
  $curEC3 = round(valdate($p_EC,$dateval,$ns) -> value, 3);
  $target3 = round(dbcomment("EC_date3",$ns),3);
  $point3A1 = round(valdate($P_A1,$dateval,$ns) -> value, 3);
  $point3A2 = round(valdate($P_A2,$dateval,$ns) -> value, 3);



  
  $strSQL ="select dt,".$p_EC." from $tb where (dt  <  '".$point1."') and ((ABS(UNIX_TIMESTAMP(dt) - UNIX_TIMESTAMP('".$point1."')) > 600) or (ABS(".$p_EC." - ".$curEC1.") > 0.3)) order by dt desc limit 1";
  
  $rs=mysqli_query($link, $strSQL);
  while( $row = mysqli_fetch_assoc( $rs)){
    $res = $row; // Inside while loop
    $startPoint =  $res["dt"]; 
  }
  echo "<br>";
  echo "Начало калибровки: ".$startPoint;
  echo "<br>";

  $strSQL ="select dt,".$p_EC." from $tb where (dt  >  '".$point3."') and ((ABS(UNIX_TIMESTAMP(dt) - UNIX_TIMESTAMP('".$point3."')) > 600) or (ABS(".$p_EC." - ".$curEC3.") > 0.3)) order by dt asc limit 1";
  
  $rs=mysqli_query($link, $strSQL);
  while( $row = mysqli_fetch_assoc( $rs)){
    $res = $row; // Inside while loop
    $stopPoint =  $res["dt"]; 
  }
  echo "Окончание калибровки: ".$stopPoint;
  echo "<br>";






  echo "<br>";

  // График АЦП
  if ($P_A1 != 'null' and $P_A2 != 'null') {
    $pref="ecrawcalc";    
    $xsize=1000;
    $ysize=400;
    

    $gimg=$gimg.$pref;
    $img=$img.$pref;
    
    $strSQL ="select
    dt,
    ".$P_A1.",
    ".$P_A2."

    from sens 
    where dt  >  '".$startPoint."'
     and  dt  <  '".$stopPoint."'
    order by dt";
    include "sqltocsv.php";
    
    $name="АЦП электродов измерения электропроводности";
    $dimens="RAW";
    $nplot1="A1.RAW(-)";
	  $nplot2="A2.RAW(+)";
    
    gplotgen($xsize,$ysize,$gimg,$startPoint,$stopPoint,$csv,$handler,$text,$gnups,$img,$name,$nplot1,$nplot2,$nplot3,$nplot4,$nplot5,$dimens);

    echo "<br>Текущие значения АЦП при положительной и отрицательной фазе:";
    echo "<br>";
    echo "A1.RAW(-)=".$A1;
    echo "<br>";
    echo "A2.RAW(+)=".$A2;

}

  // подбор оптимальных Rx1 и Rx2
  $strSQL ="select * from $tb where dt  >  '".$startPoint."' and  dt  <  '".$stopPoint."' order by dt limit $limit";
  $rs=mysqli_query($link, $strSQL);

  while( $row = mysqli_fetch_assoc( $rs)){
    $dataset[] = $row; // Inside while loop
  }

  $r1 = floatval(dbval("EC_R1",$ns));
  $dr = floatval(dbval("Dr",$ns));

  $delta = 0;
  $precisions = array(100, 10, 1);
  $min_delta = 100000000000;
  $best_rx1 = 0;
  $best_rx2 = 0;

  foreach($precisions as $precision)
  {
    $rx1_min = -20*$precision+$best_rx1;
    $rx1_max = 20*$precision+$best_rx1;
    $rx2_min = -20*$precision+$best_rx2;
    $rx2_max = 20*$precision+$best_rx2;
    for($rx1 = $rx1_min; $rx1 <= $rx1_max; $rx1=$rx1+$precision)
    {
      for($rx2 = $rx2_min; $rx2 <= $rx2_max; $rx2=$rx2+$precision)
      {
        $delta = 0;
        foreach($dataset as $row)
        {
          if($row['Ap'] && $row['An'])
          {
            $a1 = $row['Ap'];
            $a2 = $row['An'];
            $R2p = -1 * (-1 * $a1 * $r1 - $a1 * $rx2 + $rx2 * $dr) / (-1 * $a1 + $dr);
            $R2n = (-1 * $a2 * $r1 - $a2 * $rx1 + $r1 * $dr + $rx1 * $dr) / $a2;
            $delta += abs($R2p - $R2n);
          }
        }
        if($delta < $min_delta)
        {
          $min_delta = $delta;
          $best_rx1 = $rx1;
          $best_rx2 = $rx2;
        }
      }
    }
  }

  // Посчитаем контрольные точки для построения графика EC через термокомпенсацию по известным параметрам


  $A1 = $point1A1;
  $A2 = $point1A2;
  $Rx1 = $best_rx1;
  $Rx2 = $best_rx2;
  
  $R2p=(((-$A2*$R1-$A2*$Rx1+$R1*$Dr+$Rx1*$Dr)/$A2));
  $R2n=(-(-$A1*$R1-$A1*$Rx2+$Rx2*$Dr)/(-$A1+$Dr));
  $R2=($R2p+$R2n)/2;
  echo "<p>Point1 R2: ".$R2."</p>";
  $ex1 = $R2;
  $ec1=($target1-$eckorr)*(1-$kt*(-$temp1+25));
  echo "<p>Point1 EC: ".$ec1."</p>";
  
  $A1 = $point3A1;
  $A2 = $point3A2;
  $R2p=(((-$A2*$R1-$A2*$Rx1+$R1*$Dr+$Rx1*$Dr)/$A2));
  $R2n=(-(-$A1*$R1-$A1*$Rx2+$Rx2*$Dr)/(-$A1+$Dr));
  $R2=($R2p+$R2n)/2;
  echo "<p>Point3 R2: ".$R2."</p>";
  $ex3 = $R2;
  $ec3=($target3-$eckorr)*(1-$kt*(-$temp3+25));
  echo "<p>Point3 EC: ".$ec3."</p>"; 

  $eb = (-log($ec1/$ec3))/(log($ex3/$ex1));
  $ea = pow($ex1,(-$eb))*$ec1;

  $ec = $ea*pow($ex1,$eb);
  $ECt =$ec/(1+$kt*($temp1-25));
  $ECt = $ECt + $eckorr;
  echo "<p>Point1 TK EC: ".$ECt."</p>";


  $ec = $ea*pow($ex3,$eb);
  $ECt =$ec/(1+$kt*($temp3-25));
  $ECt = $ECt + $eckorr;
  echo "<p>Point3 TK EC: ".$ECt."</p>";


  //echo $_SERVER['HTTP_REFERER'];
  //$return_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $return_url = $_SERVER['HTTP_REFERER'];
  $return_url = str_replace("calcEC.php", "CalibrateEC.php", $return_url);
  echo "<form><form action='' method='get'>";
  echo "<input type='hidden' name='ns' value='".$ns."'>";
  echo "<input type='hidden' name='return_url' value='".$return_url."'>";
  echo "<input type='hidden' name='set_rx1' value='".$best_rx1."'>";
  echo "<input type='hidden' name='set_rx2' value='".$best_rx2."'>";
  echo "<input type='hidden' name='EC_Rx1_comment' value='".dbcomment("EC_Rx1",$ns)."'>";
  echo "<input type='hidden' name='EC_Rx2_comment' value='".dbcomment("EC_Rx2",$ns)."'>";


  echo "<input type='hidden' name='EC_val_p1_set' value='".$ec1."'>";
  echo "<input type='hidden' name='EC_val_p1_comment' value='".dbcomment("EC_val_p1",$ns)."'>";

  echo "<input type='hidden' name='EC_R2_p1_set' value='".$ex1."'>";
  echo "<input type='hidden' name='EC_R2_p1_comment' value='".dbcomment("EC_R2_p1",$ns)."'>";


  echo "<input type='hidden' name='EC_val_p2_set' value='".$ec3."'>";
  echo "<input type='hidden' name='EC_val_p2_comment' value='".dbcomment("EC_val_p2",$ns)."'>";

  echo "<input type='hidden' name='EC_R2_p2_set' value='".$ex3."'>";
  echo "<input type='hidden' name='EC_R2_p2_comment' value='".dbcomment("EC_R2_p2",$ns)."'>";



  echo "<input type='hidden' name='wsdt' value='".$_GET['wsdt']."'>";
  echo "<input type='hidden' name='wpdt' value='".$_GET['wpdt']."'>";
  echo "<p>EC_Rx1: ".$best_rx1;
  echo "<p>EC_Rx2: ".$best_rx2;
  echo "<p><input type='submit' value='set' name='act'>";
}
else
{
  echo "Не выбрана система";
}
?>
