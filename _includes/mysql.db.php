<?php

$servername = "localhost";
$username = "sailsorgzit";
$password = "janJv!#sails@2021";//"deEv!#sails@2021";
$database = 'sailsorgzit';


    function dbConnect() {
        global $servername, $username, $password,$database, $dbConnect;
        $connectionObject = new mysqli($servername, $username, $password,$database);
        //mysqli_set_charset($connectionObject,'utf8');        
        if (!$connectionObject) {
            die('ERROR: Could not connect to database.');
        }
        return $connectionObject;
    }

    function dbClose($connectionObject) {
        mysqli_close($connectionObject);
    }

    function dbExecute($sqlQuery) {
        $co = dbConnect();
        $result = mysqli_query($co, $sqlQuery);
        if (!$result) {
            die("ERROR: ".mysqli_error($co));
        }
        dbClose($co);
        return $result;
    }
    
    function getNumRows($query){
        $co = dbConnect();    
        //echo '<BR>Qry<BR>'.$query;
	$result = mysqli_query($co, $query);
	if ($result)
	{
		// it return number of rows in the table.
		$row = mysqli_num_rows($result);
		if ($row)
		{
		  return $row;
		}else{
		  return 0;	
		}    
	}
    }

    function get2dArray($result) {
	global $i;
        $output = array();
        $i = 0;
        while ($row = mysqli_fetch_array($result)) {
            $output[$i] = array();
            foreach ($row as $key => $value) {
                $output[$i][$key] = $value;
            }
            $i++;
        }
        return $output;
    }
	

	

?>
