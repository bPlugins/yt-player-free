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

        // The AJAX dispatcher always hands this raw $_POST data, so a JSON-encoded
        // preset string arrives slashed (quotes escaped as \") and must be
        // unslashed and decoded into a real array before maybe_serialize() can
        // store it the same way the natively-seeded Default preset is stored -
        // otherwise it's saved as inert escaped text that never unserializes
        // back into structured data (every field inside it, e.g. hideYoutubeUI,
        // then silently fails to apply).
        $preset = $args['preset'] ?? null;
        if ( is_string( $preset ) ) {
            $decoded = json_decode( wp_unslash( $preset ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                $preset = $decoded;
            }
        }
        $args['preset'] = maybe_serialize( $preset );

        if(!isset($args['id'])){
            $wpdb->insert($table_name, $args); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            return $wpdb->insert_id;
        }else {
            return $wpdb->update($table_name, $args, ['id' => $args['id']]); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        }
        return false;
    }

    public function get($id){
        // The AJAX dispatcher calls every model method with the full params
        // array (e.g. ['id' => '6']), not a bare scalar, so without this an
        // array falls through to intval() below and resolves to 1 every time -
        // silently always returning the first/default preset regardless of
        // which id was actually requested.
        if ( is_array( $id ) ) {
            $id = $id['id'] ?? null;
        }

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

       // Presets saved before the createOrUpdate() decode fix were stored as a
       // raw (slashed) JSON string rather than a proper serialized array, so
       // maybe_unserialize() above leaves them as plain text. Fall back to
       // decoding that text here so already-saved presets keep working instead
       // of silently losing every option (hideYoutubeUI, controls, etc.).
       if ( isset( $tempItem['preset'] ) && is_string( $tempItem['preset'] ) ) {
           $decoded = json_decode( $tempItem['preset'], true );
           if ( json_last_error() === JSON_ERROR_NONE ) {
               $tempItem['preset'] = $decoded;
           }
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
        if(!current_user_can('manage_options')){
            return new \WP_Error('forbidden', 'permission denied');
        }

        global $wpdb;
        $table_name =  $wpdb->prefix.$this->table_name;
        $data = $wpdb->get_results("SELECT * FROM $table_name", 'ARRAY_A'); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        foreach($data as $index => $item){
            foreach($item as $key => $value ){
                $data[$index][$key] = maybe_unserialize( $data[$index][$key] );
            }

            // See get(): presets saved before the createOrUpdate() decode fix
            // were stored as raw JSON text, not a serialized array.
            if ( isset( $data[$index]['preset'] ) && is_string( $data[$index]['preset'] ) ) {
                $decoded = json_decode( $data[$index]['preset'], true );
                if ( json_last_error() === JSON_ERROR_NONE ) {
                    $data[$index]['preset'] = $decoded;
                }
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