
<?php 
 set_time_limit(298);
include 'dbConn.php';
include 'common.php';
$delay = 10; //minutes
$meters = getMeters();
$meterId;
$meterUrl;
$count =1;   // meter loop counter
$successCount = 0;
date_default_timezone_set("Asia/Dhaka");

 $current_date = date("Y-m-dH:i" , strtotime("-$delay minutes")) ;


foreach ($meters as $meter) {     //$meters as $meter

    
   $meterId =  $meter['METER_ID'];
   $meterUrl = $meter['METER_URL'];
   //echo $meterId."".$meterUrl."<br>";

      echo "====="."<br>".$count."<br>"."====="."<br>";
         
        

       fetchDataAndInsert($meterId ,$meterUrl,$current_date   ) ;

      $count++;

 }

function fetchDataAndInsert(  $meterId , $meterUrl, $current_date ){
            $username="mnj_ho_it";
            $password ="123456";

           // echo $apiUrl ;
           // echo "<br>"; 
             global $successCount;
             global $delay;
                  $arrayLength =0;

                   $readingCount = 1;
              $apiUrl = getBmsApiUrl($meterUrl , $current_date, $readingCount);
              echo $apiUrl."<br>";  
              

              $array = getDataFromApi ($apiUrl,$username, $password );
               

               if ($array == "Access denied"){
                 
                   echo "No Data or access Denied <br>";
                   
                     $seq =  date('H', strtotime($current_date ));
                     $seq = $seq*100;
                     $seq =$seq  +date('i', strtotime($current_date ));
                   
                       $status=$array ;
                      insertApiDataIntoTrendLogTable($meterId,$Timestamp,$value,$sequence, $delay , $status);
                  
                   return;

               } 

              $arrayLength = sizeof($array);


             echo  " arrayLength :".$arrayLength."<br>";

               

               
            if(   isset ($array['Row'] )   ){
                    
                    print_r($array);
                   echo "<br>";

                    $successCount++;
                    echo "Success Count :".$successCount."<br>";

                   
                  $row = $array['Row'] ;

                  $Timestamp= $row['Timestamp'] ;
                  $value= $row['Value'];
                  $sequence = $row['Seq'];
                  $status= "";
                  
                 insertApiDataIntoTrendLogTable($meterId,$Timestamp,$value,$sequence, $delay , $status);
                     
               }


             echo "==========================================================================================================================="."<br>" ;
    
}



oci_close($dbConn);

?>