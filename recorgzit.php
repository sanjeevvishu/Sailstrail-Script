<?php
require_once("./_includes/posts.entity.php");
require_once("./_includes/helper.php");
require_once("./_includes/db.wrapper.php");
// TIMING :
date_default_timezone_set("Europe/Istanbul");


function Recorgzit(){
	//Get the current date and time.
	$date = new DateTime();
	//Create a DateInterval object with P1D.
	$interval = new DateInterval('P1D');
	//Add a day onto the current date.	
  $date->add($interval);
	//Tommorrow's Date :


  $tommorrow = $date->format("Y-m-d")."T00:00:00.000Z";
	//Today's Date:
	$today= date("Y-m-d")."T00:00:00.000Z";


  /* FETCH DATA FROM SAILSTRAIL BASED ON TWO DATES */

	$curlf = curl_init();			
 	curl_setopt_array($curlf, array(
		CURLOPT_URL => "https://standalone-api.salestrail.io/export/calls/json?from=".$today."&to=".$tommorrow,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
		  'Authorization: Basic MGU4YjY0OTAtNDliZi00YTdhLWE5NzUtYTQxYTViMWM3YWMzOnFRcEp5VkZ2eExpdG93SDZqZ3pqS2hZTzh1c1gzd21oUnE5RTk0OU1hSThUTkZseXRVMmh2R0JvMXBHemNrWHk='
        ),
    ));
    $response = curl_exec($curlf);
    $data = json_decode($response,true);

    /* Create Last Execution Logs from Sailstrail API */
    generateLastExecutionLogs($response);

	  curl_close($curlf);
          $counter=0;
          $agentcalls=0;
          $iii = 0;
          foreach($data as $row => $column) { 
            $dater=$column["createdAt"];
            $modifiedDate= date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($dater)));
            
            //if($column["userPhone"]=="+905421353422"){
            //echo $column["number"]."==>"; echo $column["userPhone"]."==>".$modifiedDate."<br />";
            //}
            
            $counter++;            
            $phone = ltrim(trim($column["number"]),"0");            

            /* CHECKING AGENT IS EXIST IN SAILSTRAILS AGENT TABLE */

            $received = findAgent($phone);

            //Check is Agent in Agent Table of Sailstrail Tbl

            if($received)
            {

              foreach($received as $row2 => $receivedCl)
              { 
                
                $client_phone = ltrim(trim($column["userPhone"]), "0");
                /* CHECKING CLIENT IS EXIST IN SAILSTRAILS CLIENT TABLE */
                $foundedClient = findClient($client_phone);


                // Is Client in Client Table of Sailstrail Tbl
                if($foundedClient > 0){
                    //$foundedClient = findClient($client_phone);                
                    if($foundedClient)
                    {
                      foreach($foundedClient as $row3)
                      $activityowner = $row3["orgzit_id"];                    
                    }
                    else
                    {
                      $activityowner = "";
                    }

                    $agentname = $receivedCl["tid"];
                    if($column["sourceDetail"]=="SIM")
                    {
                    	$medium = "Dialer";
                    }else{
                    	$medium = "Whatsapp";
                    }
                    $duration = $column["duration"];
                    $realTime = date("Y-m-d H:i:s");
                    
                    if($column["inbound"]==true)
                    {
                        if($column["answered"]==true)
                        {
                          $call_type = "Attended Incoming Call";
                        }
                        elseif($column["answered"]==false)
                        {
                          $call_type = "Missed Call";
                        }
                    }
                    elseif($column["inbound"]==false)
                    {
                        if($column["answered"]==true)
                        {
                          $call_type = "Attended Outgoing Call";
                        }
                        elseif($column["answered"]==false)
                        {
                          $call_type = "Missed Call";
                        }
                    }
                    
                    ////////////////////////////////////////////////// Logging Calls in TMP Calls Table
                    
                    $call_id = $column["callId"];
                    $findCallID = findCall($call_id);
                    //echo '<BR>Is Client Call Foind in Call Table of Sailstrail => <pre>';print_r($findCallID);
                    if($findCallID)
                    {
                      //echo "Call Existed";
                    }
                    else
                    {
                      
                      $iii++;  
                      // Inserting Call To TMP Table
                      insertCall($call_id,$activityowner,$agentname);
                      // Creating JSON Format of Data                      
                      $postingData = array(
                        "dataform_id"=>"mhxcz389cr",
                        "dataform"=>"/api/1/dataform/mhxcz389cr/",
                        "fields"=>array(
                            "real_created_time"=>$realTime,
                            "call_type"=>$call_type,
                            "call_duration_sec"=>$duration,
                            "activity_medium"=>$medium,
                        ),
                        "dbvalues"=>array(
                          "activity_owner"=>$activityowner,
                          "agent_name"=>$agentname,
                        ),
                        "use_field_slug"=> true
                      );                      
                      $jsonData = json_encode($postingData);


                      // Start Posting To CRM
                      $curl = curl_init();
                      curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://cuengine.orgzit.com/api/1/record/',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS =>$jsonData,
                        CURLOPT_HTTPHEADER => array(
                          'Authorization: ApiKey salestrailapi:7893bcfde423478edd99e79d47e014bd81cfe224',
                          'Content-Type: application/json'
                        ),
                      ));

                      $response = curl_exec($curl);
                      
                      $responseArray = json_decode($response);

                      //echo "<BR>Regiter Call In ORGZIT CRM AS WELL Details Are =><BR><pre>";print_r($responseArray);echo "<BR><BR>";
                      
                      // Inserting Call To TMP Table
                      updateCall($call_id,$responseArray->fields->id);
                      // Creating JSON Format of Data
                      //$orgzitCallID = $responseArr->fields->id
                      //$call_id
                      
                      curl_close($curl);
                      //echo $response;
                    }
                    // Creating JSON Format of Data For Sending To Orgzit                
                }
              }
            }
          }
      //echo "<br /><br /> Total Calls : ".$counter;
      echo "<br /><br />Today's Calls To Agents Until last Update : ".$iii;

}

