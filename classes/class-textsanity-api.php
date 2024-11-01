<?php
defined('ABSPATH') || exit;

class TextSanity_API {
    public $key = 'txsy';
    public $key_ = 'txsy_';

    public $access_token = '';
    public $refresh_token = '';

    public $refreshing = false;

    // Base URL for API.
    public $base_url = 'https://app.textsanity.com';

    // The class constructor.
    public function __construct() {
        $this->access_token = get_option($this->key_ . 'access_token');
        $this->refresh_token = get_option($this->key_ . 'refresh_token');

        add_action('wp_ajax_' . $this->key_ . 'oauth_redirect', [$this, 'ajaxOAuthRedirect']);
    }

    // Handle the OAuth return call.
    public function ajaxOAuthRedirect() {
        $pass = true;
        if(!isset($_GET['code'])) {
            $pass = false;
        }

        if($pass) {
            $code = sanitize_text_field($_GET['code']);

            $data = $this->getTokens($code);

            if(isset($data['access_token']) && isset($data['refresh_token'])) {
                update_option($this->key_ . 'access_token', $data['access_token']);
                update_option($this->key_ . 'refresh_token', $data['refresh_token']);
            } else {
                $pass = false;
            }
        }  

        if($pass) {
            wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'connection'));
            exit;
        } else {
            wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'connection' . '&msg=fail'));
            exit;
        }
    }

    // Base method to make API calls.
    public function api($method, $url, $data = []) {
        $headers = [];
        // $data is set to false when Authorization header is not needed.
        if($data !== false) {
            $headers['Authorization'] = 'Bearer ' . $this->access_token;
            $headers['Content-Type'] = 'application/json';
            $headers['Accept'] = 'application/json';
        }

        $args = [];
        $args['timeout'] = 10;
        $args['headers'] = $headers;
        if($method == 'DELETE') {
            $args['method'] = 'DELETE';
        } elseif($method == 'POST') {
            $args['method'] = 'POST';
            $args['body'] = json_encode($data);
        } elseif($method == 'PUT') {
            $args['method'] = 'PUT';
            $args['body'] = json_encode($data);
        } else {
            $args['method'] = 'GET';
        }
        $res = wp_remote_request($url, $args);

        $status_code = wp_remote_retrieve_response_code($res);
        $response = wp_remote_retrieve_body($res);

        if($status_code == 401) {
            if(!$this->refreshing) {
                return $this->refresh($method, $url, $data);
            } else {
                return false;
            }
        } elseif($status_code == 404) {
            return json_decode($response, true);
        } elseif($status_code == 422) {
            return json_decode($response, true);
        } else {
            return json_decode($response, true);
        }
    }

    // Get a list of keywords from the account.
    public function getKeywords() {
        $url = $this->base_url . '/api/campaigns/readAll';

        $response = $this->api('GET', $url);

        return $response['data'];
    }

    // Get the OAuth URL for so that users can connect their account.
    public function getOAuthURL() {
        return 'https://app.textsanity.com/oauth/wordpress?redirect=' . urlencode($this->getRedirectURL());
    }

    // Get the redirect URL that is passed to the OAuth URL.
    public function getRedirectURL() {
        return admin_url('admin-ajax.php?action=txsy_oauth_redirect');
    }

    // Get a list of tags.
    public function getTags() {
        $url = $this->base_url . '/api/groups/read';

        $response = $this->api('GET', $url);

        if(isset($response['data'])) {
            return $response['data'];
        } else {
            return false;
        }
    }

    // Get the access and refresh tokens using the he OAuth redirect code.
    public function getTokens($code) {
        $url = $this->base_url . '/oauth/wordpressToken';
        $url .= '?redirect=' . urlencode($this->getRedirectURL());
        $url .= '&code=' . $code;

        $response = $this->api('GET', $url, false);

        return $response;
    }

    // Start a campaign for a phone number.
    public function initiateCampaign($phone, $keyword) {
        $url = $this->base_url . '/api/utilities/initiateCampaign';

        $data = [];
        $data['phone'] = $phone;
        $data['keyword'] = $keyword;

        $response = $this->api('POST', $url, $data);

        if($response['success']) {
            return true;
        } else {
            return false;
        }
    }

    // Is the account properly connected.
    public function isConnected() {
        if($this->access_token && $this->refresh_token) {
            $tags = $this->getTags();
            if($tags === false) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    // A user is opting in.
    public function optInMessage($phone, $tag = '') {
        $url = $this->base_url . '/api/utilities/optInMessage';

        $data = [];
        $data['phone'] = $phone;
        if($tag) {
            $data['tag'] = $tag;
        }

        $response = $this->api('POST', $url, $data);

        if($response['success']) {
            return true;
        } else {
            return false;
        }
    }

    // Handle refreshing tokens. Currently, tokens are set to expire in 365 days.
    public function refresh($original_method, $original_url, $original_data = []) {
            $this->refreshing = true;

            $url = $this->base_url . '/oauth/wordpressRefresh?redirect=' . urlencode($this->getRedirectURL());
            $url .= '&refresh_token=' . $this->refresh_token;
            $response = $this->api('GET', $url, false);

            if(isset($response['access_token']) && isset($response['refresh_token'])) {
                update_option($this->key_ . 'access_token', $response['access_token']);
                update_option($this->key_ . 'refresh_token', $response['refresh_token']);
                $this->access_token = $response['access_token'];
                $this->refresh_token = $response['refresh_token'];

                return $this->api($original_method, $original_url, $original_data);
            } else {
                return $response;
            }
    }

    // Send a message.
    public function sendMessage($phone, $message, $tag = '') {
        $url = $this->base_url . '/api/utilities/sendMessage';

        $data = [];
        $data['phone'] = $phone;
        $data['message'] = $message;
        if($tag) {
            $data['tag'] = $tag;
        }

        $response = $this->api('POST', $url, $data);

        if($response['success']) {
            return true;
        } else {
            return false;
        }
    }
}

