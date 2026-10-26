<?php 
namespace YTP\Model; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

use YTP\Helper\Import;

class Ajax{


    protected static $_instance = null;
    private $params = [];
    private $requestType; 
    private $requestMethod;
    private $requestModel;
    private $namespace = 'YTP\Model\\';
    private $model;

    public function register(){
        add_action('wp_ajax_ytp_import_data', [$this, 'ytp_import_data']);

        add_action('wp_ajax_ytp_ajax', [$this, 'prepareAjax']);
    }

    public static function instance(){
        if(!self::$_instance){
            self::$_instance = new self();
        }
        return self::$_instance;
    }   

    public function isset($array, $key, $default = false){
        if(isset($array[$key])){
            return $array[$key];
        }
        return $default;
    }

    public function prepareAjax(){
        // wp_send_json_success('success');
        if(isset($_GET['nonce'])){ // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $this->params = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $this->requestType = 'POST';
        } else {
            $this->params = $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $this->requestType = 'GET';
        }
        echo wp_kses_post(wp_json_encode($this->proceedRequest()));
        die();
    }

    public function proceedRequest(){
        $data = $this->params;
        $nonce = $this->isset($data,'nonce');

        if(!wp_verify_nonce( $nonce, 'wp_ajax' )){
            return new \WP_Error('invalid', 'invalid nonce');
        }

        if(!current_user_can('edit_posts')){
            return new \WP_Error('forbidden', 'permission denied');
        }

        $this->requestModel = $this->isset($data, 'model', 'Model');
        $this->requestMethod = $this->isset($data, 'method', 'invalid');
        $this->model = $this->namespace.$this->requestModel;

        // Presets store/expose site-wide configuration. Gate every action on this
        // model here at the dispatcher, not inside individual methods, so a future
        // read/write method can't accidentally be added without the same check.
        $adminOnlyModels = array('Presets');
        if (in_array($this->requestModel, $adminOnlyModels, true) && !current_user_can('manage_options')) {
            return new \WP_Error('forbidden', 'permission denied');
        }

        unset($this->params['method']);
        unset($this->params['action']);
        unset($this->params['nonce']);
        unset($this->params['model']);
        
        if(method_exists($this, $this->requestMethod)){
            return new \WP_REST_Response($this->{$this->requestMethod}($this->params), 200);
        }
        
        if(!class_exists($this->namespace.$this->requestModel)){
            return new \WP_Error('invalid', $this->namespace.$this->requestModel);
            return $this->invalid();
        }

        $model = new $this->model();

        if(method_exists($model, $this->requestMethod)){
            return new \WP_REST_Response($model->{$this->requestMethod}($this->params), 200);
        }else {
            return new \WP_Error('invalid', 'Invalid Request');
            return false;
        }
    }

    public function invalid(){
       return new \WP_REST_Response('invalid request', 400);
    }

    public function savePreset(){
        global $wpdb;
        return $this->params['preset'];

    }

    public function ytp_import_data(){
        check_ajax_referer('ytp-nonce', 'nonce');

        if(!current_user_can('manage_options')){
            wp_send_json_error(['message' => 'permission denied'], 403);
        }

        Import::meta();
        Import::option();
        wp_send_json_success();
    }
}