
<?php 

 set_time_limit(598);
include 'dbConn.php';
include 'common.php';
$delay = 360; //minutes
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
   $dateLastValue = $meter['DATE_TIME_LAST_VALUE'];
    $lastSeqNo =$meter['LAST_SEQ_NO'];
 // echo $meterId." ==== ".$meterUrl."  ===== ".$dateLastValue."    ========   ".$lastSeqNo."    ========   "."<br>";
   //echo "====="."<br>".$count."<br>"."====="."<br>";
         
   if($dateLastValue == null ){
         
     $dateLastValue = $current_date;

     }

      fetchDataAndInsert($meterId ,$meterUrl,$dateLastValue ,$lastSeqNo) ;

       $count++;

 }

function fetchDataAndInsert(  $meterId , $meterUrl, $date, $lastSeqNo ){
            $username="mnj_ho_it";
            $password ="123456";

           //  echo $apiUrl ;
           // echo "<br>"; 
             global $successCount;
             global $delay;
                  $arrayLength =0;
             
            //24 reading for each meter will be attempted
             $readingCount = 24;

              $apiUrl = getBmsApiUrl($meterUrl , $date , $readingCount );
           //  echo $apiUrl."<br>";  
              
              $array = getDataFromApi ($apiUrl,$username, $password  );
             

            $arrayLength = sizeof($array);
             //echo  " arrayLength :".$arrayLength."<br>";
               
            if(isset ($array['Row'] )  ){
                    
                  // print_r($array);
                  //  echo "<br>";

                  $successCount++;
                  //echo "Success Count :".$successCount."<br>";
                      
                  $row = $array['Row'] ; 

                  // echo "--------------  row";
                  // echo "<br>";
                  // print_r($row);

                 if(isset ($row['Timestamp'])){
                    // if row has no array, it itself an associative array of Timestamp, value ,seq
                    //echo "in (isset (row['Timestamp'] )";
                    //echo "<br>";

                    $Timestamp= $row['Timestamp'] ;
                    $value= $row['Value'];
                    $sequence = $row['Seq'];
                    $status = "";
                 //  $delayMinutes =  date_diff(  strtotime($date) ,$Timestamp)    ;
                      $delayMinutes=$delay;

                      if(  $sequence>$lastSeqNo) {
                        insertApiDataIntoTrendLogTable( $meterId ,$Timestamp,$value,$sequence, $delayMinutes , $status);
                      }

                    
                    
                    }

                 else if (!isset($row['Timestamp'] )){
                        
                      //  echo " in ( !!! isset (row['Timestamp'] )"."<br>";
                     //  $row contains array of multiple Timestamp, value , sequence

                        foreach ($row as $rowData) {
                           
                          
                            $Timestamp= $rowData['Timestamp'] ;
                            $value= $rowData['Value'];
                            $sequence = $rowData['Seq'];
                             //  $delayMinutes = date_diff( strtotime($date),$Timestamp);
                            $delayMinutes=$delay;
                            $status = "";
                         insertApiDataIntoTrendLogTable($meterId ,$Timestamp,$value,$sequence, $delayMinutes, $status);

                          //   echo "time : ".$Timestamp."Value : ".$value." Sequence : ".$sequence."<br>";

                       }

           
                 }
             // echo "==========================================================================================================================="."<br>" ;         

         }


   }

oci_close($dbConn);
?>