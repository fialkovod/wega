<?php

include "menu.php";
$ns=$_GET['ns'];
$p_name=$_GET['parameter'];


include "../config/".$ns.".conf.php";



if ( $my_db ){

$p_value=dbval($p_name,$ns);
$p_comment=dbcomment($p_name,$ns);

// Добавляем или обновляем
if ( $_GET['act'] == 'set' ) {
    setdbval($ns,$_GET['parameter'],$_GET['value'],$_GET['comment']);
    header("Location: ".$_GET['return_url']);
 }


echo "      <br><br>
           <form>
              <form action='' method='get'>
                   <input type='hidden' name='ns' value=$ns>
                   Имя параметра: <input type='text' readonly name='parameter' value=".$p_name."><br>
                   Параметр: <input id='parVal' type='text' name='value' value='".$p_value."'><br>
                   Справочное значение ЕС: <input type='text' name='comment' value='".$p_comment."'><br>
              <input type='hidden' name='return_url' value='".str_replace("setpoint.php", "CalibrateEC.php", $_SERVER['HTTP_REFERER'])."'>
              <input type='submit' value='set' name='act'>
              <input type='button' onclick='history.back();' value='Back'/>
           </form>
";



include "sqfunc.php";

$P_A1=dbval("A1",$ns);
$A1=sensval($P_A1,$ns);

$P_A2=dbval("A2",$ns);
$A2=sensval($P_A2,$ns);


echo "<br>";

// График АЦП
if ($P_A1 != 'null' and $P_A2 != 'null') {
    $pref="ecrawpoint";    
    $xsize=1000;
    $ysize=400;
    

    $gimg=$gimg.$pref;
    $img=$img.$pref;
    
    $strSQL ="select
    dt,
    ".$P_A1.",
    ".$P_A2."

    from sens 
    where dt  >  '".$wsdt."'
     and  dt  <  '".$wpdt."'
    order by dt";
    include "sqltocsv.php";
    
    //$name="АЦП электродов измерения электропроводности";
    $name="ADC";
    $dimens="RAW";
    $nplot1="A1.RAW(-)";
	$nplot2="A2.RAW(+)";
    
   
    gplotgen($xsize,$ysize,$gimg,$wsdt,$wpdt,$csv,$handler,$text,$gnups,$img,$name,$nplot1,$nplot2,$nplot3,$nplot4,$nplot5,$dimens,$mouse=true);


}

}



?>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        let target = document.querySelector("td .mousebox");
        if (target) {
            target.style.visibility = "hidden";
            target.style.display = "none";
        }
        let tar = document.querySelector("table+.mbleft");
        if (tar) tar.style.marginLeft = 0;
            
        if (gnuplot?.mouse_version) {
            if (document.getElementById("gnuplot_canvas")) {
                document.getElementById("gnuplot_canvas").onclick = function(){
                    if (gnuplot.plotx < 0 || gnuplot.ploty < 0) return;
                    if (gnuplot.mousex > gnuplot.plot_xmax || gnuplot.mousey < gnuplot.plot_ytop) return;
                    let newd = (new Date(gnuplot.datafmt(x)).toISOString()).substr(0,19).replace("T"," ");
                    document.getElementById("parVal").setAttribute("value", newd);
                    gnuplot.zoom_in_progress = false;
                    gnuplot.rezoom(event);


                    ctx.strokeStyle="black";
                    ctx.strokeRect(gnuplot.mousex, 15, 1, 365);
                    click = " "+newd;

                    let len = ctx.measureText("sans", 9, click);
                    if (gnuplot.mousex > gnuplot.plot_term_xmax-(len) ){
                        len += ctx.measureText("sans", 9, " ");
                        ctx.drawText("sans", 9, gnuplot.mousex-len, gnuplot.mousey, click);
                    } else {
                        ctx.drawText("sans", 9, gnuplot.mousex, gnuplot.mousey, click);
                    }    


                }



            }

        }

    });

</script>