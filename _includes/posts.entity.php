<?php 
    
require_once("db.wrapper.php");

function insertmember($id,$tid,$fullname,$first_phone,$second_phone,$third_phone) {
	$totalRows = getNumRows("SELECT * FROM agents where tid='".$tid."' and first_phone='".$phone."'");
	if($totalRows == 0){
		dbExecute("INSERT INTO agents (id,tid,full_name,first_phone,second_phone,third_phone) VALUES ('$id','$tid','$fullname','$first_phone','$second_phone','$third_phone');");
		return true;
	}	
}

function BackupAgents($id,$tid,$email,$fullname,$first_phone,$second_phone,$third_phone,$company_phone,$source1,$source2,$source3,$status_1)
{
	dbExecute("INSERT INTO backup_agents (id,tid,email,fullname,first_phone,second_phone,third_phone,company_phone,source1,source2,source3,status_1) 
	VALUES ('$id','$tid','$email','$fullname','$first_phone','$second_phone','$third_phone','$company_phone','$source1','$source2','$source3','$status_1');");
	return true;	
	
}

function insertCall($call_id,$activityowner,$agentname) {
	dbExecute("INSERT INTO calls (call_id,client_id,agent_id,logged) VALUES ('$call_id','$activityowner','$agentname','1');");
	return true;	
}

function updateCall($CallID,$OrgzitCallID){
	dbExecute("UPDATE calls set orgzit_call_id='".$OrgzitCallID."' where call_id='".$CallID."'");
	return true;
}



function findAgent($phone){
	$result = dbExecute("SELECT * FROM agents where first_phone like '%$phone%' OR second_phone like '%$phone%' OR third_phone like '%$phone%';");
	return get2dArray($result);
}

function findUser($mobileNumbers){
	$result = getNumRows("SELECT * FROM clients where phone like '%".$mobileNumbers."%'");
	return $result;
}

	
function findClient($client_phone){
	$new_str = str_replace(' ', '', $client_phone);
	$result = dbExecute("SELECT * FROM clients where phone like '%$new_str';");
	return get2dArray($result);
}

function findCall($call_id){
	$result = dbExecute("SELECT * FROM calls where call_id like '%$call_id';");
	return get2dArray($result);
}
	
	
function insertclient($orgzit_id,$fullname,$phone) {
	$totalRows = getNumRows("SELECT * FROM clients where orgzit_id='".$orgzit_id."' and phone='".$phone."'");
	if($totalRows == 0){
		dbExecute("INSERT INTO clients (orgzit_id,full_name,phone) VALUES ('$orgzit_id','$fullname','$phone');");
		return true;	
	}
}
	

function truncatetable($tablename) {
		dbExecute("	TRUNCATE TABLE $tablename");
		return true;	
	}


function fill_everything1($email_adress_cl, $password_cl , $repassword_cl)
		{
	global $email_adress_cl , $password_cl , $repassword_cl;
	
	if($email_adress_cl!="" and $password_cl!="" and $repassword_cl!="")
	{
		return true;
	}
	else
	{
		header("Location: register.php?error=er1");
		die();
	}
}

function password_match($password_cl,$repassword_cl)
{
	global $password_cl,$repassword_cl ;
	if($password_cl==$repassword_cl)
	{
		return true;
	}
	else
	{
		header("Location: register.php?error=er2");
		die();	
	}
}

function emaildup($email_adress_cl){
	$result = dbExecute("SELECT * FROM users WHERE email = '$email_adress_cl';");
	return get2dArray($result);
}
/// Validating Registration 
function validatePostData($email_adress_cl,$password_cl,$repassword_cl) 
{		
	$is_fill_1 = fill_everything1($email_adress_cl, $password_cl , $repassword_cl);
	// Password Match		
	$is_password_ok = password_match($password_cl,$repassword_cl);
	
	$is_email_dup = emaildup($email_adress_cl);
	
	foreach ($is_email_dup as $row => $columns) { 
	
		if(isset($columns["id"]))
		{
			header("Location: register.php?error=er3");
			die();
		}
		else
		{
			return true;
		}
	}


        if($is_fill_1)
        {
             if($is_password_ok)
            {
				insertMember($email_adress_cl,$password_cl);
				header("Location: index.php?reg=ok");
				die();	
            }		
        }
}


function login($email_adress_cl,$password_cl){
	$result = dbExecute("SELECT * FROM users WHERE email = '$email_adress_cl';");
	return get2dArray($result);
}

	


?>
