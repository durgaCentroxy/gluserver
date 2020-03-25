<?php
function gluu_register_user($user_details) {

    include(__DIR__ . '/OIDC.php');

    $settings = new OIDC();

    //Regurl SCIM API
    $tokenurl = $settings->getTokenEndpoint();
    $clientid = $settings->getClientId();
    $clientsecret = $settings->getClientSecret();
    $regurl = $settings->getSCIMRegisterUser();

    //To generate Access token
    $username = $clientid;
    $passwordinformation = $clientsecret;
    $granttype = "client_credentials";
    $URL = $tokenurl;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["grant_type" => "client_credentials"]));
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$passwordinformation");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch);   //get status code
    $json = json_decode($result);

    $response = [];
    
    /*******************
    here's a wild idea, let's check for any errors eh? no idea what other errors may come back but have received the JSON below so added handling for that at least...

    {"error":"invalid_client","error_description":"Client authentication failed (e.g. unknown client, no client authentication included, or unsupported authentication method). The authorization server MAY return an HTTP 401 (Unauthorized) status code to indicate which HTTP authentication schemes are supported. If the client attempted to authenticate via the Authorization request header field, the authorization server MUST respond with an HTTP 401 (Unauthorized) status code, and include the WWW-Authenticate response header field matching the authentication scheme used by the client."}

    ********************/
    if ($json->access_token) {

        //Token generation
        $token = $json->access_token;
        $authorization = "Authorization: Bearer " . $token;
        $scheme = "[urn:ietf:params:scim:schemas:core:2.0:User]";
        $username = $user_details['firstname'] . " " . $user_details['lastname'];
        $active = "true";
        $formatted = $user_details['role'];
        $familyname = $user_details['lastname'];
        $givenname = $user_details['firstname'];
        $middlename = "";
        $email = $user_details['email'];
        $passwordinfo = $user_details['password'];
        $displayname = $user_details['firstname'];
        $gender = $user_details['gender'];
        $arr = array('schemas' => $scheme, 'userName' => $email, 'active' => $active, 'name' => array("formatted" => $formatted, "givenName" => $givenname, "familyName" => $familyname, "middleName" => $middlename), "emails&" => array("value" => $email), "password" => $passwordinfo, "displayName" => $displayname);
        $Userstring = json_encode($arr);
        
        $userinfo = substr($Userstring, 0, 11) . str_replace('"', '', substr($Userstring, 12, 1)) . "\"" . substr($Userstring, 13, 42) . "\"" . "]" . "," . "\"" . substr($Userstring, 59);
        $userdeatils = str_replace("&", ":[{", $userinfo);
        $emailvalue = strstr($userdeatils, value);
        $emailvalue_str = strstr($emailvalue, '}', true);
        $emailvaluestring_manuplication = "\"" . $emailvalue_str . "}]";
        $passworstring = strstr($Userstring, 'password');
        $stringuser = strstr($userdeatils, emails, true);
        $strposemail = strpos($userdeatils, "emails");
        $emailconcate = substr($userdeatils, $strposemail, 9);
        $str_to_insert = "\"";
        $emailconcatenew = substr_replace($emailconcate, $str_to_insert, 6, 0);
        $emailconcatenew . $emailvaluestring_manuplication;
        
        $userstringalloutput = $stringuser . $emailconcatenew . $emailvaluestring_manuplication . "," . "\"" . $passworstring;
        
        $URLInfo = $regurl;
        $ch = curl_init($URLInfo);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $userstringalloutput);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/scim+json', $authorization));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
    	//Json to array convert
    	$result = json_decode($result);

        if(isset($result->id)) {
            $response['status'] = 200;
            $response['gluuResponse'] = $result;
        } else if(isset($result->status) && $result->status == 409) {
            $response['status'] = 300;
            $response['gluuResponse'] = $result;
        }
    }    
	return $response;
}


function gluu_verify_user($user_id) {
    include(__DIR__ . '/OIDC.php');

    $settings = new OIDC();

    //Regurl SCIM API
    $tokenurl = $settings->getTokenEndpoint();
    $clientid = $settings->getClientId();
    $clientsecret = $settings->getClientSecret();
    $regurl = $settings->getSCIMRegisterUser();

    //To generate Access token
    $username = $clientid;
    $passwordinformation = $clientsecret;
    $granttype = "client_credentials";
    $URL = $tokenurl;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["grant_type" => "client_credentials"]));
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$passwordinformation");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch);   //get status code
    $json = json_decode($result);

    //Token generation
    $token = $json->access_token;
    $authorization = "Authorization: Bearer " . $token;
    $schema = ["urn:ietf:params:scim:schemas:core:2.0:User"];
    $user_id = $user_id;
    $active = "true";
    $arr = array('schemas' => $schema, 'id' => $user_id, 'active' => $active);
    
    $URLInfo = $regurl."/".$user_id;
    $data_json = json_encode($arr);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URLInfo);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/scim+json', $authorization));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response  = curl_exec($ch);
    curl_close($ch);

	$verify_result = json_decode($response);
	
}
?>