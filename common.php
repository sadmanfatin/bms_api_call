<?php 

 include_once 'dbConn.php';

 $baseUrl="http://163.47.147.74:81/enteliweb/Wsreportdata/Cwpldata/call/getdata?";

 $username="mnj_ho_it";
 $password ="123456";


function getMeters(){

$query = "SELECT ml.METER_ID , ml.METER_URL  ,
 to_char((select max(ta.DATE_TIME )  from  trend_log ta where ta.meter_id = ml.meter_id  ), 'yyyy-mm-ddHH24:MI' ) DATE_TIME_LAST_VALUE,
 (select nvl  (max(ta.SEQ_NO ),0 ) from  trend_log ta where ta.meter_id = ml.meter_id  ) LAST_SEQ_NO
FROM METER_LIST ml   WHERE ml.STATUS = 1" ;

$result = getData($query);

 oci_fetch_all($result,$meters , null , null , OCI_FETCHSTATEMENT_BY_ROW );

 // $meterIdAray = $meterAray['METER_ID'];
 // $meterUrlArray = $meterAray['METER_URL'];

  // return $meterUrlArray;
   
   return $meters;


}


function getBmsApiUrl($meterUrl, $date , $readingCount  ){
     // this function get a meter url in parameter and adds with the base url and adds other aggregates with the base url and returns the base url

    global $baseUrl;
    $url;
     

    
    $startTime = "starttime=".$date;
   

    $count = "&count=".$readingCount  ;

    $additionalUrl = $startTime.$count."&datapoint=".$meterUrl ;
     


    $url = $baseUrl.$additionalUrl  ;

   //echo  " url   = ".$url;
   //echo "<br>";


    return $url;
}


function getTrendLogTableInsertQuery($meterId,$Timestamp,$value,$sequence, $delay, $status ){

  $query = "INSERT INTO TREND_LOG (METER_ID,DATE_TIME,SEQ_NO, VALUE, DELAY_MINUTES,STATUS  ) 
                   VALUES ('$meterId' , TO_DATE ('$Timestamp' , 'YYYY-MM-DD HH24:MI:SS' ) , '$sequence' , '$value', '$delay', '$status')";


 return $query;
}



function getDataFromApi ($apiUrl,$username, $password ){
         //  echo "-------------------------------------- in getDataFromApi <br>";

     
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
       // echo "response :".$response ." length : ".strlen($response)."<br>"; 
        

         curl_close($client);
         
        // print_r($response);
        // echo "<br>";
        // printf($response);
        //     echo "<br>";    
        // print($response);
        //  echo "<br>"; 
     if($response == null){

     // echo "response is null <br>"; 
     // $response  = "NUll";
       return  $response ;
     }
     else if ($response == "Access denied"){
      // echo "response is Access Denied <br>";
      //  $response  = "Access denied";
       return  $response ;
    
      }
     else if ($response == ""){
      // echo "response is No data <br>";
      //  $response  = "No data";
       return  $response ;
    

      }

    //     echo "response :".gettype($response )."<br>"; 
 
    //  $responeLength =   strlen($response );
    //             echo strlen($responeLength  );
    // echo"   <br>";    
    //parsing the xml string to  SimpleXmlElement Object with  SimpleXml extension
    //$response1 = new SimpleXMLElement($response);  //does the same as   simplexml_load_string($response);


       $XmlObject =  simplexml_load_string($response);
        

      // // Converting SimpleXmlElement Object to Array 
      $json = json_encode($XmlObject);          
      $array = json_decode($json,TRUE);

      // echo " array type :".gettype($array) ;
      //          echo " array length :".sizeof($array);
      // echo"   <br>";  
       

      //echo  "row : ".$row;
      // echo "<br>";

    //  $value= $row['Value'];
    // echo  "value : ".$value;
   // echo "<br>";
    //    echo "-------------------------------------- return form getDataFromApi <br>";     
      return $array ;


}




function  insertApiDataIntoTrendLogTable($meterId,$Timestamp,$value,$sequence,$delay, $status){
     $trendLogTableInsertQuery = getTrendLogTableInsertQuery($meterId,$Timestamp,$value,$sequence,$delay, $status );

                     //  echo "query : ".$query."<br>";
                         // echo "------------------------------------------------------------"."<br>" ;
                        
                        try{
                           insertData($trendLogTableInsertQuery);
                        }
                        catch(Exception $e){
                          "exception occured whie inserting query";
                        }
                   
                   

}






?>