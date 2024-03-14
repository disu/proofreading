<?php
/**
 * Ajax functionalities
 *
 * Provide ajax async functionalities such as backend language selection event or languagetool call for text correction.
 *
 * Reference: https://webdevstudios.com/2015/02/12/handling-ajax-in-wordpress/
 *
 * @since      1.0.0
 * @package    Proofreading
 * @subpackage Proofreading/admin/includes
 * @author     Scribit <wordpress@scribit.it>
 */
class Proofreading_Ajax_Handler {
	
	public function __construct(){
		$this->hooks();
	}
	
	function hooks(){
		add_action( 'wp_ajax_analyze', array( $this, 'handle_ajax_analyze' ) );
		//add_action( 'wp_ajax_nopriv_analyze', array( $this, 'handle_ajax_analyze' ) );
		
		add_action( 'wp_ajax_select_lang_rules', array( $this, 'handle_select_lang_rules' ) );
		
		// Enqueue ajax script if we are in post add/edit page.
		global $pagenow;
		if (( $pagenow == 'post.php' ) || ( $pagenow == 'post-new.php' ) || (isset($_GET['page']) && ($_GET['page'] == PROOFREADING_PLUGIN_SLUG))) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}
	
	function handle_ajax_analyze() {
		if( ! wp_verify_nonce( $_REQUEST['nonce'], 'webdev' ) ){
			wp_send_json_error();
		}

		if (!isset($_REQUEST['language']) || strlen($_REQUEST['language']) > 2) return;
		
		require_once plugin_dir_path( __FILE__ ) . 'class-languagetool.php';
		$lt = new LanguageTool();
		
		global $wpdb;
		$sql = $wpdb->prepare("SELECT `included_rules` FROM {$wpdb->prefix}proofreading_rules_settings WHERE lang_code = %s", $_REQUEST['language']);
		$included_rules = $wpdb->get_var($sql);
		
		$body = $lt->check($_REQUEST['post_text'], $_REQUEST['language'], $included_rules);
		
		wp_send_json_success( array(
			'result' 		  => json_encode($body),
			'nonce'           => wp_create_nonce( 'webdev' ),
		) );
	}
	
	public function handle_select_lang_rules($code) {
		if( ! wp_verify_nonce( $_REQUEST['nonce'], 'webdev' ) ){
			wp_send_json_error();
		}
		
		global $wpdb;
		$result = array();
		
		$lang = esc_sql($_POST['lang_code']);
		
		$sql = $wpdb->prepare("SELECT `name`, `key`
			FROM {$wpdb->prefix}proofreading_rules
			WHERE lang_code = %s
			ORDER BY `name` ASC", $lang);
		$rules = $wpdb->get_results($sql, ARRAY_A);
			
		/*$included_rules = $wpdb->get_var("SELECT `included_rules` FROM {$wpdb->prefix}proofreading_rules_settings
			WHERE lang_code = '$lang'");*/
		$sql = $wpdb->prepare("SELECT `included_rules` 
			FROM {$wpdb->prefix}proofreading_rules_settings
			WHERE lang_code = %s", $lang);
		$included_rules = $wpdb->get_var($sql);
		
		$rules_included_keys = array();
		if ($included_rules != null){
			$rules_included_keys = explode(',', $included_rules);
		}
		
		for ( $i = 0; $i < count($rules); $i++ )
			if ( in_array($rules[$i]['key'], $rules_included_keys) || (count($rules_included_keys) == 0) )
				$rules[$i]['included'] = 1;
			else
				$rules[$i]['included'] = 0;
		
		$result['success'] = true;
		$result['data'] = array();
		$result['data']['result'] = $rules;
		
		echo json_encode($result);
		wp_die();
	}
	
	function enqueue_scripts() {
		wp_enqueue_script( 'webdev_js', plugins_url( '../js/proofreading-ajax.js', __FILE__ ), array( 'jquery' ), PROOFREADING_VERSION, true );
		wp_localize_script( 'webdev_js', 'proofreading', array(
			//'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'	   => wp_create_nonce( 'webdev' ),
			'trans_result_ok' => esc_html__('Your text is OK', 'proofreading'),
			'trans_result_errors_to_correct' => esc_html__('ERRORS to correct', 'proofreading'),
			'trans_result_errors_hidden' => esc_html__('Hidden errors', 'proofreading'),
			'trans_result_error' => esc_html__('Translation ERROR', 'proofreading'),
			'trans_result_error_too_many_errors' => esc_html__('Too many errors. Did you select the right language?', 'proofreading'),
			'trans_result_error_invalid_request' => esc_html__('Invalid request to Proofreading check service', 'proofreading'),
			'trans_btn_show_error' => esc_html__('Show error in the text', 'proofreading'),
			'trans_btn_hide_error' => esc_html__('Hide error', 'proofreading'),
			'trans_btn_hide_error_category' => esc_html__('Hide all errors in this category', 'proofreading'),
			'trans_btn_show_error_hidden' => esc_html__('Show hidden errors', 'proofreading'),
			'trans_warn_visual_mode' => esc_html__('Please set editor in "Visual" mode before analyze text', 'proofreading')
		) );
		
	}
	
}

$proofreadingAjax = new Proofreading_Ajax_Handler();