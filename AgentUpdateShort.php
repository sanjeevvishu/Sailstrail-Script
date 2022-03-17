<?php
require_once("./_includes/posts.entity.php");
require_once("./_includes/helper.php");
require_once("./_includes/db.wrapper.php");

// Check total numbers of records and iterations after devided by 20  
// Exmple total 100 agents from orgzit / rows one call =20 then iterations is = 5 

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
                                "dataform": "tpx6a1p7l9",
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
	    //echo '<BR><BR><pre>';print_r($JsonToArrayObj);exit;  100/20 = 5
            return round(($JsonToArrayObj->meta->total_count)/20);
        }
}


// Methid is used to get Agents data and Store in Sailstrail database
function AgentUpdateShort($limit, $offset){
	$curl = curl_init();
	$offset = (int) ($limit*$offset);
	$req = 'https://cuengine.orgzit.com/api/1/record/filter/?limit='.$limit.'&offset='.$offset;
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
		        "dataform": "tpx6a1p7l9",
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
        curl_close($curl);
        if(!empty($data['objects'])){
            foreach($data['objects'] as $row){
                $first_phone= $row['fields']['first_phone'];
                //echo '<BR>DATA<BR><pre>';print_r($row);
                $id= $row['fields']['id'];
                $tid= $row['id'];
                $first_phone= $row['fields']['first_phone'];
                $second_phone= $row['fields']['second_phone'];
                $third_phone= $row['fields']['third_phone'];
                $fullname= str_replace("'", "", $row['fields']['full_name']);
                insertmember($id,$tid,$fullname,$first_phone,$second_phone,$third_phone);
	    }
        }       
}

// Check total itrations 
//suppose there are 100 records then we devide it by 20 (fetch rows in single call => 100/20 = 5 so loop run 5 times)
$totalNumbersLoop = getNumberOfItrations();


for($i=0; $i < $totalNumbersLoop; $i++){
	$response = AgentUpdateShort(20, $i);
}

//AgentUpdateShort();
?>

