<?php
$defflip = (!cfip()) ? exit(header('HTTP/1.1 401 Unauthorized')) : 1;

// check if daemon can connect -> error
try {
  if ($bitcoin->can_connect() !== true) {
    $newerror = array();
    $newerror['name'] = "Coin daemon";
    $newerror['level'] = 3;
    $newerror['description'] = "Unable to connect to coin daemon using provided credentials.";
    $newerror['configvalue'] = "wallet.*";
    $newerror['extdesc'] = "We weren't able to connect to your coin daemon using the host/username/password/port given in the config. Check that your coin daemon is running and mpos is configured with the data from your coin daemon config.";
    $newerror['helplink'] = "https://github.com/MPOS/php-mpos/wiki/Config-Setup#wiki-local-wallet-rpc";
    $error[] = $newerror;
    $newerror = null;
  }
  else {
    // validate that the wallet service is not in test mode
    if ($bitcoin->is_testnet() == true) {
      $newerror = array();
      $newerror['name'] = "Coin daemon";
      $newerror['level'] = 3;
      $newerror['extdesc'] = "You may have accidentally mistyped the port, or are running the coin daemon in testnet mode. Check your coin daemon config and MPOS config.";
      $newerror['description'] = "The coin daemon service is running as a testnet. Check the TESTNET setting in your coin daemon config and make sure the correct port is set in the MPOS config.";
      $newerror['configvalue'] = "wallet.host";
      $newerror['helplink'] = "https://github.com/MPOS/php-mpos/wiki/Config-Setup#wiki-local-wallet-rpc";
      $error[] = $newerror;
      $newerror = null;
    }
    // check if there is more than one account set on wallet
    $accounts = $bitcoin->listaccounts();
    if (count($accounts) > 1 && $accounts[''] <= 0) {
      $newerror = array();
      $newerror['name'] = "Coin daemon";
      $newerror['level'] = 3;
      $newerror['extdesc'] = "You need at least one account to be able to pay miners! See the above link for more details.";
      $newerror['description'] = "There are " . count($accounts) . " Accounts set in local Wallet and Default Account has no liquid funds to pay your miners!";
      $newerror['configvalue'] = "wallet.host";
      $newerror['helplink'] = "https://github.com/MPOS/php-mpos/wiki/Config-Setup#wiki-local-wallet-rpc";
      $error[] = $newerror;
      $newerror = null;
    }
  }
} catch (Exception $e) {}
