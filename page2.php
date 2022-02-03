
<?php 
 set_time_limit(298);
include 'dbConn.php';
include 'common.php';
$delay = 240; //minutes
$meters = getMeters();
$meterId;
$meterUrl;
$count =1;
$successCount = 0;
date_default_timezone_set("Asia/Dhaka");

 $current_date = date("Y-m-dH:i" , strtotime("-$delay minutes")) ;

// echo $current_date;
// echo "====="."<br>";


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
                  
          //  http://192.168.5.130/enteliweb/cwpldata/getdata?starttime=2020-03-03 13:00:00&count=2&datapoint=//CWPL/11500.TL5.Present_Value

           // echo $apiUrl ;
           // echo "<br>"; 
             global $successCount;
             global $delay;
                  $arrayLength =0;


              $apiUrl = getBmsApiUrl($meterUrl , $current_date);
              echo $apiUrl."<br>";  
              
              
              $count = 0;

              $array = getDataFromApi ($apiUrl,$username, $password );
               
                 while  ($array == "Access denied"){

                       echo "No Data or access Denied ".$count."<br>";
                       $count++;
                       $array = getDataFromApi ($apiUrl,$username, $password );
                       
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
                  
                   
                //   echo "time : ".$Timestamp."Value : ".$value." Sequence : ".$sequence."<br>";

                   $query = "INSERT INTO TREND_LOG (METER_ID,DATE_TIME,SEQ_NO, VALUE, DELAY_MINUTES  ) 
                   VALUES ('$meterId' , TO_DATE ('$Timestamp' , 'YYYY-MM-DD HH24:MI:SS' ) , '$sequence' , '$value', '$delay')";

                   echo "query : ".$query."<br>";
                     // echo "------------------------------------------------------------"."<br>" ;
                    
                     try{
                       insertData($query);
                    }
                    catch(Exception $e){
                      "exception occured whie inserting query";
                    }
                     
               }


             echo "==========================================================================================================================="."<br>" ;
    
}



function getDataFromApi ($apiUrl,$username, $password ){
           echo "-------------------------------------- in getDataFromApi <br>";

     
     $client = curl_init($apiUrl);

       curl_setopt($client, CURLOPT_URL, $apiUrl);

            // //Specify the username and password using the CURLOPT_USERPWD option.
      curl_setopt($client, CURLOPT_USERPWD, $username . ":" . $password);          

      // //Tell cURL to return the output as a string instead
      // //of dumping it to the browser.
            curl_setopt($client,CURLOPT_RETURNTRANSFER,true);
          //  curl_setopt($client, CURLOPT_TIMEOUT, 15);
        

        $response = curl_exec($client);
               
      // curl_close($client);
        echo "response :".$response ." length : ".strlen($response)."<br>"; 
        

         curl_close($client);
         
        
     if($response == null){

     echo "response is null <br>"; 
     $response  = "NUll";
       return  $response ;
     }
     else if ($response == "Access denied"){
      echo "response is Access Denied <br>";
       $response  = "Access denied";
       return  $response ;
    
      }
     else if ($response == ""){
      echo "response is No data <br>";
       $response  = "No data";
       return  $response ;
    

      }




       $XmlObject =  simplexml_load_string($response);
        

      // // Converting SimpleXmlElement Object to Array 
      $json = json_encode($XmlObject);          
      $array = json_decode($json,TRUE);

     
        echo "-------------------------------------- return form getDataFromApi <br>";     
      return $array ;


}


oci_close($dbConn);

?>


