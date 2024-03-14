<?php
require_once plugin_dir_path( __FILE__ ) . 'scribit_custom_field.php';
 
class ProofreadingMetaBox {
     
    public $parameters = array();
    protected $_cf;
     
    public function __construct( ) {
		
		$screens = get_post_types();
		
        $this->parameters = array(
			'id' => 'proofreading_box',
			'title' => esc_html__('Proofreading', 'proofreading'),
			// 'screen' => array('post', 'page'),
			'screen' => $screens,
			'context' => 'side',
			'priority' => 'high'
		);
		
		$this->loadFields();
		
		include_once plugin_dir_path( __FILE__ ) . 'class-ajax-handler.php';
    }
     
    public function loadFields() {
		
		global $wpdb;
		$fields = array();
		
		$languages = $wpdb->get_results("SELECT name AS label, longCode AS value
			FROM {$wpdb->prefix}proofreading_languages
			WHERE active = 1", ARRAY_A);

		$this->fields[] = array(
			'name' => 'language',
			'id' => 'language',
			'class' => 'language_selection',
			'type' => 'select',
			'options' => $languages,
			'value' => get_option('proofreading-language-default'),
			'allow_empty' => false
		);
		
		$this->fields[] = array(
			'url' => plugins_url('../images/language.png', __FILE__),
			'type' => 'image',
			'style' => 'float:right;margin:4px 4px 0 4px;'
		);
		
		$this->fields[] = array(
			'name' => 'proofreading_analyze',
			'id' => 'proofreading_analyze',
			'class' => 'analyze_button',
			'type' => 'button',
			'label' => esc_html__( 'Analyze', 'proofreading' )
		);
		
		$this->fields[] = array(
			'id' => 'proofreading_analyze_results',
			'type' => 'div',
			'class' => 'proofreading_analyze_results',
			'label' => ''
		);
		
		$this->fields[] = array(
			'id' => 'proofreading_compatibility_note',
			'class' => 'proofreading_compatibility_note',
			'type' => 'div',
			'label' => sprintf(
				__( 'Proofreading is not yet compatible with Gutenberg editor; please use <a target="_blank" rel="nofollow" href="%s">Classic Editor</a> instead.', 'proofreading' ),
				esc_url( 'https://wordpress.org/plugins/classic-editor/' )
			)
		);
		
        $this->_cf = new ScribitCustomField( $this->parameters, $this->fields ); 
		
    }
	
}

?>