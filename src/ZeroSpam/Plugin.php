<?php
class ZeroSpam_Plugin implements ArrayAccess {
  protected $contents;
  public $settings = array();

  public $default_settings =  array(
    'spammer_msg_comment'         => 'There was a problem processing your comment.',
    'spammer_msg_registration'    => '<strong>ERROR</strong>: There was a problem processing your registration.',
    'spammer_msg_contact_form_7'  => 'There was a problem processing your comment.',
    'spammer_msg_bp'              => 'There was a problem processing your registration.',
    'spammer_msg_nf'              => 'There was a problem processing your submission.',
    'blocked_ip_msg'              => 'Access denied.'
  );

  public function __construct() {
    $this->contents = array();

    $this->load_settings();
  }

  /**
   * Add setting link to plugin.
   *
   * Applied to the list of links to display on the plugins page (beside the activate/deactivate links).
   *
   * @since 2.0.0
   *
   * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
   */
  public function plugin_action_links( $links ) {
    $link = array( '<a href="' . zerospam_admin_url() . '?page=zerospam">' . __( 'Settings', 'zerospam' ) . '</a>' );

    return array_merge( $links, $link );
  }

  public function load_settings() {
    // Merge and update new changes
    if ( isset( $_POST['zerospam_general_settings'] ) ) {
      $saved_settings = $_POST['zerospam_general_settings'];
      if ( is_plugin_active_for_network( plugin_basename( ZEROSPAM_PLUGIN ) ) ) {
        update_site_option( 'zerospam_general_settings', $saved_settings );
      } else {
        update_option( 'zerospam_general_settings', $saved_settings );
      }
    }

    // Retrieve the settings
    $saved_settings = zerospam_settings();

    $this->settings = array_merge(
      $this->default_settings,
      $saved_settings
    );
  }

  /**
   * Plugin meta links.
   *
   * Adds links to the plugins meta.
   *
   * @since 2.0.0
   *
   * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/preprocess_comment
   */
  public function plugin_row_meta( $links, $file ) {
    if ( false !== strpos( $file, 'zero-spam.php' ) ) {
      $links = array_merge( $links, array( '<a href="https://benmarshall.me/wordpress-zero-spam-plugin/">WordPress Zero Spam</a>' ) );
      $links = array_merge( $links, array( '<a href="https://www.gittip.com/bmarshall511/">Donate</a>' ) );
    }
    return $links;
  }

  public function offsetSet( $offset, $value ) {
    $this->contents[$offset] = $value;
  }

  public function offsetExists($offset) {
    return isset( $this->contents[$offset] );
  }

  public function offsetUnset($offset) {
    unset( $this->contents[$offset] );
  }

  public function offsetGet($offset) {
    if( is_callable($this->contents[$offset]) ){
      return call_user_func( $this->contents[$offset], $this );
    }
    return isset( $this->contents[$offset] ) ? $this->contents[$offset] : null;
  }

  public function run() {
    foreach( $this->contents as $key => $content ){ // Loop on contents
      if( is_callable($content) ){
        $content = $this[$key];
      }
      if( is_object( $content ) ){
        $reflection = new ReflectionClass( $content );
        if( $reflection->hasMethod( 'run' ) ){
          $content->run(); // Call run method on object
        }
      }
    }

    add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

    if ( is_plugin_active_for_network( plugin_basename( ZEROSPAM_PLUGIN ) ) ) {
      add_filter( 'network_admin_plugin_action_links_' . plugin_basename( ZEROSPAM_PLUGIN ), array( $this, 'plugin_action_links' ) );
    } else {
      add_filter( 'plugin_action_links_' . plugin_basename( ZEROSPAM_PLUGIN ), array( $this, 'plugin_action_links' ) );
    }
  }
}