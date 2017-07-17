<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');





class ActiveDirectory extends Core{

	function __construct() {
		parent::__construct();
		$this->activeDirectory = core::$coreConfig['ActiveDirectory'];

		foreach($this->activeDirectory as $id => $activeDirectory){
			$activeDirectory['id'] = $id;
			$dn = explode('.', $activeDirectory['domain']);
			$ldapDN = '';
			foreach($dn as $item){
			    $ldapDN = $ldapDN.'DC='.$item.',';
			}
			$ldapDN = substr($ldapDN, 0 , strlen($ldapDN)-1);

			// Connect to AD
			$ad = ldap_connect($activeDirectory['host']);
			if(!$ad){
			    return false;
			}
			ldap_set_option($ad, LDAP_OPT_DEBUG_LEVEL, 7);
			if(!ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3)){fatal_error("Failed to set LDAP Protocol version to 3, TLS not supported.");}
			if(!ldap_set_option($ad, LDAP_OPT_REFERRALS, 0)){fatal_error("Failed to set LDAP LDAP_OPT_REFERRALS, TLS not supported.");}

				$this->activeDirectory[$activeDirectory['id']]['ad'] = $ad;
				$this->activeDirectory[$activeDirectory['id']]['ldapDN'] = $ldapDN;
				$this->activeDirectory[$activeDirectory['id']]['id'] = $id;
				ldap_set_option($ad, LDAP_OPT_SIZELIMIT, 1500); //LIMITA A QUANTIDADE DE REGISTROS RETORNADOS
		}
    }

	public function getDomain($index){
		//var_dump($this->activeDirectory); die();
		foreach($this->activeDirectory as $activeDirectory){
			if($activeDirectory['id'] == $index){
				return $activeDirectory;
			}
			if($activeDirectory['domain'] == $index){
				return $activeDirectory;
			}
		}
	return false;
	}

	public function authenticate($login, $pass){ //TODO: Passar també o dominio por parametro
		if(!$login || !$pass){
			return false;
		}
		foreach($this->activeDirectory as $activeDirectory){
			//var_dump($this->activeDirectory ); die();
			$bd = @ldap_bind($activeDirectory['ad'], $login.'@'.$activeDirectory['domain'], $pass);
			if($bd){
				return true;
			}else{
				return false;
			}
		}
		return false;
	}




	public function searchUser($value = null){ //IN ALL DOMAINS
		$all = array();
		foreach($this->activeDirectory as $activeDirectory){
			// Bind to the directory server.
			$bd = @ldap_bind($activeDirectory['ad'], $activeDirectory['admin_user']."@".$activeDirectory['domain'], $activeDirectory['admin_pass']);
			if($bd){
				if($value == null){
					$filter="(&(objectCategory=person)(useraccountcontrol=512)(objectClass=user)(lockoutTime>=3))";
				}else{
					$filter="(&(objectCategory=user)(objectCategory=person)($value))";
				}
				$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);
				$info = ldap_get_entries($activeDirectory['ad'], $search);
				$return= ldap_get_entries($activeDirectory['ad'],$search);
				if($return['count'] > 0){
					$return[0]['domain'] = $activeDirectory['domain'];
					array_push($all, $return);
				}
			}else{
				//consoleWrite("Cant BIND to Active Directory!");
				echo "Cant BIND to Active Directory!";
			}
		}
		if(empty($all)){
			return array('domain' => 'No domain informed');
		}
		return $all;
	}

    public function searchDomainUser($value, $domain){ //IN CHOSEN ONE DOMAIN
		$all = array();
		$activeDirectory = $this->getDomain($domain);
		// Bind to the directory server.
		$connection =  $activeDirectory['admin_user']."@".$activeDirectory['domain'];
		//var_dump($activeDirectory);
		$bd = ldap_bind($activeDirectory['ad'], $connection, $activeDirectory['admin_pass']);
		if($bd){
			$filter="(&(objectCategory=user)(objectCategory=person)($value))";
			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);

			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);
			$info = ldap_get_entries($activeDirectory['ad'], $search);

			$return= ldap_get_entries($activeDirectory['ad'],$search);
			if($return['count'] > 0){
				array_push($all, $return);
				//var_dump($all);
				return $all;
			}
			//var_dump($return);
			return $return;
		}else{
			//consoleWrite("Cant BIND to Active Directory!");
		}
		return false;
    }



    public function searchDomainUserBlocked($value, $domain){ //IN CHOSEN ONE DOMAIN
		$all = array();
		$activeDirectory = $this->getDomain($domain);
		// Bind to the directory server.
		$bd = ldap_bind($activeDirectory['ad'], $activeDirectory['admin_user']."@".$activeDirectory['domain'], $activeDirectory['admin_pass']);
		if($bd){
			$filter="(&(&(&(&(objectCategory=person)(objectClass=user)(lockoutTime:1.2.840.113556.1.4.804:=4294967295)))))";
			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);

			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);
			$info = ldap_get_entries($activeDirectory['ad'], $search);

			$return= ldap_get_entries($activeDirectory['ad'],$search);
			if($return['count'] > 0){
				array_push($all, $return);
				//var_dump($all);
				return $all;
			}
			//var_dump($return);
			return $return;
		}else{
			//consoleWrite("Cant BIND to Active Directory!");
		}
		return false;
    }

    public function getUser($string, $domain = null){

    	if(isset($domain) && $domain<> 'all'){
    		//$activeDirectory = null;
    		if(isset($this->activeDirectory[$domain]))
    			$activeDirectory = $this->activeDirectory[$domain];
  			else
  				foreach($this->activeDirectory as $adDomain){
  					if($adDomain['domain'] == $domain)
  						$activeDirectory = $adDomain;
  				}

    		$bd = ldap_bind($activeDirectory['ad'], $activeDirectory['admin_user']."@".$activeDirectory['domain'], $activeDirectory['admin_pass']);
		    if($bd){
			$filter="(&(objectCategory=user)(objectCategory=person)($string))";
			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);

			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);
			$info = ldap_get_entries($activeDirectory['ad'], $search);

			$return= ldap_get_entries($activeDirectory['ad'],$search);
			if($return['count'] > 0){
			    $return[0]['domain'] = $activeDirectory['domain'];
			    return $return[0]; //RETURN THE FIRST ONE
			}
		    }else{
			//consoleWrite("Cant BIND to Active Directory!");
			return false;
		    }
    	}


		foreach($this->activeDirectory as $activeDirectory){
		    // Bind to the directory server.
		    $bd = ldap_bind($activeDirectory['ad'], $activeDirectory['admin_user']."@".$activeDirectory['domain'], $activeDirectory['admin_pass']);
		    if($bd){
			$filter="(&(objectCategory=user)(objectCategory=person)($string))";
			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);

			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);
			$info = ldap_get_entries($activeDirectory['ad'], $search);

			$return= ldap_get_entries($activeDirectory['ad'],$search);
			if($return['count'] > 0){
			    $return[0]['domain'] = $activeDirectory['domain'];
				$return = $return[0];
			    return $return; //RETURN THE FIRST ONE
			}
		    }else{
				//consoleWrite("Cant BIND to Active Directory!");
			return false;
		    }
		}
    }

    public function listUsersofaGroup($group){

		foreach($this->activeDirectory as $activeDirectory){
		    // Bind to the directory server.
		    $bd = ldap_bind($activeDirectory['ad'], $activeDirectory['admin_user']."@".$activeDirectory['domain'], $activeDirectory['admin_pass']);
		    if($bd){
			$filter="(&(objectCategory=user)(objectCategory=person)($group))";
			$search = @ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);
			$search = @ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);
			$info = ldap_get_entries($activeDirectory['ad'], $search);

			$return= ldap_get_entries($activeDirectory['ad'],$search);
			if($return['count'] > 0){
			    return $return;
			}
		    }else{
				return false;
		    }
		}
    }

    public function getUserDomain($string, $domain){
		$activeDirectory = $this->activeDirectory[$domain];

		    // Bind to the directory server.
		    $bd = ldap_bind($activeDirectory['ad'], $activeDirectory['admin_user']."@".$activeDirectory['domain'], $activeDirectory['admin_pass']);
		    if($bd){
			$filter="(&(objectCategory=user)(objectCategory=person)($string))";
			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);

			$search = ldap_search($activeDirectory['ad'],$activeDirectory['ldapDN'],$filter);
			$info = ldap_get_entries($activeDirectory['ad'], $search);

			$return= ldap_get_entries($activeDirectory['ad'],$search);
			if($return['count'] > 0){
			    $return[0]['domain'] = $activeDirectory['domain'];
			    return $return[0]; //RETURN THE FIRST ONE
			}
		    }else{
			//consoleWrite("Cant BIND to Active Directory!");
			return false;
		    }

    }




    public function unblockUser($user, $domain){
		$activeDirectory = $this->getDomain($domain);
		$ad = $activeDirectory['ad'];
		if($user['useraccountcontrol'][0] == 512 || $user['useraccountcontrol'][0] == 544 || $user['useraccountcontrol'][0] == 66048){
	 		try{
				$userdata["useraccountcontrol"][0]=512;
				$userdata["lockoutTime"][0]=0;
				$return = @ldap_modify($ad, $user['dn'], $userdata);
				$user2 = $this->getUser('samaccountname='.$user['samaccountname'][0]);
				if(ldap_errno($ad) || $user2['useraccountcontrol'][0] <> 512){
	 				return false;
	 			}
			}catch(Exception $e){
				throw new \Exception(ldap_errno($ad));
			}
			return true;
		}else{
			return false;
			//throw new \Exception(0);
		}
	}

	public function changeLogonHours($user, $turn){
		if(isset($user['userprincipalname'][0])){
			$activeDirectory = $this->getDomain(substr($user['userprincipalname'][0], strpos($user['userprincipalname'][0], '@')+1));

			$ad = $activeDirectory['ad'];
	 		try{
	 			if($user['samaccountname'][0] == 'belquihor.carvalho'){
	 				$userdata["logonhours"] = hex2bin($turn);
					$return = @ldap_modify($ad, $user['dn'], $userdata);
	 				return array($user['samaccountname'][0], $turn, $return);
	 			}
	 			return array($user['samaccountname'][0], $turn, true);
				//$userdata["logonhours"] = hex2bin($turn);
				//$return = @ldap_modify($ad, $user['dn'], $userdata);

				return $return;
			}catch(Exception $e){
				throw new \Exception(ldap_errno($ad));
			}
			return true;
		}else{
			return true;
		}

	}

	public function changePass($user, $newPass){


		$activeDirectory = $this->getDomain($user['domain']);
			//var_dump($activeDirectory);
		$ad = $activeDirectory['ad'];
		try{
			$ADSI = new \COM("LDAP:");
			$dsObject = $ADSI->OpenDSObject("LDAP://".$activeDirectory['host']."/".$user['dn'], $activeDirectory['admin_user'], $activeDirectory['admin_pass'], 1);

			//$dsObject->AccountDisabled = false;
			//$dsObject->SetPassword($newPass);
			//$dsObject->SetInfo();


			die();
			return true;
		}catch (Exception $e){
			var_dump($e); die();
			return false;
		}

	}

public	function pwd_encryption( $newPassword ) {
	$newPassword = "\"" . $newPassword . "\"";
	$len = strlen( $newPassword );
	$newPassw = "";
	for ( $i = 0; $i < $len; $i++ ){ $newPassw .= "{$newPassword{$i}}\000"; } $userdata["unicodePwd"] = $newPassw; return $userdata;
	 }
	public function get_user_dn( $ldap_conn, $user_name ) {
	/* Write the below details as per your AD setting */
	$basedn = "DC=AD Test,DC=Local";
	/* Search the user details in AD server */
	$searchResults = ldap_search( $ldap_conn, $basedn, $user_name );
	if ( !is_resource( $searchResults ) )
	die('Error in search results.');

	/* Get the first entry from the searched result */
	$entry = ldap_first_entry( $ldap_conn, $searchResults );
	return ldap_get_dn( $ldap_conn, $entry );
}


	public function createUser($user, $activeDirectory){


												// Conectando ao servidor
												if (!($connect=ldap_connect($activeDirectory['host'])))die("Impossível conectar ao servidor do ActiveDirectoryr");
												ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3) or die ("Could not set ldap protocol");
												ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
												ldap_set_option($connect, LDAP_OPT_SIZELIMIT, 1500); //LIMITA A QUANTIDADE DE REGISTROS RETORNADOS
												$connection =  $activeDirectory['admin_user']."@".$activeDirectory['domain'];
												$bd = ldap_bind($activeDirectory['ad'], $connection, $activeDirectory['admin_pass']);
												if ($bd){
													ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3); // IMPORTANT
													// prepare data
													$user['objectclass']= 'user';
													$user["UserAccountControl"] = 544;





													// add data to directory
													//var_dump($user);
													$dn = 'cn='.$user["cn"].','.$activeDirectory["ldapDN"];
													$r = ldap_add($activeDirectory['ad'], $dn, $user);


													switch (ldap_errno($activeDirectory['ad'])){
														case 'true':
														return 'Entry created with success';
														break;


														case 68:
														return 'Entry already exists';
														break;
													}




													//var_dump($r);
													ldap_close($connect);
													return true;


												}else{
													return false;
												}


	}







}
