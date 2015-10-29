<?php

namespace App\Services;

/**
*
*/
class GoogleApi
{
    private $_client          = null;
    private $_credentialsPath = null;
    private $_secretPath      = null;
    private $_redirectUrl     = null;
    private $_appName         = null;

    public function __construct($params)
    {
        $this->_credentialsPath = $params['credentialsPath'];
        $this->_secretPath      = $params['secretPath'];
        $this->_appName         = $params['appName'];
        $this->_redirectUrl     = $params['redirectUrl'];
        $this->_client          = $this->_getClient();
    }

    /**
    * Returns an authorized API client.
    * @return Google_Client the authorized client object
    */
    public function getApiClient() {
        // Load previously authorized credentials from a file.

        if (file_exists($this->_credentialsPath)) {
            $accessToken = file_get_contents($this->_credentialsPath);
        } else {
            // Request authorization from the user.
            $authUrl = $this->_client->createAuthUrl();
            return ['url' => $authUrl];
        }
        $this->_client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($this->_client->isAccessTokenExpired()) {
            $this->_client->refreshToken($this->_client->getRefreshToken());
            file_put_contents($this->_credentialsPath, $this->_client->getAccessToken());
        }
        return ['client' => $this->_client];
    }

    public function authenticate($code)
    {
      try {
          $accessToken = $this->_client->authenticate($code);
          error_log(print_r($accessToken, true));
          // Store the credentials to disk.
          if(!file_exists($this->_credentialsPath)) {
              mkdir(dirname($this->_credentialsPath), 0777, true);
          }
          file_put_contents($this->_credentialsPath, $accessToken);
          return true;
      } catch (Exception $e) {
          return false;
      }
    }

    private function _getClient()
    {
        if (!$this->_client) {
            $this->_client = new \Google_Client();
            $this->_client->setApplicationName($this->_appName);
            $this->_client->setScopes(\Google_Service_Calendar::CALENDAR);
            $this->_client->setAuthConfigFile($this->_secretPath);
            $this->_client->setAccessType('offline');
            $this->_client->setApprovalPrompt('force');
            $this->_client->setRedirectUri($this->_redirectUrl);
        }

        return $this->_client;
    }
}
