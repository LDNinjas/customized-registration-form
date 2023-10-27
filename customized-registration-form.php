<?php
/**
 * Plugin Name: Customized Registration Form
 * Version: 1.0
 * Description: This will add password field to the registration form
 * Author: LDninjas
 * Author URI: https://ldninjas.com
 * Plugin URI: https://ldninjas.com
 * Text Domain: password-field
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Customized_Registration_Form
 */
class Customized_Registration_Form {

    const VERSION = '1.0';

    /**
     * @var self
     */
    private static $instance = null;

    /**
     * @since 1.0
     * @return $this
     */
    public static function instance() {

        if ( is_null( self::$instance ) && ! ( self::$instance instanceof Customized_Registration_Form ) ) {
            self::$instance = new self;

            self::$instance->setup_constants();
            self::$instance->includes();
            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Upgrade function hook
     *
     * @since 1.0
     * @return void
     */
    public function upgrade() {
        if( get_option ( 'crf_version' ) != self::VERSION ) {
        }
    }

    /**
     * defining constants for plugin
     */
    public function setup_constants() {

        /**
         * Directory
         */
        define( 'CRF_DIR', plugin_dir_path ( __FILE__ ) );
        define( 'CRF_DIR_FILE', CRF_DIR . basename ( __FILE__ ) );
        define( 'CRF_INCLUDES_DIR', trailingslashit ( CRF_DIR . 'includes' ) );
        define( 'CRF_TEMPLATES_DIR', trailingslashit ( CRF_DIR . 'templates' ) );
        define( 'CRF_BASE_DIR', plugin_basename(__FILE__));

        /**
         * URLs
         */
        define( 'CRF_URL', trailingslashit ( plugins_url ( '', __FILE__ ) ) );
        define( 'CRF_ASSETS_URL', trailingslashit ( CRF_URL . 'assets' ) );

        /**
         * Text Domain
         */
        define( 'CRF_TEXT_DOMAIN', 'password-field');
    }

    /**
     * Plugin Hooks
     */
    public function hooks() {
        add_action( 'user_register', [ $this, 'save_user_password' ] );
        add_filter( 'registration_errors', [ $this, 'validate_pass_register_field' ], 10, 3 );
        add_action( 'register_form', [ $this, 'add_pass_field_to_register' ] );
    }

    /**
     * Includes
     */
    public function includes() {

    }

    /**
     * Add password field to the form
     */
    public function add_pass_field_to_register() {
        $pass = ( ! empty( $_POST['user_pass'] ) ) ? $_POST['user_pass'] : '';

        ?>
        <p>
            <label for="user_pass"><?php _e( 'Password', CRF_TEXT_DOMAIN ) ?><br />
                <input type="text" name="user_pass" id="user_pass" class="input" value="<?php echo $pass; ?>" size="25" /></label>
        </p>
        <?php
    }

    /**
     * Validate user password
     *
     * @param $errors
     * @param $sanitized_user_login
     * @param $user_email
     * @return mixed
     */
    public function validate_pass_register_field( $errors, $sanitized_user_login, $user_email ) {

        if ( empty( $_POST['user_pass'] ) || ! empty( $_POST['user_pass'] ) && trim( $_POST['user_pass'] ) == '' ) {
            $errors->add( 'user_pass_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', CRF_TEXT_DOMAIN ),__( 'You must include password.', CRF_TEXT_DOMAIN ) ) );

        }

        return $errors;
    }

    /**
     * Save user password
     *
     * @param $user_id
     */
    public function save_user_password( $user_id ) {
        if ( ! empty( $_POST['user_pass'] ) ) {
            $password = trim( $_POST['user_pass'] );
            wp_set_password( $password, $user_id );
        }
    }

}

/**
 * @return bool
 */
function CRF() {

    return Customized_Registration_Form::instance();
}
add_action( 'plugins_loaded', 'CRF' );