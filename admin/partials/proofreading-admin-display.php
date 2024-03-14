<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 * @package    Proofreading
 * @subpackage Proofreading/admin/partials
 * @author     Scribit <wordpress@scribit.it>
 */

function proofreading_admin_page_handler() {
	$current_page = isset($_GET['subpage']) && in_array($_GET['subpage'], ['settings', 'about']) ? esc_attr($_GET['subpage']) : 'settings';
?>
	<div class="wrap proofreading-backend proofreading-<?= $current_page ?>">
		<span class="clearfix proofreading-title">
			<span class="proofreading-logo"><img src="<?= plugins_url('../images/logo.png', __FILE__) ?>"></span>
			<h1><?= esc_html__('Proofreading', 'proofreading') ?></h1>
		</span>
		
		<h2 class="nav-tab-wrapper"> 
			<a href="options-general.php?page=<?= PROOFREADING_PLUGIN_SLUG ?>" class="nav-tab <?= ($current_page == 'settings') ? 'nav-tab-active' : '' ?>">
				<span class="dashicons dashicons-translation" aria-hidden="true"></span><?= esc_html__('Languages', 'proofreading') ?>
			</a> 
			<a style="color:#88C" href="options-general.php?page=<?= PROOFREADING_PLUGIN_SLUG ?>&subpage=about" class="nav-tab <?= ($current_page == 'about') ? 'nav-tab-active' : '' ?>">
				<span class="dashicons dashicons-info-outline" aria-hidden="true"></span><?= esc_html__('About', 'proofreading') ?>
			</a>
		</h2>
		
		<div class="proofreading-tab-content"><?php
		
		switch($current_page){
			case 'settings':
				proofreading_admin_page_settings_handler();
				break;
			
			case 'about':
				proofreading_admin_page_about_handler();
				break;
		}
		?></div>
	</div><?php
}
 
function proofreading_admin_page_settings_handler() {
	
	if (isset($_POST['submit'])) {
		$res = true;
		$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
		
		if ( wp_verify_nonce( $nonce, 'proofreading-admin-menu-save' ) ){
			if ( strlen($_POST['proofreading-language-default']) <= 2 )
				update_option("proofreading-language-default", esc_attr( $_POST['proofreading-language-default'] ) );
			
			if ( isset($_POST['rules']) && (strlen($_POST['proofreading-language-rules-settings']) <= 2) ){
				global $wpdb;

				$_POST['proofreading-language-rules-settings'] = esc_sql($_POST['proofreading-language-rules-settings']);

				$sql = $wpdb->prepare("SELECT `name`, `key`
					FROM {$wpdb->prefix}proofreading_rules
					WHERE lang_code = %s
					ORDER BY `name` ASC", $_POST['proofreading-language-rules-settings']);
				$rules = $wpdb->get_results($sql, ARRAY_A);
				$rules = array_column( $rules, 'key' );
				
				foreach ($_POST['rules'] as $rule_key => $rule){
					// Remove elements not in language rules
					if (array_search($rule, $rules) === false)
						unset($_POST['rules'][$rule_key]);
				}

				$wpdb->delete( "{$wpdb->prefix}proofreading_rules_settings", array( 'lang_code' => $_POST['proofreading-language-rules-settings'] ), array( '%s' ) );
				$wpdb->insert( 
					"{$wpdb->prefix}proofreading_rules_settings", 
					array( 
						'lang_code' => $_POST['proofreading-language-rules-settings'], 
						'included_rules' => implode(',', $_POST['rules'])
					), 
					array( '%s', '%s' ) 
				);
			}
		}
		else {
			$res = false;
		}
		?>
		<div id="setting-error-settings_updated" class="<?= $res ? '' : 'error' ?> updated settings-error notice is-dismissible"> 
			<p><strong><?= $res ? esc_html__('Settings saved.', 'proofreading') : esc_html__('Saving Error.', 'proofreading') ?></strong></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?= esc_html__('Dismiss this notice.', 'proofreading') ?></span>
			</button>
		</div>
<?php }
	else
	{
		$nonce = wp_create_nonce( 'proofreading-admin-menu-save' );
	} 
?>
	
	<form method="post" novalidate="novalidate">
		<input type="hidden" name="nonce" value="<?= $nonce ?>" />
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="proofreading-language-default"><?= esc_html__('Default correction Language', 'proofreading') .':' ?></label></th>
					<td>
						<?php global $wpdb;
							$languages = $wpdb->get_results("SELECT name, code
								FROM {$wpdb->prefix}proofreading_languages
								WHERE active = 1", ARRAY_A);
						?>
						<select id="proofreading-language-default" name="proofreading-language-default">
							<?php $current_value = get_option( "proofreading-language-default", 2 );
								foreach ($languages as $language): ?>
								<option <?= $current_value == $language['code'] ? 'selected' : '' ?> value="<?= $language['code'] ?>"><?= $language['name'] ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr><td colspan="2"><hr></td></tr>
				<tr>
					<th scope="row">
						<label><?= esc_html__('Rules category', 'proofreading') ?></label>
						<p class="description" id="tagline-description"><?= esc_html__('Enable or disable types of rule to be checked in the corrections.', 'proofreading') ?></p>
						<p><?= esc_html__('Select the language to manage the rules:', 'proofreading') ?></p>
						<select id="proofreading-language-rules-settings" name="proofreading-language-rules-settings">
							<?php $current_value = isset($_POST['proofreading-language-rules-settings']) ? $_POST['proofreading-language-rules-settings'] : get_option( "proofreading-language-default", 2 );
								foreach ($languages as $language): ?>
								<option <?= $current_value == $language['code'] ? 'selected' : '' ?> value="<?= $language['code'] ?>"><?= $language['name'] ?></option>
							<?php endforeach; ?>
						</select>
					</th>
					<td>
						<table class="proofreading_rules">
							<thead>
								<tr class="select_all_rules_row" style="display:none">
									<td><label for="select_all_rules"><input type="checkbox" id="select_all_rules"> <strong><?= esc_html__('Select all rules', 'proofreading') ?><strong></label></td>
								</tr>
							</thead>
							<tbody class="rules_container"></tbody>
						</table>
						
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?= esc_html__('Save settings', 'proofreading') ?>"></p>
	</form>
	
<?php }

function proofreading_admin_page_about_handler(){ ?>

	<table id="proofreading_about_support">
		<tr>
			<td class="scribit_support_description"><?= esc_html__('If you like our plugin please feel free to give us 5 stars :)', 'proofreading') ?></td>
			<td><a target="_blank" class="button button-primary scribit_support_button" rel="nofollow" href="https://wordpress.org/support/plugin/proofreading/reviews/">
				<span style="color:#CFC" class="dashicons dashicons-star-filled" aria-hidden="true"></span><?= esc_html__('WRITE A PLUGIN REVIEW', 'proofreading') ?>
			</a></td>
		</tr>

		<tr>
			<td class="scribit_support_description"><?= esc_html__('If you want to help us to improve our service please Donate a coffe', 'proofreading') ?></td>
			<td><a target="_blank" class="button button-primary scribit_support_button" rel="nofollow" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=riccardosormani@gmail.com&item_name=Proofreading Wordpress plugin donation&no_note=0">
				<span style="color:#FC9" class="dashicons dashicons-coffee" aria-hidden="true"></span><?= esc_html__('DONATE WITH PAYPAL', 'proofreading') ?>
			</a></td>
		</tr>

		<tr>
			<td class="scribit_support_description"><?= esc_html__('If you want some information about our Company', 'proofreading') ?></td>
			<td><a target="_blank" class="button button-primary scribit_support_button" href="mailto:wordpress@scribit.it">
				<span style="color:#DDD" class="dashicons dashicons-email" aria-hidden="true"></span><?= esc_html__('CONTACT US', 'proofreading') ?>
			</a></td>
		</tr>
	</table>
	
	<p id="footer-thankyou"><?php
		$url = 'https://languagetool.org';
		echo sprintf( wp_kses( __( 'Thanks to <a target="_blank" href="%s">Languagetool.org</a> services.', 'proofreading' ), array('a' => array('href' => array(), 'target' => array())) ), esc_url($url) );
	?></p>

	<br/><hr/>

	<h4><?php echo esc_html__('Try other Scribit plugins:', 'proofreading') ?></h4>
	<div class="wp-list-table widefat plugin-install">
		<div class="scribit_plugins">
			<?php $plugin_slug = 'shortcodes-finder'; ?>
			<div class="plugin-card plugin-card-<?php echo $plugin_slug ?>">
				<div class="plugin-card-top">
					<div class="name column-name">
						<h3><a href="
							<?php if ( is_multisite() ) : ?>
								<?php echo esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin='. $plugin_slug ) ) ?>
							<?php else : ?>
								<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin='. $plugin_slug ) ) ?>
							<?php endif ?>
						">Shortcodes Finder<img src="https://ps.w.org/<?php echo $plugin_slug ?>/assets/icon-256x256.png" class="plugin-icon"></a></h3>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<?php if ( class_exists('Shortcodes_Finder_Admin') ) : ?>
								<li><button type="button" class="button button-disabled" disabled="disabled"><?php echo esc_html__( 'Active', 'proofreading') ?></button></li>
							<?php else: ?>
								<li><a href="
									<?php if ( is_multisite() ) : ?>
										<?php echo esc_url( network_admin_url( 'plugin-install.php?s='. $plugin_slug .'+scribit&tab=search&type=term' ) ) ?>
									<?php else : ?>
										<?php echo esc_url( admin_url( 'plugin-install.php?s='. $plugin_slug .'+scribit&tab=search&type=term' ) ) ?>
									<?php endif ?>
								" class="button button-primary"><?php echo esc_html__('Install') ?></a></li>
							<?php endif; ?>
							<li><a href="
								<?php if ( is_multisite() ) : ?>
									<?php echo esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin='. $plugin_slug ) ) ?>
								<?php else : ?>
									<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin='. $plugin_slug ) ) ?>
								<?php endif ?>
							" class="thickbox open-plugin-details-modal"><?php echo esc_html__('More Details') ?></a></li>
						</ul>
					</div>
					<div class="desc column-description">
						<ul>
							<li><?php echo esc_html__('Find every shortcode (by tag or content type) present in your posts, pages and custom type contents', 'proofreading') ?></li>
							<li><?php echo esc_html__('Search unused shortcodes', 'proofreading') ?></li>
							<li><?php echo esc_html__('Disable active or unused/orphan shortcodes', 'proofreading') ?></li>
							<li><?php echo esc_html__('Test your shortcodes before use them in your website', 'proofreading') ?></li>
						</ul>
					</div>
				</div>
			</div>

			<?php $plugin_slug = 'random'; ?>
			<div class="plugin-card plugin-card-<?php echo $plugin_slug ?>">
				<div class="plugin-card-top">
					<div class="name column-name">
						<h3><a href="
							<?php if ( is_multisite() ) : ?>
								<?php echo esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin='. $plugin_slug ) ) ?>
							<?php else : ?>
								<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin='. $plugin_slug ) ) ?>
							<?php endif ?>
							">Random<img src="https://ps.w.org/<?php echo $plugin_slug ?>/assets/icon-256x256.png" class="plugin-icon"></a>
						</h3>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<?php if ( class_exists('Random_Admin') ) : ?>
								<li><button type="button" class="button button-disabled" disabled="disabled"><?php echo esc_html__('Active', 'proofreading') ?></button></li>
							<?php else: ?>
								<li>
									<a href="
									<?php if ( is_multisite() ) : ?>
										<?php echo esc_url( network_admin_url( 'plugin-install.php?s='. $plugin_slug .'+scribit&tab=search&type=term' ) ) ?>
									<?php else : ?>
										<?php echo esc_url( admin_url( 'plugin-install.php?s='. $plugin_slug .'+scribit&tab=search&type=term' ) ) ?>
									<?php endif; ?>
									" class="button button-primary"><?php echo esc_html__('Install') ?></a>
								</li>
							<?php endif; ?>
							<li><a href="
								<?php if ( is_multisite() ) : ?>
									<?php echo esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin='. $plugin_slug ) ) ?>
								<?php else : ?>
									<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin='. $plugin_slug ) ) ?>
								<?php endif ?>
							" class="thickbox open-plugin-details-modal"><?php echo esc_html__('More Details') ?></a></li>
						</ul>
					</div>
					<div class="desc column-description">
						<p><?php echo esc_html__('Insert into your WordPress website one or more random contents coming from your posts. The source contents can be pages, posts or custom post types.', 'proofreading') ?></p>
						<p><?php echo __('You can display different informations:<ul>
						   <li>A list of post titles</li>
						   <li>One or more full contents or excerpts</li>
						   <li>Raw URLs to posts permalink</li></ul>', 'proofreading') ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }