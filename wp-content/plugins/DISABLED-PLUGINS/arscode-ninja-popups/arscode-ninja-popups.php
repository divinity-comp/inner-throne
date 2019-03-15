<?php
/*
  Plugin Name: Ninja Popups
  Plugin URI: http://codecanyon.net/item/ninja-popups-for-wordpress/3476479?ref=arscode
  Description: Awesome Popups for Your WordPress!
  Version: 3.9.6
  Author: ArsCode
  Author URI: http://www.arscode.pro/
 */

if (!defined('ABSPATH'))
{
	die('-1');
}
define('SNP_OPTIONS', 'snp');
define('SNP_DB_VER', '1.2');
define('SNP_URL', plugins_url('/', __FILE__));
//define('SNP_DIR_PATH', plugin_dir_path(__FILE__));
define('SNP_DIR_PATH', plugin_dir_path(__FILE__));
//echo SNP_DIR_PATH;
define('SNP_PROMO_LINK', 'http://codecanyon.net/item/ninja-popups-for-wordpress/3476479?ref=');
$snp_options = array();
$snp_popups = array();
if(is_admin())
{
    require_once( plugin_dir_path(__FILE__) . '/admin/options.php' );
    require_once( plugin_dir_path(__FILE__) . '/admin/init.php' );
    require_once( plugin_dir_path(__FILE__) . '/admin/updates.php' );
    require_once( plugin_dir_path(__FILE__) . '/include/lists.inc.php' );    
}
require_once( plugin_dir_path(__FILE__) . '/include/fonts.inc.php' );
require_once( plugin_dir_path(__FILE__) . '/include/functions.inc.php' );
require_once( plugin_dir_path(__FILE__) . '/include/snp_links.inc.php' );

function snp_get_option($opt_name, $default = null)
{
	global $snp_options;
	if (!$snp_options)
	{
		$snp_options = get_option(SNP_OPTIONS);
	}
	return (!empty($snp_options[$opt_name])) ? $snp_options[$opt_name] : $default;
}
global $snp_ignore_cookies;
$SNP_THEMES = array();

//$SNP_THEMES_DIR = plugin_dir_path(__FILE__) . '/themes/';
$SNP_THEMES_DIR_2 = apply_filters( 'snp_themes_dir_2', '' );
$SNP_THEMES_DIR = apply_filters( 'snp_themes_dir', array(plugin_dir_path(__FILE__) . '/themes/', $SNP_THEMES_DIR_2));
function snp_popup_submit()
{
	global $wpdb;
	$result = array();
	$errors = array();
	$_POST['email'] = trim($_POST['email']);
	if (isset($_POST['name']))
	{
		$_POST['name'] = trim($_POST['name']);
	}
	if (!snp_is_valid_email($_POST['email']))
	{
		$errors['email'] = 1;
	}
	if (isset($_POST['name']) && !$_POST['name'])
	{
		$errors['name'] = 1;
	}
	$post_id = intval($_POST['popup_ID']);
	if($post_id)
	{
	    $POPUP_META = get_post_meta($post_id);
	}
	$cf_data=array();
	if (isset($POPUP_META['snp_cf']) && $post_id)
	{
	    $cf = unserialize($POPUP_META['snp_cf'][0]);
	    if (isset($cf) && is_array($cf))
	    {
		foreach ($cf as $f)
		{
		    if (isset($f['name']))
		    {
			if (strpos($f['name'], '['))
			{
			    $f['name'] = substr($f['name'], 0, strpos($f['name'], '['));
			}
			if (!empty($_POST[$f['name']]))
			{
			    $cf_data[$f['name']] = $_POST[$f['name']];
			}
		    }
		    if(isset($f['required']) && $f['required']=='Yes' && !$cf_data[$f['name']])
		    {
			$errors[$f['name']] = 1;
		    }
		}
	    }
	}
	if (count($errors) > 0)
	{
		$result['Errors'] = $errors;
		$result['Ok'] = false;
	}
	else
	{
		$Done = 0;
		if(!empty($_POST['name']))
		{
		    $names=snp_detect_names($_POST['name']);
		}
		else
		{
		    $names=array('first' => '','last' => '');
		}
		
		
		
		$api_error_msg='';
		if ( snp_get_option('ml_manager') == 'directmail' ) 
		{
			require_once SNP_DIR_PATH . '/include/directmail/class.directmail.php';
			$form_id = snp_get_option('ml_dm_form_id');
			
			if($form_id)
			{
				$api = new DMSubscribe();
				$retval = $api->submitSubscribeForm($form_id, $_POST['email'], $error_message);
				
				if ($retval) {
					$Done = 1;
				}
				else {
					// Error... Send by email?
					$api_error_msg=$error_message;
				}
			}
		}
		elseif ( snp_get_option('ml_manager') == 'sendy' ) 
		{
			$list_id=$POPUP_META['snp_ml_sendy_list'][0];
			if(!$list_id)
			{
			   $list_id=snp_get_option('ml_sendy_list');
			}		
			if($list_id)
			{
			    $options = array(
				'list' => $list_id,
				'boolean' => 'true'
			    );
			    $args['email'] = $_POST['email'];
			    if (!empty($_POST['name']))
			    {
				    $args['name'] = $_POST['name'];
			    }
			    if(count($cf_data)>0)
			    {
				$args=array_merge($args, (array) $cf_data);
			    }
			    $content = array_merge($args, $options);
			    $postdata = http_build_query($content);
			    $ch = curl_init(snp_get_option('ml_sendy_url') .'/subscribe');
			    curl_setopt($ch, CURLOPT_HEADER, 0);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			    curl_setopt($ch, CURLOPT_POST, 1);
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			    $api_result = curl_exec($ch);
			    curl_close($ch);
			    if (strval($api_result)=='true' || strval($api_result)=='1' || strval($api_result)=='Already subscribed.') 
			    {
			        $Done = 1;
			    }
			    else 
			    {
			        $api_error_msg=$api_result;
			    }
			}
		}
		elseif (snp_get_option('ml_manager') == 'mailchimp')
		{
			require_once SNP_DIR_PATH . '/include/mailchimp/Mailchimp.php';
			$ml_mc_list=$POPUP_META['snp_ml_mc_list'][0];
			if(!$ml_mc_list)
			{
			   $ml_mc_list=snp_get_option('ml_mc_list');
			}
			if (snp_get_option('ml_mc_apikey') && $ml_mc_list)
			{
				$api = new Mailchimp(snp_get_option('ml_mc_apikey'));
				$args = array();
				if (!empty($_POST['name']))
				{
					$args = array('FNAME' => $names['first'], 'LNAME' => $names['last'] );
				}
				if(count($cf_data)>0)
				{
				    $args=array_merge($args, (array) $cf_data);
				}
				try
				{
				    $double_optin=snp_get_option('ml_mc_double_optin');
				    if($double_optin==1)
				    {
					$double_optin=true;
				    }
				    else
				    {
					$double_optin=false;
				    }
				    $double_optin=snp_get_option('ml_mc_double_optin');
				    if($double_optin==1)
				    {
					$double_optin=true;
				    }
				    else
				    {
					$double_optin=false;
				    }
				    $send_welcome=snp_get_option('ml_mc_send_welcome');
				    if($send_welcome==1)
				    {
					$send_welcome=true;
				    }
				    else
				    {
					$send_welcome=false;
				    }
				    $retval = $api->lists->subscribe($ml_mc_list, array('email'=>$_POST['email']), $args, 'html', $double_optin, false, true, $send_welcome);
				    $Done = 1;
				}
				catch (Exception $e)
				{
				    if($e->getCode()==214)
				    {
					 $Done = 1;
				    }
				    else
				    {
					$api_error_msg=$e->getMessage();
				    }
				}
			}
		}
		elseif (snp_get_option('ml_manager') == 'egoi')
		{
			$ml_egoi_apikey = snp_get_option('ml_egoi_apikey');
			$client = new SoapClient('http://api.e-goi.com/v2/soap.php?wsdl');
			try
			{
			    $ml_egoi_list=$POPUP_META['snp_ml_egoi_list'][0];
			    if(!$ml_egoi_list)
			    {
			       $ml_egoi_list=snp_get_option('ml_egoi_list');
			    }
			    $args = array(
				    'apikey' => $ml_egoi_apikey,
				    'listID' => $ml_egoi_list,
				    'email' => $_POST['email'],
			    );
			    if (!empty($_POST['name']))
			    {
				    $args['first_name'] = $names['first'];
				    $args['last_name'] = $names['last'];
			    }
			    if(count($cf_data)>0)
			    {
				$CustomFields=array();
				foreach($cf_data as $k => $v)
				{
				    $args[$k]= $v;
				}
			    }
			    $res = $client->addSubscriber($args);
			    if(isset($res['UID']))
			    {
				$Done = 1;
			    }
			}
			catch (Exception $e)
			{
				// Error...
				// We'll send this by email.
			}
		}
		elseif (snp_get_option('ml_manager') == 'getresponse')
		{
			$ml_gr_apikey = snp_get_option('ml_gr_apikey');
			require_once SNP_DIR_PATH . '/include/getresponse/jsonRPCClient.php';
			$api = new jsonRPCClient('http://api2.getresponse.com');
			try
			{
			    $ml_gr_list=$POPUP_META['snp_ml_gr_list'][0];
			    if(!$ml_gr_list)
			    {
			       $ml_gr_list=snp_get_option('ml_gr_list');
			    }
			    $args = array(
				    'campaign' => $ml_gr_list,
				    'email' => $_POST['email'],
			    );
			    if (!empty($_POST['name']))
			    {
				    $args['name'] = $_POST['name'];
			    }
			    if(count($cf_data)>0)
			    {
				$CustomFields=array();
				foreach($cf_data as $k => $v)
				{
				    $CustomFields[]=array(
					'name' => $k,
					'content' => $v
				    );
				}
				$args['customs']=$CustomFields;
			    }
			    $res = $api->add_contact($ml_gr_apikey, $args);
			    $Done = 1;
			}
			catch (Exception $e)
			{
				// Error...
				// We'll send this by email.
				$api_error_msg=$e->getMessage();
			}
		}
		elseif (snp_get_option('ml_manager') == 'campaignmonitor')
		{
			require_once SNP_DIR_PATH . '/include/campaignmonitor/csrest_subscribers.php';
			$ml_cm_list=$POPUP_META['snp_ml_cm_list'][0];
			if(!$ml_cm_list)
			{
			   $ml_cm_list=snp_get_option('ml_cm_list');
			}
			$wrap = new CS_REST_Subscribers($ml_cm_list, snp_get_option('ml_cm_apikey'));
			$args = array(
				'EmailAddress' => $_POST['email'],
				'Resubscribe' => true
			);
			if (!empty($_POST['name']))
			{
				$args['Name'] = $_POST['name'];
			}
			if(count($cf_data)>0)
			{
			    $CustomFields=array();
			    foreach($cf_data as $k => $v)
			    {
				$CustomFields[]=array(
				    'Key' => $k,
		                    'Value' => $v
				);
			    }
			    $args['CustomFields']=$CustomFields;
			}
			$res = $wrap->add($args);
			if ($res->was_successful())
			{
				$Done = 1;
			}
			else
			{
				// Error...
				// We'll send this by email.
				$api_error_msg='Failed with code ' . $res->http_status_code;
			}
		}
		elseif (snp_get_option('ml_manager') == 'icontact')
		{
			require_once SNP_DIR_PATH . '/include/icontact/iContactApi.php';
			iContactApi::getInstance()->setConfig(array(
				'appId' => snp_get_option('ml_ic_addid'),
				'apiPassword' => snp_get_option('ml_ic_apppass'),
				'apiUsername' => snp_get_option('ml_ic_username')
			));
			$oiContact = iContactApi::getInstance();
			$res1 = $oiContact->addContact($_POST['email'], null, null, (isset($names['first']) ? $names['first'] : ''), (isset($names['last']) ? $names['last'] : ''), null, null, null, null, null, null, null, null, null);
			if ($res1->contactId)
			{
			    $ml_ic_list=$POPUP_META['snp_ml_ic_list'][0];
			    if(!$ml_ic_list)
			    {
			       $ml_ic_list=snp_get_option('ml_ic_list');
			    }
			    if ($oiContact->subscribeContactToList($res1->contactId, $ml_ic_list, 'normal'))
			    {
				    $Done = 1;
			    }
			}
			else
			{
				// Error...
				// We'll send this by email.
				$api_error_msg='iContact Problem!';
			}
		}
		elseif (snp_get_option('ml_manager') == 'constantcontact')
		{
			require_once SNP_DIR_PATH . '/include/constantcontact/class.cc.php';
			$cc = new cc(snp_get_option('ml_cc_username'), snp_get_option('ml_cc_pass'));
			$send_welcome=snp_get_option('ml_cc_send_welcome');
			if($send_welcome==1)
			{
			     $cc->set_action_type('contact');
			}
			$email = $_POST['email'];
			$contact_list=$POPUP_META['snp_ml_cc_list'][0];
			if(!$contact_list)
			{
			   $contact_list=snp_get_option('ml_cc_list');
			}
			$extra_fields = array(
			);
			if (!empty($names['first']))
			{
				$extra_fields['FirstName'] = $names['first'];
			}
			if (!empty($names['last']))
			{
				$extra_fields['LastName'] = $names['last'];
			}
			if(count($cf_data)>0)
			{
			    $extra_fields=array_merge($extra_fields, (array) $cf_data);
			}
			$contact = $cc->query_contacts($email);
			if ($contact)
			{
				$status = $cc->update_contact($contact['id'], $email, $contact_list, $extra_fields);
				if ($status)
				{
					$Done = 1;
				}
				else
				{
					$api_error_msg="Contact Operation failed: " . $cc->http_get_response_code_error($cc->http_response_code);
				}
			}
			else
			{
				$new_id = $cc->create_contact($email, $contact_list, $extra_fields);
				if ($new_id)
				{
					$Done = 1;
				}
				else
				{
					$api_error_msg="Contact Operation failed: " . $cc->http_get_response_code_error($cc->http_response_code);
				}
			}
		}
		elseif (snp_get_option('ml_manager') == 'madmimi')
		{
		    require_once SNP_DIR_PATH . '/include/madmimi/MadMimi.class.php';
		    if (snp_get_option('ml_madm_username') && snp_get_option('ml_madm_apikey'))
		    {
			    $mailer	 = new MadMimi(snp_get_option('ml_madm_username'), snp_get_option('ml_madm_apikey'));
			    $user = array('email' => $_POST['email']);
			    if (!empty($names['first']))
			    {
				    $user['FirstName'] = $names['first'];
			    }
			    if (!empty($names['last']))
			    {
				    $user['LastName'] = $names['last'];
			    }
			    if(count($cf_data)>0)
			    {
				$user=array_merge($user, (array) $cf_data);
			    }
			    $ml_madm_list=$POPUP_META['snp_ml_madm_list'][0];
			    if(!$ml_madm_list)
			    {
			       $ml_madm_list=snp_get_option('ml_madm_list');
			    }
			    $user['add_list']=$ml_madm_list;
			    $res=$mailer->AddUser($user); 
			    $Done = 1;
		    }
		}
		elseif (snp_get_option('ml_manager') == 'infusionsoft')
		{
		    require_once SNP_DIR_PATH . '/include/infusionsoft/infusionsoft.php';
		    if (snp_get_option('ml_inf_subdomain') && snp_get_option('ml_inf_apikey'))
		    {
			    $infusionsoft	 = new Infusionsoft(snp_get_option('ml_inf_subdomain'), snp_get_option('ml_inf_apikey'));
			    $user = array('Email' => $_POST['email']);
			    if (!empty($names['first']))
			    {
				    $user['FirstName'] = $names['first'];
			    }
			    if (!empty($names['last']))
			    {
				    $user['LastName'] = $names['last'];
			    }
			    if(count($cf_data)>0)
			    {
				$user=array_merge($user, (array) $cf_data);
			    }
			    $ml_inf_list=$POPUP_META['snp_ml_inf_list'][0];
			    if(!$ml_inf_list)
			    {
			       $ml_inf_list=snp_get_option('ml_inf_list');
			    }
			    $contact_id = $infusionsoft->contact( 'add', $user );
                            $r = $infusionsoft->APIEmail('optIn', $_POST['email'], "Ninja Popups on ".get_bloginfo());
			    if($contact_id && $ml_inf_list)
			    {
				$infusionsoft->contact( 'addToGroup', $contact_id, $ml_inf_list);
			    }
			    if($contact_id)
			    {
				$Done = 1;
			    }
		    }
		}		
		elseif (snp_get_option('ml_manager') == 'aweber')
		{
			require_once SNP_DIR_PATH . '/include/aweber/aweber_api.php';
			if (get_option('snp_ml_aw_auth_info'))
			{
				$aw = get_option('snp_ml_aw_auth_info');
				try
				{
					$aweber = new AWeberAPI($aw['consumer_key'], $aw['consumer_secret']);
					$account = $aweber->getAccount($aw['access_key'], $aw['access_secret']);
					$aw_list=$POPUP_META['snp_ml_aw_lists'][0];
					if(!$aw_list)
					{
					   $aw_list=snp_get_option('ml_aw_lists');
					}
					$list = $account->loadFromUrl('/accounts/' . $account->id . '/lists/' . $aw_list);
					$subscriber = array(
						'email' => $_POST['email'],
						'ip' => $_SERVER['REMOTE_ADDR']
					);
					if (!empty($_POST['name']))
					{
						$subscriber['name'] = $_POST['name'];
					}
					if(count($cf_data)>0)
					{
					    $subscriber['custom_fields'] = $cf_data;
					}
					$r=$list->subscribers->create($subscriber);
					$Done = 1;
				}
				catch (AWeberException $e)
				{
					$api_error_msg=$e->getMessage();
				}
			}
		}
		elseif (snp_get_option('ml_manager') == 'wysija' && class_exists('WYSIJA'))
		{
			$ml_wy_list=$POPUP_META['snp_ml_wy_list'][0];
			if(!$ml_wy_list)
			{
			   $ml_wy_list=snp_get_option('ml_wy_list');
			}
			$userData = array(
				'email' => $_POST['email'],
				'firstname' => $names['first'],
				'lastname' => $names['last']);
			$data = array(
				'user' => $userData,
				'user_list' => array('list_ids' => array($ml_wy_list))
			);
			$userHelper = &WYSIJA::get('user', 'helper');
			if($userHelper->addSubscriber($data))
			{
				$Done = 1;
			}
			else
			{
			    $api_error_msg='MailPoet Problem!';
			}
		}
		elseif (snp_get_option('ml_manager') == 'sendpress')
		{
		    $ml_sp_list=$POPUP_META['snp_ml_sp_list'][0];
		    if(!$ml_sp_list)
		    {
		       $ml_sp_list=snp_get_option('ml_sp_list');
		    }
		    try
		    {
			SendPress_Data::subscribe_user($ml_sp_list, $_POST['email'], $names['first'], $names['last'], 2);
			$Done = 1;
		    }
		    catch (Exception $e)
		    {
			$api_error_msg='SendPress Problem!';
		    }
		}
		elseif (snp_get_option('ml_manager') == 'mymail')
		{
			$userdata = array(
				'firstname' => $names['first'],
				'lastname' => $names['last']
			    );
			$ml_mm_list=$POPUP_META['snp_ml_mm_list'][0];
			if(!$ml_mm_list)
			{
			   $ml_mm_list=snp_get_option('ml_mm_list');
			}
			$lists  = array($ml_mm_list);
			if(function_exists('mymail'))
			{
			    $entry = $userdata;
			    $entry['email'] = $_POST['email'];
			    $double_optin=snp_get_option('ml_mm_double_optin');
			    if($double_optin==1)
			    {
				$entry['status'] = 0;
			    }
			    else
			    {
				$entry['status'] = 1;
			    }
			    if(count($cf_data)>0)
			    {
				foreach($cf_data as $k => $v)
				{
				    $entry[$k] = $v;
				}
			    }
			    $subscriber_id = mymail('subscribers')->add($entry, true);
			    if ( !is_wp_error($subscriber_id) )
			    {
				$success = mymail('subscribers')->assign_lists($subscriber_id, $lists, false);
			    }
			    if($success)
			    {
				$Done = 1;
			    }
			    else
			    {
				$api_error_msg='MyMail Problem!';
			    }
			}
			else
			{
			    $return = mymail_subscribe($_POST['email'], $userdata, $lists);
			    if ( !is_wp_error($return) )
			    {
				    $Done = 1;
			    }
			    else
			    {
				$api_error_msg='MyMail Problem!';
			    }
			}
		}
		elseif (snp_get_option('ml_manager') == 'csv' && snp_get_option('ml_csv_file') && is_writable(SNP_DIR_PATH . 'csv/'))
		{
			if(!isset($_POST['name']))
			{
				$_POST['name']='';
			}
			if(count($cf_data)>0)
			{
			    $CustomFields='';
			    foreach($cf_data as $k => $v)
			    {
				$CustomFields.= $k.' = '.$v.';';
			    }
			}
			$data = $_POST['email'] . ";" . $_POST['name'] . ";" . $CustomFields . get_the_title($_POST['popup_ID']) . " (" . $_POST['popup_ID'] . ");" . date('Y-m-d H:i') . ";" . $_SERVER['REMOTE_ADDR'] . ";\n";
			if (file_put_contents(SNP_DIR_PATH . 'csv/' . snp_get_option('ml_csv_file'), $data, FILE_APPEND | LOCK_EX) !== FALSE)
			{
				$Done = 1;
			}
			else
			{
			    $api_error_msg='CSV Problem!';
			}
		}
		if (snp_get_option('ml_manager') == 'email' || !$Done)
		{
			$Email = snp_get_option('ml_email');
			if (!$Email)
			{
				$Email = get_bloginfo('admin_email');
			}
			if(!isset($_POST['name']))
			{
				$_POST['name']='--';
			}
			$error_mgs = '';
			if($api_error_msg!='')
			{
			    $error_mgs.="IMPORTANT! You have received this message because connection to your e-mail marketing software failed. Please check connection setting in the plugin configuration.\n";
			    $error_mgs.=$api_error_msg."\n";
			}
			$cf_msg = '';
			if(count($cf_data)>0)
			{
			    foreach($cf_data as $k => $v)
			    {
				$cf_msg .= $k.": " . $v . "\n";
			    }
			}
			$msg = 
			"New subscription on " . get_bloginfo() . "\n".
			$error_mgs.	
			"\n".
			"E-mail: " . $_POST['email'] . "\n".
			"Name: " . $_POST['name'] . "\n".
			$cf_msg.
			"\n".
			"Form: " . get_the_title($_POST['popup_ID']) . " (" . $_POST['popup_ID'] . ")\n".
			"\n".
			"Date: " . date('Y-m-d H:i') . "\n".
			"IP: " . $_SERVER['REMOTE_ADDR'] . "";
			wp_mail($Email, "New subscription on " . get_bloginfo(), $msg);
		}
		$result['Ok'] = true;
	}
	echo json_encode($result);
	die('');
}

function snp_popup_stats()
{
	global $wpdb;
	$table_name	 = $wpdb->prefix . "snp_stats";
	$ab_id	 = intval($_POST['ab_ID']);
	$post_id = intval($_POST['popup_ID']);
	if (current_user_can( 'manage_options' )) {
		die('');
	}
	if ($post_id > 0)
	{
		if ($_POST['type'] == 'view')
		{
			$count = get_post_meta($post_id, 'snp_views');
			if (!$count || !$count[0])
				$count[0] = 0;
			update_post_meta($post_id, 'snp_views', $count[0] + 1);
			if($ab_id)
			{
			    $count		 = get_post_meta($ab_id, 'snp_views');
			    if (!$count || !$count[0])
				$count[0]	 = 0;
			    update_post_meta($ab_id, 'snp_views', $count[0] + 1);
			}
			$wpdb->query("insert into $table_name (`date`,`ID`,`AB_ID`,imps) values (CURDATE(),$post_id,$ab_id,1) on duplicate key update imps = imps + 1;"); 
			echo 'ok: view';
		}
		else
		{
			$count = get_post_meta($post_id, 'snp_conversions');
			if (!$count || !$count[0])
				$count[0] = 0;
			update_post_meta($post_id, 'snp_conversions', $count[0] + 1);
			if($ab_id)
			{
			    $count		 = get_post_meta($ab_id, 'snp_conversions');
			    if (!$count || !$count[0])
				$count[0]	 = 0;
			    update_post_meta($ab_id, 'snp_conversions', $count[0] + 1);
			}
			$wpdb->query("insert into $table_name (`date`,`ID`,`AB_ID`,convs) values (CURDATE(),$post_id,$ab_id,1) on duplicate key update convs = convs + 1;"); 
			echo 'ok: conversion';
		}
	}
	die('');
}

function snp_get_theme($theme)
{
	global $SNP_THEMES, $SNP_THEMES_DIR;
	if (!$theme)
	{
		return false;
	}
	foreach($SNP_THEMES_DIR as $DIR)
	{
	    if (is_dir($DIR . '/' . $theme . '') && is_file($DIR . '/' . $theme . '/theme.php'))
	    {
		require_once( $DIR . '/' . $theme . '/theme.php' );
		$SNP_THEMES[$theme]['DIR']=$DIR . '/' . $theme . '/';
		return $SNP_THEMES[$theme];
	    }  
	}
	return false;
}

function snp_get_themes_list()
{
	global $SNP_THEMES, $SNP_THEMES_DIR;
	if (count($SNP_THEMES) == 0)
	{
		$files = array();
		foreach($SNP_THEMES_DIR as $DIR)
		{
		    if (is_dir($DIR))
		    {
			    if ($dh = opendir($DIR))
			    {
				    while (($file = readdir($dh)) !== false)
				    {
					    if (is_dir($DIR . '/' . $file) && $file != '.' && $file != '..')
					    {
						    $files[] = $file;
					    }
				    }
				    closedir($dh);
			    }
		    }
		}
		sort($files);
		foreach ($files as $file)
		{
			snp_get_theme($file);
		}
	}
	//print_r($SNP_THEMES);

	return $SNP_THEMES;
}

function snp_popup_fields_list($popup)
{
	global $SNP_THEMES;
	$popup = trim($popup);
	if (is_array($SNP_THEMES) && is_array($SNP_THEMES[$popup]))
	{
		return $SNP_THEMES[$popup]['FIELDS'];
	}
	else
	{
		return array();
	}
}

function snp_popup_fields()
{
	global $SNP_THEMES, $SNP_NHP_Options, $post;
	if(!$post)
	{
		$post = (object)array();
	}
	$post->ID = intval($_POST['snp_post_ID']);
	snp_get_themes_list();
	if ($SNP_THEMES[$_POST['popup']])
	{
		$SNP_NHP_Options->_custom_fields_html('snp_popup_fields', $_POST['popup']);
	}
	else
	{
		echo 'Error...';
	}
	die();
}

function snp_ml_list()
{
	require_once( plugin_dir_path(__FILE__) . '/include/lists.inc.php' );
	if ($_POST['ml_manager'] == 'mailchimp')
	{
		echo json_encode(snp_ml_get_mc_lists($_POST['ml_mc_apikey']));
	}
	elseif ($_POST['ml_manager'] == 'getresponse')
	{
		echo json_encode(snp_ml_get_gr_lists($_POST['ml_gr_apikey']));
	}
	elseif ($_POST['ml_manager'] == 'campaignmonitor')
	{
		echo json_encode(snp_ml_get_cm_lists($_POST['ml_cm_clientid'], $_POST['ml_cm_apikey']));
	}
	elseif ($_POST['ml_manager'] == 'icontact')
	{
		echo json_encode(snp_ml_get_ic_lists($_POST['ml_ic_username'], $_POST['ml_ic_addid'], $_POST['ml_ic_apppass']));
	}
	elseif ($_POST['ml_manager'] == 'constantcontact')
	{
		echo json_encode(snp_ml_get_cc_lists($_POST['ml_cc_username'], $_POST['ml_cc_pass']));
	}
	elseif ($_POST['ml_manager'] == 'aweber_auth')
	{
		echo json_encode(snp_ml_get_aw_auth($_POST['ml_aw_auth_code']));
	}
	elseif ($_POST['ml_manager'] == 'aweber_remove_auth')
	{
		echo json_encode(snp_ml_get_aw_remove_auth());
	}
	elseif ($_POST['ml_manager'] == 'aweber')
	{
		echo json_encode(snp_ml_get_aw_lists());
	}
	elseif ($_POST['ml_manager'] == 'wysija')
	{
		echo json_encode(snp_ml_get_wy_lists());
	}
	elseif ($_POST['ml_manager'] == 'madmimi')
	{
		echo json_encode(snp_ml_get_madm_lists($_POST['ml_madm_username'], $_POST['ml_madm_apikey']));
	}
	elseif ($_POST['ml_manager'] == 'infusionsoft')
	{
		echo json_encode(snp_ml_get_infusionsoft_lists($_POST['ml_inf_subdomain'], $_POST['ml_inf_apikey']));
	}
	elseif ($_POST['ml_manager'] == 'mymail')
	{
		echo json_encode(snp_ml_get_mm_lists());
	}
	elseif ($_POST['ml_manager'] == 'sendpress')
	{
		echo json_encode(snp_ml_get_sp_lists());
	}
	elseif ($_POST['ml_manager'] == 'egoi')
	{
		echo json_encode(snp_ml_get_egoi_lists($_POST['ml_egoi_apikey']));
	}
	else
	{
		echo json_encode(array());
	}
	die();
}

function snp_popup_colors()
{
	global $SNP_THEMES, $SNP_NHP_Options, $post;
	snp_get_themes_list();
	echo json_encode($SNP_THEMES[$_POST['popup']]['COLORS']);
	die();
}

function snp_popup_types()
{
	global $SNP_THEMES, $SNP_NHP_Options, $post;
	snp_get_themes_list();
	echo json_encode($SNP_THEMES[$_POST['popup']]['TYPES']);
	die();
}

function snp_init()
{
	if (!snp_get_option('js_disable_jq_cookie') || is_admin())
	{
		// jQuery Cookie
		wp_enqueue_script(
				'jquery-np-cookie', plugins_url('/js/jquery.ck.min.js', __FILE__), array('jquery'), false, true
		);
	}
	if (!snp_get_option('js_disable_fancybox') || is_admin())
	{
		// Fancybox 2
		wp_register_style('fancybox2', plugins_url('/fancybox2/jquery.fancybox.min.css', __FILE__));
		wp_enqueue_style('fancybox2');
		wp_enqueue_script(
				'fancybox2', plugins_url('/fancybox2/jquery.fancybox.min.js', __FILE__), array('jquery'), false, true
		);
	}
	if (!snp_get_option('js_disable_jq_placeholder') || is_admin())
	{
		// jquery.placeholder.js
		wp_enqueue_script(
			'jquery-np-placeholder', plugins_url('/js/jquery.placeholder.js', __FILE__), array('jquery'), false, true
		);
	}	
	wp_enqueue_script(
		'js-ninjapopups', plugins_url('/js/ninjapopups.min.js', __FILE__), array('jquery'), false, true
	);
}




function snp_run_popup($ID, $type)
{
	global $snp_popups, $PREVIEW_POPUP_META;
	if (!$ID && $ID != -1)
	{
		return;
	}
	snp_init();
	if ($ID == -1)
	{
		$POPUP_META = $PREVIEW_POPUP_META;
		// gm
		foreach ($POPUP_META as $k => $v)
		{
		    if (is_array($v))
		    {
			$v = serialize($v);
		    }
		    else
		    {
			$v		 = stripslashes($v);
		    }
		    $POPUP_META[$k]	 = $v;
		    $PREVIEW_POPUP_META[$k]	 = $v;
		}
	}
	else
	{
		if(strpos($ID,'ab_')===0) 
		{
		    $AB_ID = str_replace('ab_', '', $ID);
		    $AB_META = get_post_meta($AB_ID);
		    if(!isset($AB_META['snp_forms']))
		    {
			return;
		    }
		    $AB_META['snp_forms'] = array_keys(unserialize($AB_META['snp_forms'][0]));
		    if(!is_array($AB_META['snp_forms']) || count($AB_META['snp_forms'])==0)
		    {
			return;
		    }
		    $ID=$AB_META['snp_forms'][array_rand($AB_META['snp_forms'])];
		}
		if(get_post_status($ID)!='publish')
		{
		    return;
		}
		$POPUP_META = get_post_meta($ID);
		foreach ((array) $POPUP_META as $k => $v)
		{
			$POPUP_META[$k] = $v[0];
		}
	}
	$POPUP_META['snp_theme'] = isset($POPUP_META['snp_theme']) ? unserialize($POPUP_META['snp_theme']) : '';
	$POPUP_START_DATE = strtotime(isset($POPUP_META['snp_start_date']) ? $POPUP_META['snp_start_date'] : '');
	$POPUP_END_DATE = strtotime(isset($POPUP_META['snp_end_date']) ? $POPUP_META['snp_end_date'] : '');
	if ($POPUP_START_DATE)
	{
		if ($POPUP_START_DATE <= time())
		{
			
		}
		else
		{
			return;
		}
	}
	if ($POPUP_END_DATE)
	{
		if ($POPUP_END_DATE >= time())
		{
			
		}
		else
		{
			return;
		}
	}

	if ($type == 'exit')
	{
		$use_in = snp_get_option('use_in');
		if ($use_in['the_content'] == 1)
		{
			add_filter('the_content', array('snp_links', 'search'), 100);
		}
		if ($use_in['the_excerpt'] == 1)
		{
			add_filter('the_excerpt', array('snp_links', 'search'), 100);
		}
		if ($use_in['widget_text'] == 1)
		{
			add_filter('widget_text', array('snp_links', 'search'), 100);
		}
		if ($use_in['comment_text'] == 1)
		{
			add_filter('comment_text', array('snp_links', 'search'), 100);
		}
	}
	add_action('wp_footer', 'snp_footer');
	wp_register_style('snp_styles_reset', plugins_url('/themes/reset.min.css', __FILE__));
	wp_enqueue_style('snp_styles_reset');
	if (isset($POPUP_META['snp_theme']['theme']) && $POPUP_META['snp_theme']['theme'])
	{
		$THEME_INFO = snp_get_theme($POPUP_META['snp_theme']['theme']);
	}
	if (isset($THEME_INFO['STYLES']) && $THEME_INFO['STYLES'])
	{
		//wp_register_style('snp_styles_' . $POPUP_META['snp_theme']['theme'], plugins_url('/themes/' . $POPUP_META['snp_theme']['theme'] . '/' . $THEME_INFO['STYLES'] . '', __FILE__));
		wp_register_style('snp_styles_' . $POPUP_META['snp_theme']['theme'], plugins_url($POPUP_META['snp_theme']['theme'] . '/' .$THEME_INFO['STYLES'], realpath($THEME_INFO['DIR'])));
		wp_enqueue_style('snp_styles_' . $POPUP_META['snp_theme']['theme']);
	}
	if (isset($POPUP_META['snp_theme']['theme']) && function_exists('snp_enqueue_' . $POPUP_META['snp_theme']['theme']))
	{
	    call_user_func('snp_enqueue_' .$POPUP_META['snp_theme']['theme'], $POPUP_META);
	}
	if($type=='inline')
	{
	}
	elseif($type=='content')
	{
		$snp_popups[$type][] = array('ID' => $ID, 'AB_ID' => isset($AB_ID) ? $AB_ID : false);
	}
	else
	{
		$snp_popups[$type] = array('ID' => $ID, 'AB_ID' => isset($AB_ID) ? $AB_ID : false);
	}
}

function snp_create_popup($ID, $AB_ID, $type)
{
	global $PREVIEW_POPUP_META;
	$return = '';
	if ($ID == -1)
	{
		$POPUP_META = $PREVIEW_POPUP_META;
		/*foreach ($POPUP_META as $k => $v)
		{
			if (is_array($v))
			{
				$v = serialize($v);
			}
			else
			{
				$v = stripslashes($v);
			}
			$POPUP_META[$k] = $v;
		}*/
	}
	else
	{
		$POPUP = get_post($ID);
		$POPUP_META = get_post_meta($ID);
		foreach ($POPUP_META as $k => $v)
		{
			$POPUP_META[$k] = $v[0];
		}
	}
	if(!is_array($POPUP_META['snp_theme']))
	{
	    $POPUP_META['snp_theme'] = unserialize($POPUP_META['snp_theme']);
	}
	if (!$POPUP_META['snp_theme']['theme'])
	{
		return;
	}
	if ($POPUP_META['snp_theme']['type'] == 'social' || $POPUP_META['snp_theme']['type'] == 'likebox')
	{
		snp_enqueue_social_script();
	}
	$CURRENT_URL = snp_get_current_url();
	$return .='	<div id="'.snp_get_option('class_popup','snppopup') . '-' . $type . ($type=='content' || $type=='inline' || $type=='widget' ? '-'.$ID : '').'" class="snp-pop-'.$ID.' '.snp_get_option('class_popup','snppopup').($type=='inline' ? ' snp-pop-inline' : '').($type=='widget' ? ' snp-pop-widget' : '').'">';
	if (isset($POPUP_META['snp_cb_close_after']) && $POPUP_META['snp_cb_close_after'])
	{
		$return .= '<input type="hidden" class="snp_autoclose" value="' . $POPUP_META['snp_cb_close_after'] . '" />';
	}
	if (isset($POPUP_META['snp_open']) && $POPUP_META['snp_open'])
	{
		$return .= '<input type="hidden" class="snp_open" value="' . $POPUP_META['snp_open'] . '" />';
	}
	else
	{
		$return .= '<input type="hidden" class="snp_open" value="load" />';
	}
	if (isset($POPUP_META['snp_open_after']) && $POPUP_META['snp_open_after'])
	{
		$return .= '<input type="hidden" class="snp_open_after" value="' . $POPUP_META['snp_open_after'] . '" />';
	}
	if (isset($POPUP_META['snp_open_inactivity']) && $POPUP_META['snp_open_inactivity'])
	{
		$return .= '<input type="hidden" class="snp_open_inactivity" value="' . $POPUP_META['snp_open_inactivity'] . '" />';
	}
	if (isset($POPUP_META['snp_open_scroll']) && $POPUP_META['snp_open_scroll'])
	{
		$return .= '<input type="hidden" class="snp_open_scroll" value="' . $POPUP_META['snp_open_scroll'] . '" />';
	}
	if (isset($POPUP_META['snp_optin_redirect']) && $POPUP_META['snp_optin_redirect']=='yes' && !empty($POPUP_META['snp_optin_redirect_url']))
	{
		$return .= '<input type="hidden" class="snp_optin_redirect_url" value="' . $POPUP_META['snp_optin_redirect_url'] . '" />';
	}
	else
	{
		$return .= '<input type="hidden" class="snp_optin_redirect_url" value="" />';
	}
	if (!isset($POPUP_META['snp_popup_overlay']))
	{
		$POPUP_META['snp_popup_overlay'] = '';
	}
	$return .= '<input type="hidden" class="snp_show_cb_button" value="' . $POPUP_META['snp_show_cb_button'] . '" />';
	$return .= '<input type="hidden" class="snp_popup_id" value="' . $ID . '" />';
	if($AB_ID!=false)
	{
	    $return .= '<input type="hidden" class="snp_popup_ab_id" value="' . $AB_ID . '" />';
	}   
	$return .= '<input type="hidden" class="snp_popup_theme" value="' . $POPUP_META['snp_theme']['theme'] . '" />';
	$return .= '<input type="hidden" class="snp_overlay" value="' . $POPUP_META['snp_popup_overlay'] . '" />';
	$return .= '<input type="hidden" class="snp_cookie_conversion" value="' . (!empty($POPUP_META['snp_cookie_conversion']) ? $POPUP_META['snp_cookie_conversion'] : '30') . '" />';
	$return .= '<input type="hidden" class="snp_cookie_close" value="' . (!empty($POPUP_META['snp_cookie_close']) && $POPUP_META['snp_cookie_close'] ? $POPUP_META['snp_cookie_close'] : '-1') . '" />';
	$THEME_INFO = snp_get_theme($POPUP_META['snp_theme']['theme']);
	ob_start();
	include($THEME_INFO['DIR'] . '/template.php');
	$return .= ob_get_clean();
	if (!isset($POPUP_META['snp_cb_img']))
	{
		$POPUP_META['snp_cb_img'] = '';
	}
	if (!isset($POPUP_META['snp_custom_css']))
	{
		$POPUP_META['snp_custom_css'] = '';
	}
	if (!isset($POPUP_META['snp_custom_js']))
	{
		$POPUP_META['snp_custom_js'] = '';
	}
	//if ($POPUP_META['snp_overlay'] == 'disabled')
	//{
	//	$return .= '<style>.snp-pop-' . $ID . '-overlay { background: none !important;}</style>';
	//}
	if ($POPUP_META['snp_popup_overlay'] == 'image' && $POPUP_META['snp_overlay_image'])
	{
		$return .= '<style>.snp-pop-' . $ID . '-overlay { background: url(\'' . $POPUP_META['snp_overlay_image'] . '\');}</style>';
	}
	if ($POPUP_META['snp_cb_img'] != 'close_default' && $POPUP_META['snp_cb_img'] != '')
	{
		$return .= '<style>';
		switch ($POPUP_META['snp_cb_img'])
		{
			case 'close_1':
				$return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 31px; height: 31px; top: -15px; right: -15px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
				break;
			case 'close_2':
				$return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 19px; height: 19px; top: -8px; right: -8px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
				break;
			case 'close_3':
				$return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 33px; height: 33px; top: -16px; right: -16px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
				break;
			case 'close_4':
			case 'close_5':
				$return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 20px; height: 20px; top: -10px; right: -10px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
				break;
			case 'close_6':
				$return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 24px; height: 24px; top: -12px; right: -12px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
				break;
		}
		$return .= '</style>';
	}
	if ($POPUP_META['snp_custom_css'] != '')
	{
		$return .= '<style>';
		$return .= $POPUP_META['snp_custom_css'];
		$return .= '</style>';
	}
	if ($POPUP_META['snp_custom_js'] != '')
	{
		$return .= '<script>';
		$return .= $POPUP_META['snp_custom_js'];
		$return .= '</script>';
	}
	if(isset($THEME_INFO['OPEN_FUNCTION']) || isset($THEME_INFO['CLOSE_FUNCION']))
	{
		$return .= '<script>';
		$return .= 'snp_f[\''.snp_get_option('class_popup','snppopup') . '-' . $type . ($type=='content' || $type=='inline' ? '-'.$ID : '').'-open\'] ='.$THEME_INFO['OPEN_FUNCTION'].';';
		$return .= 'snp_f[\''.snp_get_option('class_popup','snppopup') . '-' . $type . ($type=='content' || $type=='inline' ? '-'.$ID : '').'-close\'] ='.$THEME_INFO['CLOSE_FUNCION'].';';
		$return .= '</script>';
	}
	$return .= '</div>';
	return $return;
}

function snp_footer()
{
	global $snp_popups, $snp_ignore_cookies, $post;
	?>
		<script>
		    var snp_f = [];
		</script>
		<div class="snp-root">
			<input type="hidden" id="snp_popup" value="" />
			<input type="hidden" id="snp_popup_id" value="" />
			<input type="hidden" id="snp_popup_theme" value="" />
			<input type="hidden" id="snp_exithref" value="" />
			<input type="hidden" id="snp_exittarget" value="" />
			<?php
			// exit popup
			if (!empty($snp_popups['exit']['ID']) && intval($snp_popups['exit']['ID']))
			{
				echo snp_create_popup($snp_popups['exit']['ID'], $snp_popups['exit']['AB_ID'], 'exit');
			}
			// welcome popup
			if (!empty($snp_popups['welcome']['ID']) && intval($snp_popups['welcome']['ID']))
			{
				echo snp_create_popup($snp_popups['welcome']['ID'], $snp_popups['welcome']['AB_ID'], 'welcome');
			}
			// popups from content
			if (isset($snp_popups['content']) && is_array($snp_popups['content']))
			{
				foreach($snp_popups['content'] as $popup_id)
				{
					echo snp_create_popup($popup_id['ID'], $popup_id['AB_ID'], 'content');
				}
			}
			?>
		</div>
		<script>
			var snp_timer;
			var snp_timer_o;
			var snp_ajax_url= '<?php echo admin_url('admin-ajax.php') ?>';
			var snp_ignore_cookies = <?php if (!$snp_ignore_cookies) { echo 'false'; } else { echo 'true'; } ?>;
			var snp_is_interal_link;
			<?php
			if (snp_get_option('enable_analytics_events')=='yes' && !is_admin())
			{
			    echo 'var snp_enable_analytics_events = true;';
			}
			else
			{
			    echo 'var snp_enable_analytics_events = false;';
			}
			if (snp_get_option('enable_mobile')=='enabled' && !is_admin())
			{
			    echo 'var snp_enable_mobile = true;';
			}
			else
			{
			    echo 'var snp_enable_mobile = false;';
			}
			?>
			jQuery(document).ready(function(){
				<?php
				if (!snp_get_option('js_disable_jq_placeholder') || is_admin())
				{
					echo "jQuery('[placeholder]').placeholder();";
				}
				?>
				jQuery(".snp_nothanks, .snp_closelink, .snp-close-link").click(function(){
					snp_close();
					return false;
				});
				jQuery(".snp_subscribeform").submit(function(){
					return snp_onsubmit(jQuery(this));
				});
				<?php
				if (!empty($snp_popups['welcome']['ID']) && intval($snp_popups['welcome']['ID']))
				{
					?>
								var snp_open=jQuery('#<?php echo snp_get_option('class_popup','snppopup') . '-welcome'; ?> .snp_open').val();
								var snp_open_after=jQuery('#<?php echo snp_get_option('class_popup','snppopup') . '-welcome'; ?> .snp_open_after').val();
								var snp_open_inactivity=jQuery('#<?php echo snp_get_option('class_popup','snppopup') . '-welcome'; ?> .snp_open_inactivity').val();
								var snp_open_scroll=jQuery('#<?php echo snp_get_option('class_popup','snppopup') . '-welcome'; ?> .snp_open_scroll').val();
								var snp_op_welcome = false;
								if(snp_open=='inactivity')
								{
								    var snp_idletime=0;
								    function snp_timerIncrement()
								    {
									snp_idletime++;
									if (snp_idletime > snp_open_inactivity)
									{
									    window.clearTimeout(snp_idleInterval);
									    snp_open_popup('','','<?php echo snp_get_option('class_popup','snppopup') . '-welcome'; ?>','welcome');
									}
								    }
								    var snp_idleInterval = setInterval(snp_timerIncrement, 1000);
								    jQuery(this).mousemove(function (e) {
									snp_idletime = 0;
								    });
								    jQuery(this).keypress(function (e) {
									snp_idletime = 0;
								    });
								}
								else if(snp_open=='scroll')
								{
								    jQuery(window).scroll(function() {
									var h = jQuery(document).height()-jQuery(window).height();
									var sp = jQuery(window).scrollTop();
									var p = parseInt(sp/h*100);
									if(p>=snp_open_scroll &&  snp_op_welcome == false){
									    snp_open_popup('','','<?php echo snp_get_option('class_popup','snppopup') . '-welcome'; ?>','welcome'); snp_op_welcome = true;
									}
								     });
								}
								else
								{
								    if(snp_open_after)
								    {
									    snp_timer_o=setTimeout("snp_open_popup('','','<?php echo snp_get_option('class_popup','snppopup') . '-welcome'; ?>','welcome');",snp_open_after*1000);	
								    }
								    else
								    {
									    snp_open_popup('','','<?php echo snp_get_option('class_popup','snppopup') . '-welcome'; ?>','welcome');
								    }
								}
					<?php
				}
				?>
			});	
			<?php
			if (isset($snp_popups['exit']['ID']) && intval($snp_popups['exit']['ID']))
			{
				?>
				var snp_hostname = new RegExp(location.host);
				var snp_http = new RegExp("^(http|https)://", "i");
				var snp_excluded_urls = [];
				<?php
				$exit_excluded_urls=snp_get_option('exit_excluded_urls');
				if(is_array($exit_excluded_urls))
				{
					foreach($exit_excluded_urls as $url)
					{
						echo "snp_excluded_urls.push('".$url."');";
					}
				}
			
				$EXIT_POPUP_META = get_post_meta($snp_popups['exit']['ID']);
				if ($EXIT_POPUP_META['snp_show_on_exit'][0] == 2)
				{
					?>
					jQuery("a").click(function(){
						if(jQuery(this).hasClass('<?php echo snp_get_option('class_popup','snppopup'); ?>'))
						{
							return snp_open_popup(jQuery(this).attr('href'),jQuery(this).attr('target'),'<?php echo snp_get_option('class_popup','snppopup') . '-exit'; ?>','exit');
						}
						else
						{
							var url = jQuery(this).attr("href");
							if(url.slice(0, 1) == "#")
							{   
							    return;
							}
							if(url.length>0 && !snp_hostname.test(url) && snp_http.test(url))
							{
								if(jQuery.inArray(url, snp_excluded_urls)==-1)
								{
									snp_is_interal_link=false;
								}
								else
								{
									// is excluded
									snp_is_interal_link=true;
								}
							}
							else
							{
								snp_is_interal_link=true;
							}
						}
					});
								jQuery(window).bind('beforeunload', function(e){
					<?php
					if (!$snp_ignore_cookies)
					{
						echo "if(jQuery.cookie('snp_" . snp_get_option('class_popup','snppopup') . "-exit')==1){return;}";
					}
					?>
									if(jQuery.fancybox2!==undefined && jQuery.fancybox2.isOpen)
									{
										return;
									}
									if(snp_is_interal_link==true)
									{
										return;
									}
									setTimeout("snp_open_popup(jQuery(this).attr('href'),jQuery(this).attr('target'),'<?php echo snp_get_option('class_popup','snppopup') . '-exit'; ?>','exit');",1000);
									//snp_open_popup(jQuery(this).attr('href'),jQuery(this).attr('target'),'<?php echo snp_get_option('class_popup','snppopup') . '-exit'; ?>','exit');
									var e = e || window.event;
									if (e) {
										e.returnValue = '<?php echo str_replace("\r\n", '\n', addslashes($EXIT_POPUP_META['snp_exit_js_alert_text'][0])); ?>';
									}
									return '<?php echo str_replace("\r\n", '\n', addslashes($EXIT_POPUP_META['snp_exit_js_alert_text'][0])); ?>';
								});	
					<?php
				}
				elseif ($EXIT_POPUP_META['snp_show_on_exit'][0] == 3)
				{
				    ?>
				    var snp_op_exit=false;
				    jQuery(document).ready(function(){
					   jQuery(document).bind('mouseleave',function(e){
					      var rightD = jQuery(window).width() - e.pageX;
					      if(snp_op_exit == false && rightD>20){ 
					        snp_open_popup(jQuery(this).attr('href'),jQuery(this).attr('target'),'<?php echo snp_get_option('class_popup','snppopup') . '-exit'; ?>','exit');
						snp_op_exit = true;
					    }
					});
				    });	
				    <?php
				}
				else
				{
				    ?>
				    jQuery(document).ready(function(){
					<?php
					$use_in = snp_get_option('use_in');
					if ($use_in['all'] == 1)
					{
					    ?>
					    jQuery("a:not(.<?php echo snp_get_option('class_popup','snppopup'); ?>)").click(function(){
						    if(jQuery(this).hasClass('<?php echo snp_get_option('class_popup','snppopup'); ?>'))
						    {
							    return snp_open_popup(jQuery(this).attr('href'),jQuery(this).attr('target'),'<?php echo snp_get_option('class_popup','snppopup') . '-exit'; ?>','exit');
						    }
						    else
						    {
							    var url = jQuery(this).attr("href");
							    if(!snp_hostname.test(url) && url.slice(0, 1) != "#" && snp_http.test(url))
							    {
								    if(jQuery.inArray(url, snp_excluded_urls)==-1)
								    {
									    return snp_open_popup(jQuery(this).attr('href'),jQuery(this).attr('target'),'<?php echo snp_get_option('class_popup','snppopup') . '-exit'; ?>','exit');
								    }
							    }
						    }
					    });    
					    <?php
					}
					?>
						jQuery("a.<?php echo snp_get_option('class_popup','snppopup'); ?>").click(function(){
							return snp_open_popup(jQuery(this).attr('href'),jQuery(this).attr('target'),'<?php echo snp_get_option('class_popup','snppopup') . '-exit'; ?>','exit');
						});
					});	
					<?php
				}
			}
			if (isset($snp_popups['content']) && is_array($snp_popups['content']))
			{
			?>
			jQuery(document).ready(function(){
				jQuery("a.<?php echo snp_get_option('class_popup','snppopup'); ?>-content, a[href^='#ninja-popup-']").click(function(){
				    var id = jQuery(this).attr('rel');
				    if(!id)
				    {
					id = jQuery(this).attr('href').replace('#ninja-popup-','');
				    }
				    if(id)
				    {
					return snp_open_popup('','','<?php echo snp_get_option('class_popup','snppopup'); ?>-content-'+id,'content');
				    }
				});
			});	
			<?php
			}
			?>
		</script>
		<?php
	}

	function snp_enqueue_social_script()
	{
		if (!snp_get_option('js_disable_fb') || is_admin())
		{
			// Facebook
			wp_enqueue_script('fbsdk', 'https://connect.facebook.net/'.snp_get_option('fb_locale','en_GB').'/all.js#xfbml=1', array());
			wp_localize_script('fbsdk', 'fbsdku', array(
				'xfbml' => 1,
			));
		}
		if (!snp_get_option('js_disable_gp') || is_admin())
		{
			// Google Plus
			wp_enqueue_script('plusone', 'https://apis.google.com/js/plusone.js', array());
		}
		if (!snp_get_option('js_disable_tw') || is_admin())
		{
			// Twitter
			wp_enqueue_script('twitter', 'https://platform.twitter.com/widgets.js', array());
		}
		if (!snp_get_option('js_disable_li') || is_admin())
		{
			// Linkedin
			wp_enqueue_script('linkedin', 'http://platform.linkedin.com/in.js', array());
		}
		//if (!snp_get_option('js_disable_pi') || is_admin())
		//{
		// Pinterest
		//wp_enqueue_script('pinterest', 'https://assets.pinterest.com/js/pinit.js', array());
		//}
	}
	function snp_ninja_popup_shortcode($attr, $content = null)
	{
		extract(shortcode_atts(array('id' => '', 'autoopen' => false), $attr));
		snp_run_popup($id, 'content');
		if(isset($autoopen) && $autoopen==true)
		{
			?>
			<script type="text/javascript">
			jQuery(document).ready(function(){
				var snp_open_after=jQuery('#<?php echo snp_get_option('class_popup','snppopup') . '-content-'.$id; ?> .snp_open_after').val();
				if(snp_open_after)
				{
					snp_timer_o=setTimeout("snp_open_popup('','','<?php echo snp_get_option('class_popup','snppopup') . '-content-'.$id; ?>','content');",snp_open_after*1000);	
				}
				else
				{
					snp_open_popup('','','<?php echo snp_get_option('class_popup','snppopup') . '-content-'.$id; ?>','content');
				}		
			});
			</script>
			<?php
		}
		if($content)
		{
			return '<a href="#ninja-popup-'.$id.'" class="'.snp_get_option('class_popup','snppopup').'-content" rel="'.$id.'">'.  $content.' </a>';
		}
		return '';
	}
	add_shortcode( 'ninja-popup', 'snp_ninja_popup_shortcode' );
	
	function snp_detect_shortcode($shortcode)
	{
		global $post;
		$pattern = get_shortcode_regex();
		preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches );
		if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( $shortcode, $matches[2] ) )
		{
			$IDs=array();
			foreach($matches[2] as $k => $v)
			{
				if($v==$shortcode)
				{
					$t_atts=shortcode_parse_atts( $matches[3][$k] );
					$IDs[]=$t_atts['id'];
				}
			}
			return $IDs;
		}
		else
		{
			return array();
		}
	}
	function snp_run()
	{
		global $post;
		if (is_404())
		{
			return;
		}
		if (snp_get_option('enable') == 'disabled')
		{
			return;
		}
		// mobile device?
		//if (snp_get_option('enable_mobile') == 'disabled' && snp_detect_mobile($_SERVER['HTTP_USER_AGENT']))
		//{
			//return;
		//}
		if((isset($_REQUEST['nphide']) && $_REQUEST['nphide']==1) || isset($_COOKIE['nphide']) && $_COOKIE['nphide']==1)
		{
		    setcookie('nphide',1,0,COOKIEPATH, COOKIE_DOMAIN, false);
		    return;
		}
		$WELCOME_ID = 'global';
		$EXIT_ID = 'global';		
		if (isset($post->ID) && (is_page() || is_single()))
		{
			$WELCOME_ID = get_post_meta($post->ID, 'snp_p_welcome_popup', true);
			$WELCOME_ID = ($WELCOME_ID ? $WELCOME_ID : 'global');
			$EXIT_ID = get_post_meta($post->ID, 'snp_p_exit_popup', true);
			$EXIT_ID = ($EXIT_ID ? $EXIT_ID : 'global');
			//echo 'post='.$WELCOME_ID.'<br />'; //DEL
			//echo 'post='.$EXIT_ID.'<br />'; //DEL
			if($WELCOME_ID=='global' || $EXIT_ID=='global')
			{
			    if($post->post_type == 'post')
			    {
				$POST_CATS = wp_get_post_categories($post->ID);
			    }
			    $enable_taxs=snp_get_option('enable_taxs');
			    if(is_array($enable_taxs))
			    {
				foreach((array)$enable_taxs as $k => $v)
				{
				    $POST_CATS = array_merge((array)$POST_CATS,wp_get_object_terms($post->ID, $k, array('fields' => 'ids')));
				}
			    }
			    if(is_array($POST_CATS))
			    {
				foreach($POST_CATS as $term_id)
				{
				    $term_meta = get_option("snp_taxonomy_" . $term_id);
				    if(isset($term_meta['welcome']) && $WELCOME_ID=='global')
				    {
					$WELCOME_ID = $term_meta['welcome'];
				    }
				    if(isset($term_meta['exit']) && $EXIT_ID=='global')
				    {
					$EXIT_ID = $term_meta['exit'];
				    }
				}
			    }
			    // tax
			}
		}
		elseif(is_category() || is_tax() || is_tag() || is_archive())
		{
		    $category = get_queried_object();
		    $term_meta = get_option("snp_taxonomy_" . $category->term_id);
		    if(isset($term_meta['welcome']))
		    {
			$WELCOME_ID = $term_meta['welcome'];
		    }
		    else
		    {
			$WELCOME_ID = 'global';
		    }
		    if(isset($term_meta['exit']))
		    {
			$EXIT_ID = $term_meta['exit'];
		    }
		    else
		    {
			$EXIT_ID = 'global';
		    }
		}
		if (defined('ICL_LANGUAGE_CODE'))
		{
		    $snp_var_sufix = '_'.ICL_LANGUAGE_CODE;
		}
		else
		{
		    $snp_var_sufix = '';
		}
		// WELCOME
		if (snp_get_option('welcome_disable_for_logged')==1 && is_user_logged_in())
		{
		}
		else
		{
		    if ($WELCOME_ID !== 'disabled'  && $WELCOME_ID !== 'global')
		    {
		        snp_run_popup($WELCOME_ID, 'welcome');
		    }
		    elseif ($WELCOME_ID === 'global')
		    {
			$WELCOME_ID = snp_get_option('welcome_popup'.$snp_var_sufix);
			if ($WELCOME_ID === 'global' && defined('ICL_LANGUAGE_CODE'))
			{
			    $WELCOME_ID = snp_get_option('welcome_popup');
			}
			if($WELCOME_ID !== 'disabled')
			{
			    $welcome_display_in = snp_get_option('welcome_display_in');
			    if (is_front_page() && isset($welcome_display_in['home']) &&  $welcome_display_in['home'] == 1)//home
			    {
				snp_run_popup($WELCOME_ID, 'welcome');
			    }
			    elseif (is_page() && isset($welcome_display_in['pages']) && $welcome_display_in['pages'] == 1) //page
			    {
				snp_run_popup($WELCOME_ID, 'welcome');
			    }
			    elseif (is_single() && isset($welcome_display_in['posts']) && $welcome_display_in['posts'] == 1) //post
			    {
				snp_run_popup($WELCOME_ID, 'welcome');
			    }
			    elseif (isset($welcome_display_in['others']) &&$welcome_display_in['others'] == 1 && !is_front_page() && !is_page() && !is_single())// other
			    {
				snp_run_popup($WELCOME_ID, 'welcome');
			    }
			}
		    }
		}
		// EXIT
		if (snp_get_option('exit_disable_for_logged')==1 && is_user_logged_in())
		{
		}
		else
		{
		    if ($EXIT_ID != 'disabled'  && $EXIT_ID != 'global')
		    {
		        snp_run_popup($EXIT_ID, 'exit');
		    }
		    elseif ($EXIT_ID === 'global')
		    {
			$EXIT_ID = snp_get_option('exit_popup'.$snp_var_sufix);
			if ($EXIT_ID === 'global' && defined('ICL_LANGUAGE_CODE'))
			{
			    $EXIT_ID = snp_get_option('exit_popup');
			}
			if($EXIT_ID != 'disabled')
			{
			    $exit_display_in = snp_get_option('exit_display_in');
			    if (is_front_page() && isset($exit_display_in['home']) &&  $exit_display_in['home'] == 1)//home
			    {
				snp_run_popup($EXIT_ID, 'exit');
			    }
			    elseif (is_page() && isset($exit_display_in['pages']) && $exit_display_in['pages'] == 1) //page
			    {
				snp_run_popup($EXIT_ID, 'exit');
			    }
			    elseif (is_single() && isset($exit_display_in['posts']) && $exit_display_in['posts'] == 1) //post
			    {
				snp_run_popup($EXIT_ID, 'exit');
			    }
			    elseif (isset($exit_display_in['others']) && $exit_display_in['others'] == 1 && !is_front_page() && !is_page() && !is_single())// other
			    {
				snp_run_popup($EXIT_ID, 'exit');
			    }
			}
		    }		    
		}
		// ===============================
		add_filter( 'wp_nav_menu_objects', 'snp_wp_nav_menu_objects' );
	}
	function snp_wp_nav_menu_objects( $items ) 
	{
	    $parents = array();
	    foreach ( $items as $item ) 
	    {
		    if(strpos($item->url,'#ninja-popup-')!==FALSE)
		    {
			$ID=str_replace('#ninja-popup-', '', $item->url);
			if(intval($ID))
			{
			    snp_run_popup(intval($ID), 'content');
			}
		    }
	    }
	    return $items;    
	}

	function snp_setup()
	{
		register_post_type('snp_popups', array(
			'label' => 'Ninja Popups',
			'labels' => array(
				'name' => 'Ninja Popups',
				'menu_name' => 'Ninja Popups',
				'singular_name' => 'Popup',
				'add_new' => 'Add New Popup',
				'all_items' => 'Popups',
				'add_new_item' => 'Add New Popup',
				'edit_item' => 'Edit Popup',
				'new_item' => 'New Popup',
				'view_item' => 'View Popup',
				'search_items' => 'Search Popups',
				'not_found' => 'No popups found',
				'not_found_in_trash' => 'No popups found in Trash'
			),
			'hierarchical' => false,
			'public' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
			'show_in_admin_bar' => false,
			'show_in_menu' => true,
			'capability_type' => 'page',
			'supports' => array('title', 'editor'),
			'menu_position' => 207,
		    	'menu_icon'=> ''
		));
		register_post_type('snp_ab', array(
			'label' => 'A/B Testing',
			'labels' => array(
				'name' => 'A/B Testing',
				'menu_name' => 'A/B Testing',
				'singular_name' => 'A/B Testing',
				'add_new' => 'Add New',
				'all_items' => 'A/B Testing',
				'add_new_item' => 'Add New',
				'edit_item' => 'Edit',
				'new_item' => 'New',
				'view_item' => 'View',
				'search_items' => 'Search',
				'not_found' => 'Not found',
				'not_found_in_trash' => 'Not found in Trash'
			),
			'hierarchical' => false,
			'public' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
			'show_in_admin_bar' => false,
			'show_in_menu' => 'edit.php?post_type=snp_popups',
			'capability_type' => 'page',
			'supports' => array('title')
		));
		
		add_action('wp_ajax_nopriv_snp_popup_stats', 'snp_popup_stats');
		add_action('wp_ajax_snp_popup_stats', 'snp_popup_stats');
		add_action('wp_ajax_nopriv_snp_popup_submit', 'snp_popup_submit');
		add_action('wp_ajax_snp_popup_submit', 'snp_popup_submit');
		wp_enqueue_script('jquery');
	}

	add_action('init', 'snp_setup', 15);
	if(is_admin())
	{
	    add_action('init', 'snp_setup_admin', 15);
	}
	if (!is_admin())
	{
		if(snp_get_option('run_hook')=='wp')
		{
		    add_action('wp', 'snp_run');
		}
		else
		{
			add_action('get_header', 'snp_run');    
		}
	}

?>