<?php

include_once 'OIDC.php';

$oidc = new OIDC();

$end_session_EP = $oidc->getEndSessionEndpoint();
$post_logout_redirect_url = $oidc->getPostLogoutRedirectURL();

$url = "$end_session_EP?post_logout_redirect_uri=$post_logout_redirect_url";
echo "url: ".$url;

header('Location: https://iam4.centroxy.com/oxauth/restv1/end_session?post_logout_redirect_uri=https://localhost:8443/php_gluu-master');
?>


<!--
<script type="text/javascript">
    document.getElementById("myButton").onclick = function () {
        location.href ="https://iam.centroxy.com/oxauth/restv1/end_session?post_logout_redirect_uri=https://localhost:8443/gluu_test";
    };
</script>
-->
