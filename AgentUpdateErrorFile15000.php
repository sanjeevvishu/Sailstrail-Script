<?php
require_once("./_includes/posts.entity.php");
require_once("./_includes/helper.php");
require_once("./_includes/db.wrapper.php");

exit;


$b=0;
$tablename = "agents";
//truncatetable($tablename);

for($i=0; $i<15000;$i++)
{   
    if ($i % 20 == 0) 
    {
                $curl = curl_init();
                $offset = (int) (20*$offset);
                $req = 'https://cuengine.orgzit.com/api/1/record/filter/?limit=20&offset='.$offset;
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
                    //'Authorization: ApiKey salestrailapi:7893bcfde423478edd99e79d47e014bd81cfe224',
                    //'Content-Type: application/json'
                ),
                ));
            
                //$response = curl_exec($curl);
                //$data = json_decode($response,true);
                //curl_close($curl);
                //print_r($data['objects']);
	
                /*if(!empty($data['objects'])){
                    foreach($data['objects'] as $row){
                        //echo $row['created'];
                        //echo " ";
                        $b++;
                        $id= $row['fields']['id'];
                        $tid= $row['id'];
                        $first_phone= $row['fields']['first_phone'];
                        $second_phone= $row['fields']['second_phone'];
                        $third_phone= $row['fields']['third_phone'];
                        $fullname= $row['fields']['full_name'];
                
                        insertmember($id,$tid,$fullname,$first_phone,$second_phone,$third_phone);
                        
                    }
                }*/                
    }

}
echo $b;






?>
