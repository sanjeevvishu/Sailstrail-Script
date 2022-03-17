<?php
require_once("./_includes/posts.entity.php");
require_once("./_includes/helper.php");
require_once("./_includes/db.wrapper.php");
exit;

$b=0;
//$curl = curl_init();

function getNumberOfItrations(){
	$curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://cuengine.orgzit.com/api/1/record/filter/?limit=1",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => '{
                                "dataform": "oy96uzwlav",
                                "filters": [
					{
                                            "field": "last_modified_date",
                                            "op": "today",
                                            "values": []
                                      }
                                ],
                                "getfieldvalues": false,
                                "use_field_slug": true
                            }',
		CURLOPT_HTTPHEADER => array(
		    "Authorization: ApiKey salestrailapi:7893bcfde423478edd99e79d47e014bd81cfe224",
		    "cache-control: no-cache",
		    "content-type: application/json",
		    "postman-token: 7b619f1f-5ab6-48f1-fe76-80973c04d400"
		),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $JsonToArrayObj = json_decode($response);            
	    //echo '<BR><BR><pre>';print_r($JsonToArrayObj);exit;
            return round(($JsonToArrayObj->meta->total_count)/20);

        }
}

function createNewClient($rowLimits, $offSet){
	$b=0;
        $curl = curl_init();
	$offset = (int) ($rowLimits*$offSet);
        $req = 'https://cuengine.orgzit.com/api/1/record/filter/?limit='.$rowLimits.'&offset='.$offset;
        curl_setopt_array($curl, array(
                CURLOPT_URL => $req,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS =>'{
		        "dataform": "oy96uzwlav",
		        "filters": null,
		        "getfieldvalues": false,
		        "use_field_slug": true
                }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ApiKey salestrailapi:7893bcfde423478edd99e79d47e014bd81cfe224',
                    'Content-Type: application/json'
                ),
        ));
        $response = curl_exec($curl);
        $data = json_decode($response,true);
        // curl_close($curl);        
        //echo '<BR>ORGZIT DATA<BR><pre>';print_r($data['objects']);
        //exit;
	
        if(!empty($data['objects'])){
            foreach($data['objects'] as $row){
		$orgzit_id=  $row['user']['id'];
		$phone= $row['user']['phone_number'];
		$fullname= $row['user']['fullname'];
		insertclient($orgzit_id,$fullname,$phone);
		$b++;
            }
        }
	
        curl_close($curl);
        $NA= "Number Of Added Clients : ".$b."<br />";
        return true;
	
}            
/* COUNT RECORDS */
//echo 'Total New Clients => '.$totalNumbersLoop = getNumberOfItrations();exit;
for($i=0; $i < $totalNumbersLoop; $i++){
   $response = createNewClient(20, $i);
}
?>
