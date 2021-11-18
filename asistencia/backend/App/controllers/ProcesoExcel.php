<?php

$ruta = dirname(__FILE__)."/../PHPExcel/Classes/PHPExcel/IOFactory.php";
include ($ruta);

class ProcesoExcel{

//static $_target = dirname(__FILE__).'/../PHPExcel/archivos/';

    public static function completeArray($nombre, $salida = false){

        if(empty($nombre)){
            header('Content-Type: application/json');
                echo json_encode(array('status'=>'nombre vacio'));
                exit;
        }

	$_target = dirname(__FILE__).'/../PHPExcel/archivos/';
        $inputFileName = $_target.$nombre;
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

        if($salida === false)
            return $sheetData;
        else{
        header('Content-Type: application/json');
            echo json_encode($sheetData);
            exit;
        }
    }

    public static function borrarArchivo($nombre){
	$_target = dirname(__FILE__).'/../PHPExcel/archivos/';
        if(unlink($_target.$nombre))
            $json['status'] = 'success';
        else
            $json['status'] = 'error';

        header('Content-Type: application/json');
        echo json_encode($json);
        return;
    }

    public static function getColumna($nombre, $columna, $msisdn = true, $patron = false){

        $sheetData = self::completeArray($nombre);
        $arraySalida = array();
        foreach ($sheetData as $key => $value) {

            $valor = $value[$columna];
            if($msisdn){
                if (strlen(trim($valor))>=10)
                    $valor = substr($valor,-10);
                if ( !(preg_match('/^\d{10}$/',trim($valor))))
                    continue;
            }else{
                if($patron){
                    if(!preg_match($patron, trim($valor)))
                        continue;
                }
            }
            array_push($arraySalida, array('dato'=>$valor));
        }

        header('Content-Type: application/json');
        echo json_encode($arraySalida);
        exit;
    }

    public static function getContenido($nombre='',$columnas=array()){
        $columnas = urldecode($columnas);
        $columnas = json_decode($columnas,true);

	$_target = dirname(__FILE__).'/../PHPExcel/archivos/';
        $objPHPExcel = PHPExcel_IOFactory::load($_target.$nombre);
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $contenidoExcel = array();
        for($i = 1; $i<=intval($sheet->getHighestRow()); $i++){
/*
            $contenidoAux = array();
            foreach ($columnas as $key => $value) {
                $contenidoAux[] = $sheet->getCell($key.$i)->getCalculatedValue();
            }
            $contenidoExcel[] = $contenidoAux;
*/
        }
/*
*/
        header('Content-Type: application/json');

        print_r(json_encode($contenidoExcel));
        return;
    }

    public static function getArrayColumnas($indice){
        $abecedario = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $cols = array();

        foreach ($abecedario as $key => $value) {
            if($key==$indice){
                $cols[] = $key;
                return $cols;
            }else{
                $cols[] = $value;
            }
        }

        return $cols;
    }

    public static function getFilas($nombre){
	$_target = dirname(__FILE__).'/../PHPExcel/archivos/';
        $objPHPExcel = PHPExcel_IOFactory::load($_target.$nombre);
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $colLetra = $sheet->getHighestColumn();
        $columnas = ProcesoExcel::getIndicesColumnas($colLetra);
        $sheetData = array();
        foreach ($columnas as $key => $value) {
            $sheetData[$value] = $sheet->getCell($value.'1')->getCalculatedValue();
        }

        header('Content-Type: application/json');
        print_r(json_encode($sheetData));
        return;
    }

    public static function getIndicesColumnas($letra){
        $abecedario = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $cols = array();

        foreach ($abecedario as $key => $value) {
            if($value==$letra){
                $cols[] = $value;
                return $cols;
            }else{
                $cols[] = $value;
            }
        }

        return $cols;
    }
}
?>
