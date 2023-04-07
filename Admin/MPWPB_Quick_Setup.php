<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPWPB_Quick_Setup' ) ) {
		class MPWPB_Quick_Setup {
			public function __construct() {
				if ( ! class_exists( 'MPWPB_Dependencies' ) ) {
					add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ), 10, 1 );
				}
				add_action( 'admin_menu', array( $this, 'quick_setup_menu' ) );
			}
			public function add_admin_scripts() {
				wp_enqueue_style( 'mp_plugin_global', MPWPB_PLUGIN_URL . '/assets/helper/mp_style/mp_style.css', array(), time() );
				wp_enqueue_script( 'mp_plugin_global', MPWPB_PLUGIN_URL . '/assets/helper/mp_style/mp_script.js', array( 'jquery' ), time(), true );
				wp_enqueue_script( 'mp_admin_settings', MPWPB_PLUGIN_URL . '/assets/admin/mp_admin_settings.js', array( 'jquery' ), time(), true );
				wp_enqueue_style( 'mp_admin_settings', MPWPB_PLUGIN_URL . '/assets/admin/mp_admin_settings.css', array(), time() );
				wp_enqueue_script( 'mpwpb_admin', MPWPB_PLUGIN_URL . '/assets/admin/mpwpb_admin.js', array( 'jquery' ), time(), true );
				wp_enqueue_style( 'mpwpb_admin', MPWPB_PLUGIN_URL . '/assets/admin/mpwpb_admin.css', array(), time() );
				wp_enqueue_style( 'mptbm_font_awesome', '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css', array(), '5.15.4' );
			}
			public function quick_setup_menu() {
				$status = MPWPB_Plugin::check_woocommerce();
				if ( $status == 1 ) {
					add_submenu_page( 'edit.php?post_type=mpwpb_item', _( 'Quick Setup', 'bookingplus' ), '<span style="color:#10dd10">' . esc_html__( 'Quick Setup', 'bookingplus' ) . '</span>', 'manage_options', 'mpwpb_quick_setup', array( $this, 'quick_setup' ) );
					add_submenu_page( 'mpwpb_item', esc_html__( 'Quick Setup', 'bookingplus' ), '<span style="color:#10dd10">' . esc_html__( 'Quick Setup', 'bookingplus' ) . '</span>', 'manage_options', 'mpwpb_quick_setup', array( $this, 'quick_setup' ) );
				} else {
					add_menu_page( esc_html__( 'Bookingplus', 'bookingplus' ), esc_html__( 'Bookingplus', 'bookingplus' ), 'manage_options', 'mpwpb_item', array( $this, 'quick_setup' ), 'dashicons-admin-site-alt2', 6 );
					add_submenu_page( 'mpwpb_item', esc_html__( 'Quick Setup', 'bookingplus' ), '<span style="color:#10dd17">' . esc_html__( 'Quick Setup', 'bookingplus' ) . '</span>', 'manage_options', 'mpwpb_quick_setup', array( $this, 'quick_setup' ) );
				}
			}
			public function quick_setup() {
				if ( isset( $_POST['active_woo_btn'] ) ) {
					?>
					<script>
								  dLoaderBody();
					</script>
					<?php
					activate_plugin( 'woocommerce/woocommerce.php' );
					?>
					<script>
								  let mpwpb_admin_location = window.location.href;
								  mpwpb_admin_location = mpwpb_admin_location.replace('admin.php?page=mpwpb_item', 'edit.php?post_type=mpwpb_item&page=mpwpb_quick_setup');
								  window.location.href = mpwpb_admin_location;
					</script>
					<?php
				}
				if ( isset( $_POST['install_and_active_woo_btn'] ) ) {
					echo '<div style="display:none">';
					include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); //for plugins_api..
					$plugin = 'woocommerce';
					$api    = plugins_api( 'plugin_information', array(
						'slug'   => $plugin,
						'fields' => array(
							'short_description' => false,
							'sections'          => false,
							'requires'          => false,
							'rating'            => false,
							'ratings'           => false,
							'downloaded'        => false,
							'last_updated'      => false,
							'added'             => false,
							'tags'              => false,
							'compatibility'     => false,
							'homepage'          => false,
							'donate_link'       => false,
						),
					) );
					//includes necessary for Plugin_Upgrader and Plugin_Installer_Skin
					include_once( ABSPATH . 'wp-admin/includes/file.php' );
					include_once( ABSPATH . 'wp-admin/includes/misc.php' );
					include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
					$woocommerce_plugin = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
					$woocommerce_plugin->install( $api->download_link );
					activate_plugin( 'woocommerce/woocommerce.php' );
					echo '</div>';
					?>
					<script>
								  let mpwpb_admin_location = window.location.href;
								  mpwpb_admin_location = mpwpb_admin_location.replace('admin.php?page=mpwpb_item', 'edit.php?post_type=mpwpb_item&page=mpwpb_quick_setup');
								  window.location.href = mpwpb_admin_location;
					</script>
					<?php
				}
				if ( isset( $_POST['finish_quick_setup'] ) ) {
					$label                       = isset( $_POST['mpwpb_label'] ) ? sanitize_text_field( $_POST['mpwpb_label'] ) : 'Transportation';
					$slug                        = isset( $_POST['mpwpb_slug'] ) ? sanitize_text_field( $_POST['mpwpb_slug'] ) : 'transportation';
					$general_settings_data       = get_option( 'mpwpb_general_settings' );
					$update_general_settings_arr = [
						'label' => $label,
						'slug'  => $slug
					];
					$new_general_settings_data   = is_array( $general_settings_data ) ? array_replace( $general_settings_data, $update_general_settings_arr ) : $update_general_settings_arr;
					update_option( 'mpwpb_general_settings', $new_general_settings_data );
					flush_rewrite_rules();
					wp_redirect( admin_url( 'edit.php?post_type=mpwpb_item' ) );
				}
				?>
				<div id="mp_quick_setup" class="mpStyle">
					<form method="post" action="">
						<div class="mpTabsNext">
							<div class="tabListsNext _max_700_mAuto">
								<div data-tabs-target-next="#mpwpb_qs_welcome" class="tabItemNext">
									<h4 class="circleIcon">1</h4>
									<h5 class="circleTitle"><?php esc_html_e( 'Welcome', 'bookingplus' ); ?></h5>
								</div>
								<div data-tabs-target-next="#mpwpb_qs_general" class="tabItemNext">
									<h4 class="circleIcon">2</h4>
									<h5 class="circleTitle"><?php esc_html_e( 'General', 'bookingplus' ); ?></h5>
								</div>
								<div data-tabs-target-next="#mpwpb_qs_done" class="tabItemNext">
									<h4 class="circleIcon">3</h4>
									<h5 class="circleTitle"><?php esc_html_e( 'Done', 'bookingplus' ); ?></h5>
								</div>
							</div>
							<div class="tabsContentNext _infoLayout_mT">
								<?php
									$this->setup_welcome_content();
									$this->setup_general_content();
									$this->setup_content_done();
								?>
							</div>
							<div class="justifyBetween">
								<button type="button" class="mpBtn nextTab_prev"><span>&longleftarrow;<?php esc_html_e( 'Previous', 'bookingplus' ); ?></span></button>
								<div></div>
								<button type="button" class="themeButton nextTab_next"><span><?php esc_html_e( 'Next', 'bookingplus' ); ?>&longrightarrow;</span></button>
							</div>
						</div>
					</form>
				</div>
				<?php
			}
			public function setup_welcome_content() {
				$status = MPWPB_Plugin::check_woocommerce();
				?>
				<div data-tabs-next="#mpwpb_qs_welcome">
					<h2><?php esc_html_e( 'Bookingplus For Woocommerce Plugin', 'bookingplus' ); ?></h2>
					<p class="mTB_xs"><?php esc_html_e( 'Bookingplus Plugin for WooCommerce for your site, Please go step by step and choose some options to get started.', 'bookingplus' ); ?></p>
					<div class="_dLayout_mT_alignCenter justifyBetween">
						<h5>
							<?php if ( $status == 1 ) {
								esc_html_e( 'Woocommerce already installed and activated', 'bookingplus' );
							} elseif ( $status == 0 ) {
								esc_html_e( 'Woocommerce need to install and active', 'bookingplus' );
							} else {
								esc_html_e( 'Woocommerce already install , please activate it', 'bookingplus' );
							} ?>
						</h5>
						<?php if ( $status == 1 ) { ?>
							<h5><span class="fas fa-check-circle textSuccess"></span></h5>
						<?php } elseif ( $status == 0 ) { ?>
							<button class="warningButton" type="submit" name="install_and_active_woo_btn"><?php esc_html_e( 'Install & Active Now', 'bookingplus' ); ?></button>
						<?php } else { ?>
							<button class="themeButton" type="submit" name="active_woo_btn"><?php esc_html_e( 'Active Now', 'bookingplus' ); ?></button>
						<?php } ?>
					</div>
				</div>
				<?php
			}
			public function setup_general_content() {
				$label        = self::get_general_settings( 'label', 'Bookingplus' );
				$slug        = self::get_general_settings( 'slug', 'bookingplus' );
				?>
				<div data-tabs-next="#mpwpb_qs_general">
					<div class="section">
						<h2><?php esc_html_e( 'General settings', 'bookingplus' ); ?></h2>
						<p class="mTB_xs"><?php esc_html_e( 'Choose some general option.', 'bookingplus' ); ?></p>
						<div class="_dLayout_mT">
							<label class="fullWidth">
								<span class="min_200"><?php esc_html_e( 'Bookingplus Label:', 'bookingplus' ); ?></span>
								<input type="text" class="formControl" name="mpwpb_label" value='<?php echo esc_attr( $label ); ?>'/>
							</label>
							<i class="info_text">
								<span class="fas fa-info-circle"></span>
								<?php esc_html_e( 'It will change the Bookingplus post type label on the entire plugin.', 'bookingplus' ); ?>
							</i>
							<div class="divider"></div>
							<label class="fullWidth">
								<span class="min_200"><?php esc_html_e( 'Bookingplus Slug:', 'bookingplus' ); ?></span>
								<input type="text" class="formControl" name="mpwpb_slug" value='<?php echo esc_attr( $slug ); ?>'/>
							</label>
							<i class="info_text">
								<span class="fas fa-info-circle"></span>
								<?php esc_html_e( 'It will change the Bookingplus slug on the entire plugin. Remember after changing this slug you need to flush permalinks. Just go to Settings->Permalinks hit the Save Settings button', 'bookingplus' ); ?>
							</i>
						</div>
					</div>
				</div>
				<?php
			}
			public function setup_content_done() {
				?>
				<div data-tabs-next="#mpwpb_qs_done">
					<h2><?php esc_html_e( 'Finalize Setup', 'bookingplus' ); ?></h2>
					<p class="mTB_xs"><?php esc_html_e( 'You are about to Finish & Save Bookingplus For Woocommerce Plugin setup process', 'bookingplus' ); ?></p>
					<div class="mT allCenter">
						<button type="submit" name="finish_quick_setup" class="themeButton"><?php esc_html_e( 'Finish & Save', 'bookingplus' ); ?></button>
					</div>
				</div>
				<?php
			}
			public static function get_general_settings( $key, $default = '' ) {
				$options = get_option( 'mpwpb_general_settings' );
				if ( isset( $options[ $key ] ) && $options[ $key ] ) {
					$default = $options[ $key ];
				}
				return $default;
			}
		}
		new MPWPB_Quick_Setup();
	}