<?php

//icontact
function snp_ml_get_ic_lists($ml_ic_username='', $ml_ic_addid='', $ml_ic_apppass='')
{
	require_once SNP_DIR_PATH . '/include/icontact/iContactApi.php';
	$list = array();
	if (
			(snp_get_option('ml_ic_username') && snp_get_option('ml_ic_addid') && snp_get_option('ml_ic_apppass')) ||
			($ml_ic_username && $ml_ic_addid && $ml_ic_apppass)
	)
	{
		if (!$ml_ic_username || !$ml_ic_addid || !$ml_ic_apppass)
		{
			$ml_ic_username = snp_get_option('ml_ic_username');
			$ml_ic_addid = snp_get_option('ml_ic_addid');
			$ml_ic_apppass = snp_get_option('ml_ic_apppass');
		}
		iContactApi::getInstance()->setConfig(array(
			'appId' => $ml_ic_addid,
			'apiPassword' => $ml_ic_apppass,
			'apiUsername' => $ml_ic_username
		));
		$oiContact = iContactApi::getInstance();
		try
		{
			$res = $oiContact->getLists();
			foreach ((array) $res as $v)
			{
				$list[$v->listId] = array('name' => $v->name);
			}
			//var_dump($oiContact->getLists());
		}
		catch (Exception $oException)
		{
			// Error
			// Catch any exceptions
			// Dump errors
			//var_dump($oiContact->getErrors());
			// Grab the last raw request data
			//var_dump($oiContact->getLastRequest());
			// Grab the last raw response data
			//var_dump($oiContact->getLastResponse());
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_aw_remove_auth()
{
	$return = array();
	delete_option('snp_ml_aw_auth_info');
	$return['Ok'] = true;
	return $return;
}
function snp_ml_get_aw_auth($ml_aw_auth_code)
{
	$return = array();
	require_once SNP_DIR_PATH . '/include/aweber/aweber_api.php';
	$descr = '';
	try
	{
		list($consumer_key, $consumer_secret, $access_key, $access_secret) = AWeberAPI::getDataFromAweberID($ml_aw_auth_code);
	}
	catch (AWeberAPIException $exc)
	{
		list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
		if(isset($exc->message))
		{
			$descr = $exc->message;
			$descr = preg_replace('/http.*$/i', '', $descr);	 # strip labs.aweber.com documentation url from error message
			$descr = preg_replace('/[\.\!:]+.*$/i', '', $descr); # strip anything following a . : or ! character
			$descr = '('.$descr.')';
		}
	}
	catch (AWeberOAuthDataMissing $exc)
	{
		list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
	}
	catch (AWeberException $exc)
	{
		list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
	}
	if (!$access_secret) 
	{
		$return['Error'] = 'Unable to connect to your AWeber Account ' .$descr;
		$return['Ok'] = false;
	}
	else
	{
		$ml_aw_auth_info = array(
			'consumer_key' => $consumer_key,
			'consumer_secret' => $consumer_secret,
			'access_key' => $access_key,
			'access_secret' => $access_secret,
		);
		update_option('snp_ml_aw_auth_info',$ml_aw_auth_info);
		$return['Ok'] = true;
	}
	return $return;
}
// aweber
function snp_ml_get_aw_lists()
{
	require_once SNP_DIR_PATH . '/include/aweber/aweber_api.php';
	$list = array();
	if (get_option('snp_ml_aw_auth_info'))
	{
		$aw = get_option('snp_ml_aw_auth_info');
		try {
			$aweber = new AWeberAPI($aw['consumer_key'], $aw['consumer_secret']);
			$account = $aweber->getAccount($aw['access_key'], $aw['access_secret']);
			$res = $account->lists;
			if($res)
			{
				foreach ((array) $res->data['entries'] as $v)
				{
					$list[$v['id']] = array('name' => $v['name']);
				}
			}
		}
		catch (AWeberException $e) 
		{
		    //echo $e;
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}

// mailchimp
function snp_ml_get_mc_lists($ml_mc_apikey='')
{
	require_once SNP_DIR_PATH . '/include/mailchimp/Mailchimp.php';
	$list = array();
	if (snp_get_option('ml_mc_apikey') || $ml_mc_apikey)
	{
		try
	    {
			if ($ml_mc_apikey)
			{
				$api = new Mailchimp($ml_mc_apikey);
			}
			else
			{
				$api = new Mailchimp(snp_get_option('ml_mc_apikey'));
			}
			$retval = $api->lists->getList();
			if (!isset($api->errorCode))
			{
				foreach ((array) $retval['data'] as $v)
				{
					$list[$v['id']] = array('name' => $v['name']);
				}
			}
		}
	    catch (Exception $exc)
	    {

	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}

// campaing monitor
function snp_ml_get_cm_lists($ml_cm_clientid='', $ml_cm_apikey='')
{
	require_once SNP_DIR_PATH . '/include/campaignmonitor/csrest_clients.php';
	$list = array();
	if (
			(snp_get_option('ml_cm_clientid') && snp_get_option('ml_cm_apikey')) ||
			($ml_cm_clientid && $ml_cm_apikey)
	)
	{
		if ($ml_cm_clientid && $ml_cm_apikey)
		{
			$wrap = new CS_REST_Clients($ml_cm_clientid, $ml_cm_apikey);
		}
		else
		{
			$wrap = new CS_REST_Clients(snp_get_option('ml_cm_clientid'), snp_get_option('ml_cm_apikey'));
		}
		$res = $wrap->get_lists();
		if ($res->was_successful())
		{
			foreach ((array) $res->response as $v)
			{
				$list[$v->ListID] = array('name' => $v->Name);
			}
		}
		else
		{
			// Error
			//echo 'Failed with code ' . $res->http_status_code . "\n<br /><pre>";
			//var_dump($res->response);
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
// mymail
function snp_ml_get_mm_lists()
{
	$list = array();
	$args = array(
		'orderby'       => 'name', 
		'order'         => 'ASC',
		'hide_empty'    => false, 
		'exclude'       => array(), 
		'exclude_tree'  => array(), 
		'include'       => array(),
		'fields'        => 'all', 
		'hierarchical'  => true, 
		'child_of'      => 0, 
		'pad_counts'    => false, 
		'cache_domain'  => 'core'
	); 
	if(function_exists('mymail'))
	{
	    // 2.x
	    $lists = mymail('lists')->get();
	    foreach($lists as $v)
	    {
		if($v->ID && $v->name)
		{
		    $list[$v->ID] = array('name' => $v->name);
		}
	    }
	}
	else
	{
	    // 1.x
	    $lists=get_terms( 'newsletter_lists', $args );
	    foreach($lists as $v)
	    {
		if($v->slug && $v->name)
		{
		    $list[$v->slug] = array('name' => $v->name);
		}
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
// sendpress
function snp_ml_get_sp_lists()
{
	if (defined('SENDPRESS_VERSION'))
	{
	    $lists = SendPress_Data::get_lists();
	    foreach($lists->posts as $v)
	    {
		if($v->ID && $v->post_title)
		{
		    $list[$v->ID] = array('name' => $v->post_title);
		}
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
// wysija
function snp_ml_get_wy_lists()
{
	$list = array();
	if(class_exists('WYSIJA'))
	{
		$modelList = &WYSIJA::get('list','model');
		$wysijaLists = $modelList->get(array('name','list_id'),array('is_enabled'=>1));
		foreach($wysijaLists as $v)
		{
			$list[$v['list_id']] = array('name' => $v['name']);
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
// getresponse
function snp_ml_get_gr_lists($ml_gr_apikey='')
{
	require_once SNP_DIR_PATH . '/include/getresponse/jsonRPCClient.php';
	$list = array();
	if (snp_get_option('ml_gr_apikey') || $ml_gr_apikey)
	{
		if (!$ml_gr_apikey)
		{
			$ml_gr_apikey = snp_get_option('ml_gr_apikey');
		}
		$api = new jsonRPCClient('http://api2.getresponse.com');
		try
		{
			$result = $api->get_campaigns($ml_gr_apikey);
			foreach ((array) $result as $k => $v)
			{
				$list[$k] = array('name' => $v['name']);
			}
		}
		catch (Exception $e)
		{
			//die($e->getMessage());
			// Error
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}

// Constant Contact
function snp_ml_get_cc_lists($ml_cc_username='', $ml_cc_pass='')
{
	require_once SNP_DIR_PATH . '/include/constantcontact/class.cc.php';

	$list = array();
	if (
			(snp_get_option('ml_cc_username') && snp_get_option('ml_cc_pass')) ||
			($ml_cc_username && $ml_cc_pass)
	)
	{
		if ($ml_cc_username && $ml_cc_pass)
		{
			$cc = new cc($ml_cc_username, $ml_cc_pass);
		}
		else
		{
			$cc = new cc(snp_get_option('ml_cc_username'), snp_get_option('ml_cc_pass'));
		}
		$res = $cc->get_all_lists('lists');
		if ($res)
		{
			foreach ((array) $res as $v)
			{
				$list[$v['id']] = array('name' => $v['Name']);
			}
		}
		else
		{
			// Error
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}

// madmimi
function snp_ml_get_madm_lists($ml_madm_username = '', $ml_madm_apikey = '')
{
    require_once SNP_DIR_PATH . '/include/madmimi/MadMimi.class.php';
    $list = array();
    if (
	    (snp_get_option('ml_madm_username') && snp_get_option('ml_madm_apikey')) ||
	    ($ml_madm_username && $ml_madm_apikey)
    )
    {
	try
	{
	    if ($ml_madm_username && $ml_madm_apikey)
	    {
		$mailer = new MadMimi($ml_madm_username, $ml_madm_apikey);
	    }
	    else
	    {
		$mailer	 = new MadMimi(snp_get_option('ml_madm_username'), snp_get_option('ml_madm_apikey'));
	    }
	    $lists	 = new SimpleXMLElement($mailer->Lists());
	    if ($lists->list)
	    {
		foreach ($lists->list as $l)
		{
		    $list[(string) $l->attributes()->{'name'}->{0}] = array('name' => (string) $l->attributes()->{'name'}->{0});
		}
	    }
	}
	catch (Exception $exc)
	{
	    
	}
    }
    if (count($list) == 0)
    {
	$list[0] = array('name' => 'Nothing Found...');
    }
    return $list;
}

// infusionsoft
function snp_ml_get_infusionsoft_lists($ml_inf_subdomain = '', $ml_inf_apikey = '')
{
    require_once SNP_DIR_PATH . '/include/infusionsoft/infusionsoft.php';

    $list = array();
    if (
	    (snp_get_option('ml_inf_subdomain') && snp_get_option('ml_inf_apikey')) ||
	    ($ml_inf_subdomain && $ml_inf_apikey)
    )
    {
	try
	{
	    if ($ml_inf_subdomain && $ml_inf_apikey)
	    {
		$infusionsoft	 = new Infusionsoft($ml_inf_subdomain, $ml_inf_apikey);
	    }
	    else
	    {
		$infusionsoft	 = new Infusionsoft(snp_get_option('ml_inf_subdomain'), snp_get_option('ml_inf_apikey'));
	    }
	    $fields = array('Id','GroupName');
	    $query = array('Id' => '%');
	    $result = $infusionsoft->data('query','ContactGroup',1000,0,$query,$fields);
	    if (is_array($result))
	    {
		foreach ($result as $l)
		{
		    $list[$l['Id']] = array('name' => $l['GroupName']);
		}
	    }
	}
	catch (Exception $exc)
	{
	    
	}
    }
    if (count($list) == 0)
    {
	$list[0] = array('name' => 'Nothing Found...');
    }
    return $list;
}
// egoi
function snp_ml_get_egoi_lists($ml_egoi_apikey='')
{
	$list = array();
	if (snp_get_option('ml_egoi_apikey') || $ml_egoi_apikey)
	{
		if (!$ml_egoi_apikey)
		{
			$ml_egoi_apikey = snp_get_option('ml_egoi_apikey');
		}
		$params = array('apikey' => $ml_egoi_apikey);
		try
		{
			$client = new SoapClient('http://api.e-goi.com/v2/soap.php?wsdl');
			$result = $client->getLists($params);
			if (is_array($result))
			{
			    foreach ($result as $l)
			    {
				$list[$l['listnum']] = array('name' => $l['title']);
			    }
			}
		}
		catch (Exception $e)
		{
			//die($e->getMessage());
			// Error
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}