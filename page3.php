
<?php 
set_time_limit(250000);
include 'dbConn.php';
include 'common.php';
$delay = 0; //minutes
$meters = getMeters();
$meterId;
$meterUrl;
$count =1;
$successCount = 0;
date_default_timezone_set("Asia/Dhaka");

$current_date = date("Y-m-dH:i" , strtotime("-$delay minutes")) ;

// echo $current_date;
// echo "====="."<br>";


$d=mktime(0, 0, 0, 8, 9, 2020);
$start_date = date("Y-m-dH:i", $d  ) ;

$new_date  = $start_date;


// $d=mktime(0, 0, 0, 5, 11, 2020);
// $end_date = date("Y-m-dH:i", $d  ) ; ;



//echo $new_date;
//echo "<br>";


//while (  $new_date !=  $end_date ){


// echo "----------------  in while  -----------";
// echo "<br>";
// echo  "new date ".$new_date;
// echo "<br>";





 foreach ($meters as $meter) {     //$meters as $meter


   $meterId =  $meter['METER_ID'];
   $meterUrl = $meter['METER_URL'];
    //echo $meterId."".$meterUrl."<br>";

//       echo "====="."<br>".$count."<br>"."====="."<br>";



       fetchDataAndInsert($meterId ,$meterUrl,$new_date  ) ;

//       $count++;

  }
//fetchDataAndInsert(1 ,"//CWPL/10000.TL1.Present_Value",$new_date  ) ;

 //  $new_date  =  date("Y-m-dH:i" ,  strtotime($new_date.'+1 hours' )  ) ;



// }





function fetchDataAndInsert(  $meterId , $meterUrl, $current_date ){
  $username="mnj_ho_it";
  $password ="123456";

          //  http://192.168.5.130/enteliweb/cwpldata/getdata?starttime=2020-03-03 13:00:00&count=2&datapoint=//CWPL/11500.TL5.Present_Value

           // echo $apiUrl ;
           // echo "<br>"; 
  global $successCount;
  global $delay;
  $arrayLength =0;


  $apiUrl = getBmsApiUrl($meterUrl , $current_date, 3200);
    echo $apiUrl."<br>";  


  $array = getDataFromApi ($apiUrl,$username, $password );



            //  $arrayLength = sizeof($array);


            // echo  " arrayLength :".$arrayLength."<br>";


                  // if(   isset ($array['Row'] ) ){
                  //    echo  "^+^+^+^+^+^^+^+^+^+^+^^+^ ";
                  //      echo " array[Row] == Array" ."<hr>" ;
                  // }

            //  $arrayLength == 1  means xml report fetched at least one row



  if(   isset ($array['Row'] ) ){



    // echo "----------------------------  row is is set true -----------------------------";  
    // echo "<br>";                 
    // echo "----------------------------   array -----------------------------";  
    // print_r($array);
    // echo "<br>";


    $row = $array['Row'] ;

  //    echo "<br>";
  //    echo "size of row ".sizeof($row);
  // echo "<br>";

    for ($i=0 ;$i<sizeof($row) ; $i++) {
                         # code...
      $arr = $row[$i] ;
                         // echo "<br>";

      $Timestamp= $arr['Timestamp'] ;
      $value= $arr['Value'];
      $sequence = $arr['Seq'];

                   echo "<br>";
                  echo "time : ".$Timestamp."Value : ".$value." Sequence : ".$sequence."<br>";
                   echo "<br>";

      $query = "INSERT INTO TREND_LOG_TEST (METER_ID,DATE_TIME,SEQ_NO, VALUE, DELAY_MINUTES  ) 
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


}

echo "==========================================================================================================================="."<br>" ;

}



// function getDataFromApi ($apiUrl,$username, $password ){
//          //  echo "-------------------------------------- in getDataFromApi <br>";


//  $client = curl_init($apiUrl);

//  curl_setopt($client, CURLOPT_URL, $apiUrl);

//             // //Specify the username and password using the CURLOPT_USERPWD option.
//  curl_setopt($client, CURLOPT_USERPWD, $username . ":" . $password);  				 

// 			// //Tell cURL to return the output as a string instead
// 			// //of dumping it to the browser.
//  curl_setopt($client,CURLOPT_RETURNTRANSFER,true);
//           //  curl_setopt($client, CURLOPT_TIMEOUT, 15);


//  $response = curl_exec($client);

//       // curl_close($client);
//       //  echo "response :".$response ." length : ".strlen($response)."<br>"; 


//  curl_close($client);

//         // print_r($response);
//         // echo "<br>";
//         // printf($response);
//         //     echo "<br>";    
//         // print($response);
//         //  echo "<br>"; 
//  if($response == null){

//     // echo "response is null <br>"; 
//    $response  = "NUll";
//    return  $response ;
//  }
//  else if ($response == "Access denied"){
//   //    echo "response is Access Denied <br>";
//    $response  = "Access denied";
//    return  $response ;

//  }
//  else if ($response == ""){
//  //     echo "response is No data <br>";
//    $response  = "No data";
//    return  $response ;


//  }


  //     echo "response :".gettype($response )."<br>"; 



			 //  $responeLength =   strlen($response );
    //             echo strlen($responeLength  );
				// echo"   <br>";    
			//parsing the xml string to  SimpleXmlElement Object with  SimpleXml extension
			//$response1 = new SimpleXMLElement($response);  //does the same as   simplexml_load_string($response);



//  $XmlObject =  simplexml_load_string($response);


// 			// // Converting SimpleXmlElement Object to Array 
//  $json = json_encode($XmlObject);          
//  $array = json_decode($json,TRUE);

// 			// echo " array type :".gettype($array) ;
//    //          echo " array length :".sizeof($array);
// 			// echo"   <br>";  


//            // print_r($array);           
//            //echo"<br>";


// 		  //   

//              //echo  "row : ".$row;
//             // echo "<br>";

// 		//	$value= $row['Value'];
//              // echo  "value : ".$value;
//              // echo "<br>";
//       //  echo "-------------------------------------- return form getDataFromApi <br>";     
//  return $array ;


// }


oci_close($dbConn);

?>
