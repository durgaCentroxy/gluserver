<a><?php echo( "<button onclick= \"location.href='logout.php'\">Logout</button>");?></a>

<?php

include_once 'OIDC.php';
//require_once '../../inc/hcloud.inc';

//$db = get_db();
$oidc = new OIDC();


$code = $_GET['code'];
$state = $_GET['state'];
echo "<br>-------------<br>";
echo $code;
echo "<br>-----------------------<br>";


$token = getToken($oidc, $code);


setcookie('id_token',$token->id_token,0,'/');
setcookie('access_token',$token->access_token,0,'/');



$user = getUserInfo($oidc->getUserInfoEndpoint(), $token->access_token);
var_dump($user);
echo "<br>-----------------------<br>";

//exit;


$email_info = $user->email;
$uuid = $user->inum;
$first_name = $user->given_name;
$last_name = $user->family_name;
$mobile = $user->phone_number;
$phone = $user->phone_mobile_number;

echo "<br>mail: ".$email_info;
echo "<br>uuid: ".$uuid;
echo "<br>first_name: ".$first_name;
echo "<br>last_name: ".$last_name;
echo "<br>mobile: ".$mobile;
echo "<br>phone: ".$phone;

exit();

$cookie_value = "gluu_op";

if(isset($email_info)){
    setcookie('user_id',$email_info,0,'/');

    $exists_result = mysqli_query($db, "SELECT UserUuid FROM users WHERE Email='$email_info'");
    
    if (!$exists_result || !mysqli_num_rows($exists_result)) {
        $writeresult = mysqli_query($db, "INSERT INTO users (UserUuid, CloudCreated, FirstName, LastName, Email, DateCreated) VALUES ('$uuid', UNIX_TIMESTAMP(), '$first_name', '$last_name', '$email_info', UNIX_TIMESTAMP())");
        mysqli_close($db);
    }
    else {
        //Update User data (firstname, lastname)
        $update_result = mysqli_query($db, "UPDATE users SET  FirstName='$first_name', LastName='$last_name', Email='$email_info' WHERE UserUuid='$uuid'");
        mysqli_close($db);
        
        $user_data = mysqli_fetch_assoc($exists_result);
        $uuid = $user_data['UserUuid'];
    }

    //Create heartcloud login session
    start_hcloud_session($uuid, $email_info, $timezone);

    //To check login type for gluu
    setcookie('login_type',$cookie_value,0,'/');

    header('Location: /');
    exit();
}
else {
    //Perform Logout
    $url = include('./GetLogoutUri.php');
    
    if(isset($url)) {
        setcookie('login_type','',time() - 3600,'/');
        header('Location:'.$url);
    }
}


function getToken($oidc, $code)
{
    $token_endpoint = $oidc->getTokenEndpoint();
    $clientid = $oidc->getClientId();
    $clientsecret = $oidc->getClientSecret();
    $callback_url = $oidc->getCallbackURL();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_endpoint);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["grant_type" => "authorization_code", "code" => $code, "redirect_uri" => $callback_url]));
    curl_setopt($ch, CURLOPT_USERPWD, "$clientid:$clientsecret");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch);   //get status code
    $json = json_decode($result);
    echo "<br>------------------------<br>";
    echo "getToken Value";
    var_dump($json); 
    return $json;
}


function getUserInfo($userinfo_endpoint, $access_token)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $userinfo_endpoint);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        "Authorization: Bearer " . $access_token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch);   //get status code
    $json = json_decode($result);

    return $json;
}

?>