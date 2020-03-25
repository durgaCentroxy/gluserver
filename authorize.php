<?php

include_once 'OIDC.php';

$oidc = new OIDC();

$auth_EP = $oidc->getAuthorizationEndpoint();
$client_id = $oidc->getClientId();
$callback = $oidc->getCallbackURL();

$url = "$auth_EP?response_type=code&client_id=$client_id&redirect_uri=$callback&scope=openid+profile+email+address+clientinfo+mobile_phone+phone+user_name&state=fds879fds9898e2h5ukjcuo";
header("Location: ".$url);
exit;

?>
