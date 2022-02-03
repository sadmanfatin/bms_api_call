<?php 
	$dbhost = "163.47.147.74";    //host 163.47.147.74-(realip)   //192.168.5.248 (local ip)
	$dbport= "1521";          //port default is 1521 
	$servicename = "CWPL";        //name of database  
	$dbuser = "PRODUCTION";      //db user with all <priviliges></priviliges>
	$dbpassword = "123";    // password of user
	$dbConnString = 
			"(DESCRIPTION =
				(ADDRESS_LIST =
					(ADDRESS = (PROTOCOL = TCP)(HOST = ". $dbhost .")(PORT = ". $dbport ."))
				)
	  			(CONNECT_DATA =
	  				(SERVICE_NAME = ". $servicename .")
	  			)
	  		)";    // connection string for this we must create TNS entry for Oracle


     $dbConn = oci_connect($dbuser,$dbpassword,$dbConnString);
	if(!$dbConn ){
	$err = oci_error();
	trigger_error('Could not establish a connection: ' . $err['message'], E_USER_ERROR);
	}
	// else{
	// 	echo 'Successfully Connected to Oracle Database';
	// }
    

function insertData( $query){
     global $dbConn;
    $stmt = oci_parse( $dbConn, $query);
        

                    try{
                        oci_execute($stmt);
                    }
                    catch(Exception $e){
                      "exception occured whie executing query";
                    }
   
  // oci_commit($dbConn);
}


function  getData( $query){
	global $dbConn;
     $stmt = oci_parse( $dbConn, $query);
      oci_execute($stmt);
    return  $stmt;

}

//oci_close($dbConn);

?>


