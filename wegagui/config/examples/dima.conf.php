<?php
// Наименование системы
$namesys="Дима балкон";

// Описание системы
$comment="Глубокая протока с досветкой";

// Объем бака в литрах
$LevelFull=20;

// Запас раствора вне бака в литрах в трубказ коробах в субстрате
$LevelAdd=13.5; 

// Аварийная защита от перелива в литрах (это сколько литров сольется назад в бак при внезапной остановке циркуляции)
$La=3;

// Плановое значение ЕС в мСм/см
$ECPlan=2.2;

// Соотношение веса солей к ЕС (вес солей в литре / ЕС)
$Slk=2.55/2.0;

// Концентрация концентратов 100:1, 200:1 и т.п.
$konc=200;

// Уведомления
$LevelAddEvent=5; // Сообщать при необходимости долить больше чем указанное число литров

// Калибровка термистора ЕС трехточечная

// Для калибровки необходимо расположить цифровой датчик температуры рядом с аналоговым и измерить три точки
// минимальную, среднюю, и максимально высокую для раствора
// px - показания АЦП, py - реальное значение температуры

// Точка 1 - минимальная температура
$px1=1710; 	$py1=19.8;

// Точка 2 - средняя (потимальная)
$px2=1942; 	$py2=25;

// Точка 3 - максимум
$px3=2072; 	$py3=28;


// Функция нелинейной экстраполяции значние по 3м точкам
$pa = -(-$px1*$py3 + $px1*$py2 - $px3*$py2 + $py3*$px2 + $py1*$px3 - $py1*$px2) /  (-pow($px1,2)*$px3 + pow($px1,2)*$px2 - $px1*pow($px2,2) + $px1*pow($px3,2) - pow($px3,2)*$px2 + $px3*pow($px2,2) );
$pb = ( $py3*pow($px2,2) - pow($px2,2)*$py1 + pow($px3,2)*$py1 + $py2*pow($px1,2) - $py3*pow($px1,2) - $py2 * pow($px3,2) ) /  ( (-$px3+$px2) * ($px2*$px3 - $px2*$px1 + pow($px1,2) - $px3*$px1 ) );
$pc = ( $py3*pow($px1,2)*$px2 - $py2*pow($px1,2)*$px3 - pow($px2,2)*$px1*$py3 + pow($px3,2)*$px1*$py2 + pow($px2,2)*$py1*$px3 - pow($px3,2)*$py1*$px2 ) /  ( (-$px3+$px2) * ($px2*$px3 - $px2*$px1 + pow($px1,2) - $px3*$px1 ) );
$ta = 1;
$tb = 1;
$tc = 1;


// Калибровка сенсора EC

$R1=508; // Резистор делителя R1 в омах

$Rx1=-38; // Внутреннее сопротивление подбираются таким образом, что-бы во всех калибровочных растворах значения Rp и  Rn сошлись.
$Rx2=-26; // Внутреннее сопротивление
$Dr=1023; // Предел АЦП

$k=0.02; // Коэффициент термокомпенсации, зависит от состава раствора и корректируется по графику так, чтобы раствор при разнызной температуре не менял свой ЕС


// Для калибровки наиболее удобно использовать аптечный раствор кальция хлорида шестиводного. Он жидкий в ампулах 100 г/л
// Ампулы бывают на 5 и 10 мл.
// Можно приготовить три калибровочных раствора 1, 2 и 5 ампул растворить в полулитре (если ампула 10мл то в литре) дистиллята с ЕС 0.01
// 
// Получим там где:
//
//    одна ампула ЕС = 1.114 мсм/см
//    две апулы ЕС = 2.132 мсм/см
//    три ампуы ЕС = 3.107 мсм/см
//    четыре ЕС = 4.057 мсм/см
//    пять ЕС = 4.988 мсм/см
//    шесть ЕС = 5.909 мсм/см


// Значения сопротивления и соотвествующее ему ЕС
// Первая точка
$ex1=609; // Omh1
$ec1=1.08; // ec1
// Вторая точка
$ex2=166; // Omh2
$ec2=4.89; // ec2

// Функция нелинейной апроксимации по трем точкам одна из которых нулевая
$eb=(-log($ec1/$ec2))/(log($ex2/$ex1));
$ea=pow($ex1,(-$eb))*$ec1;


// Калибровка бака

// Функция калибровки объема в литрах по показаниям дальнометра в сантиметрах
// Метод кривой по трем точкам, одна из которых является нулем.
// Так как дальномер отмеряет значения от датчика до поверхности воды, то первое что надо сделать
// это задать точку 0, где раствора в баке нет и сигнал отражается ото дна бака.

// Такая калибровка подходит только для емкостей с равномерной формой без сложой геометрии.

$distz=22.6; // cm  Уровень 0 - бак пустой
$distmax=3.5; // cm критическое расстояние до датчика (бак полный)
// Делаем два замера на 1/3 и на 2/3
//Замер 1 
$dst1= 6.15; // cm от дна
$lev1=  7; // литров
//Замер 2 
$dst2= 19.02; // cm от дна
$lev2=  17; // литров

$lb=(-log($lev1/$lev2))/(log($dst2/$dst1));
$la=pow($dst1,(-$lb))*$lev1;

//Калибровка Люксметра

$x1=431;   // raw АЦП
$y1=10000; //Lux

$x2=642;   // raw АЦП
$y2=65000; // Lux

$bpht=(-log($y1/$y2))/(log($x2/$x1));
$apht=pow($x1,(-$bpht))*$y1;

// Калибровка pH метра
$phr1=14576;
$ph1=4.01;

$phr2=12200;
$ph2=6.86;

$aph=(-$phr2*$ph1+$ph2*$phr1)/(-$phr2+$phr1);
$bph=(-$ph2+$ph1)/(-$phr2+$phr1);



// Параметры базы

include "../../db.php";
$my_db="wega91"; // Имя базы
$tb="sens"; // Имя таблицы с данными

// Соответсвие полей базы
$dAirTemp="AirTemp"; // температура воздуха в градусах
$dAirHum="hum";  // влажность воздуха в процентах
$RootTemp="WaterTemp";  // температура корней в градусах
$EcTempRaw="WaterTemp"; // температура в датчике ЕС в RAW АЦП
$LightRaw="0"; // датчик освещенности в RAW
//$dist="Dist"; // дистанция до поверхности раствора в см
$dist="( 20.046796*sqrt(273.15+WaterTemp) )/10000*(us/2)"; // дистанция до поверхности раствора в см

// Блок дорасчета уровня по ультрозвуковому датчику
// Первичные данне
$DistC0="us";
// Расчет расстояния от датчика до поверхности раствора в см
$DistC1="( 20.046796*sqrt(273.15+".$RootTemp.") )/10000*(".$DistC0."/2)";
// Расчет высоты водного столба от дна бака до поверхности раствора в см
$DistC2="if (".$DistC1."<".$distz.",".$distz."-".$DistC1.",null)";
// Филтрация выбросов (сглаживание)
$DistC3="kalman(".$DistC2.",8,-0.3,1,3)";
// Расчет уровня по раствора в литрах в зависимости от высоты водного столба и формы бака в л.
$DistC4=$la."*pow(".$DistC3.",".$lb.")";

//$dist="kalman(( 20.046796*sqrt(273.15+WaterTemp) )/10000*(us/2),12,0.5,3,1)"; // дистанция до поверхности раствора в см

//@Dist:=( 20.046796*sqrt(273.15+WaterTemp) )/10000*(us/2),
//@DistF:=kalman(@dist,12,0.5,3,1)


$A1="An"; // значение EC в RAW при отрицательной фазе цикла
$A2="Ap"; // значение EC в RAW при положительной фазе цикла
$phraw="0"; // значение pH в RAW

// Функция апроксимации объема раствора
//$f_lev=$la."*pow(kalman(@dist,12,-0.5,1,3),".$lb.")";
$f_lev=$DistC4;

//$f_lev="levmin(intpl(".$dist."-3.0))";

// Формула расчета остатка солей
$f_soil="(@lev+".$LevelAdd.")*@ECt*".$Slk;


// Формула расчета pH
//$f_ph=".$aph."+".$bph."*".$phraw.";
//$f_ph=$aph."+".$bph."*".$phraw;
$f_ph='null';

// Функция калибровки ЕС
$f_ec=$ea."*pow(@R2,".$eb.")";

// Функция калибровки аналогово сенсора для компенсации ЕС
$f_atemp=$RootTemp;



$csv="s.csv";
$gnups="s.gnuplot";
$img="s.png";
$gimg=$img;


$chat_id="70042565";

?>