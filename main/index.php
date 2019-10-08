<?php 

include('connect.php');
require_once "vendor/autoload.php";
function checkImage($excel){
    $c=1;
        $coord = array();
        foreach ($excel->getActiveSheet()->getDrawingCollection() as $drawing) {
            if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );
                $imageContents = ob_get_contents();
                ob_end_clean();
                $extension = 'png';
            }else {
                
                $zipReader = fopen($drawing->getPath(),'r');
                $imageContents = '';
        
                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader,1024);
                }
                fclose($zipReader);
                $extension = $drawing->getExtension();
                $string = $drawing->getCoordinates();
                $coordinate = PHPExcel_Cell::coordinateFromString($string);
                print_r($coordinate) ;
                array_push($coord, $coordinate);
                echo ' hello else';
                echo '<br>';
            }
            $myFileName = '00_Image_'.++$c.'.'.$extension;
            file_put_contents($myFileName,$imageContents);
        }
        print_r($coord);
        // foreach($coordinate as $key => $value) {
        //     echo($key);
        // }
}


//load excel file
if(!empty($_FILES)){
    $excel = PHPExcel_IOFactory::load('Book1.xlsx');

    $excel = PHPExcel_IOFactory::load($_FILES['excel']['tmp_name']);
    // set active sheet to first sheet
    $excel->setActiveSheetIndex(0);
    checkImage($excel);
    echo '<table>';
    // first row of data set
    $i=2;

    // loop to end of data
    while($excel->getActiveSheet()->getCell('A'.$i)->getValue()!=""){
        $name = $excel->getActiveSheet()->getCell('A'.$i)->getValue();
        $email = $excel->getActiveSheet()->getCell('B'.$i)->getValue();
        echo "
            <tr>
                <td>".$i."</td>
                <td>".$name."</td>
                <td>".$email."</td>
            </tr>
        ";
        // $sql = " INSERT INTO data VALUES('$name', '$email') " ;
        // mysqli_query($conn, $sql);
        
    
        $i++;
    }
    echo '<table>';
}else{
    echo "
        <form method='post' enctype='multipart/form-data' action='index.php'>
            <input type='file' name='excel'>
            <input type='submit' value='fetch'>
        </form>
    ";
}


?>