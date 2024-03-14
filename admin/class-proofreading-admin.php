<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the proofreading, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Proofreading
 * @subpackage Proofreading/admin
 * @author     Scribit <wordpress@scribit.it>
 */
class Proofreading_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Proofreading_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Proofreading_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $pagenow;
		if (( $pagenow == 'post.php' ) || ( $pagenow == 'post-new.php' ) || (get_post_type() == 'post') || (isset($_GET['page']) && ($_GET['page'] == PROOFREADING_PLUGIN_SLUG)) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/proofreading-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Proofreading_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Proofreading_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		
		if (isset($_GET['page']) && ($_GET['page'] == PROOFREADING_PLUGIN_SLUG)) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/proofreading-admin.js', array( 'jquery' ), $this->version, false );
		}

	}
	
	public function admin_notices() {
		
		$screen = get_current_screen();
	
		if ($screen->id === 'settings_page_proofreading') {
		
			if (!isset($_POST['submit'])) {  ?>
					
				<div class="notice notice-warning">
					<p><?= sprintf(
						__( 'Proofreading plugin is not yet Gutenberg ready. Please use <a target="_blank" rel="nofollow" href="%s">Classic Editor plugin</a> instead if you want a full compatibility.', 'proofreading' ), 
						esc_url( 'https://wordpress.org/plugins/classic-editor/' ) 
					); ?></p>
				</div>
				
			<?php }
		}
			
	}
	
	/**
	 * Define menu items for backend console.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function admin_menu() {
		
		require_once plugin_dir_path( __FILE__ ) . 'partials/proofreading-admin-display.php';
		
		add_options_page(
			esc_html__("Proofreading", "proofreading"),
			esc_html__("Proofreading", "proofreading"),
			"manage_options",
			PROOFREADING_PLUGIN_SLUG,
			"proofreading_admin_page_handler"
		);
	}
	
	public function admin_bar_menu($wp_admin_bar) {
		
		$args = array(
			'id'     => 'menu_id',
			'title'	=>	'title',
			'meta'   => array( 'class' => 'first-toolbar-group' ),
		);
		$wp_admin_bar->add_node( $args );	

		// add child items
		$args = array();
		array_push($args,array(
			'id'		=>	'id_sub',
			'title'		=>	'title_sub',
			'href'		=>	'sub_link',
			'parent'	=>	'menu_id',
		));
		
		foreach( $args as $each_arg)	{
			$wp_admin_bar->add_node($each_arg);
		}
	}
	
	function proofreading_footer_text (){
		
		// Show footer only in plugin pages
		if (strpos(get_current_screen()->id, 'settings_page_proofreading') !== 0) return;

		$url = 'https://www.scribit.it';
		echo '<span class="scribit_credit">'.sprintf( '%s <a href="%s" target="_blank">Scribit</a>', esc_html__('Proofreading is powered by', 'proofreading'), esc_url($url) ).'</span>';
		
	}
		
	public function proofreading_actions_links( $links ) {
		
		$settings_link = '<a href="options-general.php?page=' . PROOFREADING_PLUGIN_SLUG . '">' . esc_html__('Settings', 'proofreading') . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
		
	}
	
	public function load_metaboxes() {
		
		if( !class_exists('ProofreadingMetaBox') )
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-proofreading-metabox.php';
		
		new ProofreadingMetaBox();
		
	}
}