<?php

class OIDC
{
	private $client_id = "4131f00d-e197-4699-9f55-173c38fca8d8";
    private $client_secret = "l3HXSDpy6P55NwcCqDiLvdVv";
    
    private $authorization_endpoint = "https://iam4.centroxy.com/oxauth/restv1/authorize";
    private $token_endpoint = "https://iam4.centroxy.com/oxauth/restv1/token";
	private $userinfo_endpoint = "https://iam4.centroxy.com/oxauth/restv1/userinfo";
	private $end_session_endpoint = "https://iam4.centroxy.com/oxauth/restv1/end_session";

	private $scim_registeruser = "https://iam4.centroxy.com/identity/restv1/scim/v2/Users";
    
	private $callback_url = "https://localhost:8443/php_gluu-master/callback.php";
	private $post_logout_redirect_url = "https://localhost:8443/php_gluu-master";


	public function getClientId()
	{
		return $this->client_id;
	}
	
	public function getClientSecret()
	{
		return $this->client_secret;
	}
	
	public function getAuthorizationEndpoint()
	{
		return $this->authorization_endpoint;
    }
    
    public function getTokenEndpoint()
	{
		return $this->token_endpoint;
    }
    
    public function getUserInfoEndpoint()
	{
		return $this->userinfo_endpoint;
	}

	public function getEndSessionEndpoint()
	{
		return $this->end_session_endpoint;
	}
	
	public function getSCIMRegisterUser()
	{
		return $this->scim_registeruser;
	}

	public function getCallbackURL()
	{
		return $this->callback_url;
	}

	public function getPostLogoutRedirectURL()
	{
		return $this->post_logout_redirect_url;
	}
	
}

?>