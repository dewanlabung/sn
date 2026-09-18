<?php

/**
 * ajax -> admin -> license
 * 
 * @package Sngine
 * @author Zamblek
 */

// set execution time
set_time_limit(0); /* unlimited max execution time */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// check admin|moderator permission
if (!$user->_is_admin) {
  modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
}

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

// handle license
try {

  if (!defined('LICENCE_KEY') || LICENCE_KEY === '') {
    throw new Exception(__("No license key found."));
  }

  /* re-verify and store the signed payload */
  $fresh = get_licence_key(LICENCE_KEY, 'admin');
  licence_store_payload($fresh);

  if (empty($fresh['entitlements']['monetization'])) {
    throw new Exception(__("Payments & Monetization require an Extended or Subscription license."));
  }

  $zip = licence_download_module('monetization');
  $result = licence_install_module_zip($zip);

  if (empty($result['extracted'])) {
    modal("INFO", __("Payments & monetization"), $result['message']);
  }

  return_json([
    'callback' => "modal('#modal-success', {title: '" . addslashes(__("Payments & monetization")) . "', message: '" . addslashes($result['message']) . "'}); setTimeout(function(){ window.location.reload(); }, 1200);"
  ]);
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
