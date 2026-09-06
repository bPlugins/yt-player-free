<?php
namespace YTP\Model; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

class Presets {

    protected $table_name = 'yt_player_presets';

    public function createOrUpdate($args){
        if(!current_user_can('manage_options')){
            return new \WP_Error('forbidden', 'permission denied');
        }

        global $wpdb;
        $table_name = $wpdb->prefix.$this->table_name;

        $args = array_intersect_key($args, array_flip(['id', 'name', 'preset']));

        $args['preset'] = maybe_serialize( $args['preset']);

        if(!isset($args['id'])){
            $wpdb->insert($table_name, $args); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            return $wpdb->insert_id;
        }else {
            return $wpdb->update($table_name, $args, ['id' => $args['id']]); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        }
        return false;
    }

    public function get($id){
        global $wpdb;
        $table_name = $wpdb->prefix.$this->table_name;
        $result = $wpdb->get_row($wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", intval($id))); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

        if (!$result && !empty($id)) {
            $result = $wpdb->get_row($wpdb->prepare( "SELECT * FROM $table_name WHERE name = %s", $id)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        }

        if (!$result) {
            $result = $wpdb->get_row("SELECT * FROM $table_name ORDER BY id ASC LIMIT 1"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        }

        if(!$result){
            return [];
        }
        
        $tempItem = [];
       foreach($result as $key => $value){
        $tempItem[$key] = maybe_unserialize( $value );
       }
       return $tempItem;
    }

    public function getPreset($id){
        $preset =  $this->get($id);
        return $preset['preset'] ?? [];
    }

    function deletePreset($args){
        if(!current_user_can('manage_options')){
            return new \WP_Error('forbidden', 'permission denied');
        }

        global $wpdb;
        $table_name = $wpdb->prefix.$this->table_name;
        return $wpdb->delete($table_name, ['id' => $args['id']]); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    }

    function fetchPresets(){
        global $wpdb;
        $table_name =  $wpdb->prefix.$this->table_name;
        $data = $wpdb->get_results("SELECT * FROM $table_name", 'ARRAY_A'); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        foreach($data as $index => $item){
            foreach($item as $key => $value ){
                $data[$index][$key] = maybe_unserialize( $data[$index][$key] );
            }
        }
        return $data;
    }

    function get_client_ip() {
        $ipaddress = '';
        if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else if(getenv('SERVER_ADDR'))
            $ipaddress = getenv('SERVER_ADDR');
        else if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}