<?php
if (!defined('ABSPATH'))
{
    die;
} // Cannot access pages directly.

/**
 * Class MPWPB_Wc_Checkout_Account
 *
 * @since 1.0
 *  
 * */
if (!class_exists('MPWPB_Wc_Checkout_Account'))
{
    class MPWPB_Wc_Checkout_Account 
    {
        private $error;

        public function __construct()
        {
            $this->error = new WP_Error();
            add_action('mpwpb_wc_checkout_tab', array($this, 'tab_item'));
            add_action('mpwpb_wc_checkout_tab_content', array($this, 'tab_content'), 10, 1);
            add_action('admin_init', [ $this, 'save_mpwpb_wc_account_field_settings' ]);            
            //add_action('wp_loaded', array( $this,'apply' ), 7  );
            add_action('admin_notices',array($this, 'mp_admin_notice' ) );
        }

        public function apply()
        {
            			
        }

        public function tab_item()
        {
            ?>
                <li class="tab-item" data-tabs-target="#mpwpb_wc_account_field_settings"><i class="dashicons dashicons-admin-generic text-primary"></i> Account Fields <i class="i i-chevron-right dashicons dashicons-arrow-right-alt2"></i></li>
            <?php
        }

        public function tab_content($contents)
        {
            ?>
                <div class="tab-content" id="mpwpb_wc_account_field_settings">
                    <h2>Woocommerce Account Fields</h2>
                    <?php do_action('mpwpb_wc_checkout_add','account'); ?>
                    <!-- <table class="wc_gateways wp-list-table widefat striped"> -->
                    <div>
                    <table class="wc_gateways widefat striped">
						<thead>
							<tr>
								<th>Name</th>
                                <th>Label</th>
                                <th>Type</th>                                
                                <th>Placeholder</th>
                                <th>Validations</th>
                                <th>Required</th>
                                <th>Disabled</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>

                            <?php foreach ($contents['account'] as $key => $checkout_field) : ?>

                                <?php $status = ''; $status = (isset($checkout_field['disabled']) && $checkout_field['disabled']=='1')?'':'checked'; ?>
                                
                                <tr>
                                    <input id="<?php echo esc_attr(esc_html($key))?>" type="hidden" name="<?php echo esc_attr(esc_html($key))?>" value="<?php echo esc_attr(esc_html(json_encode(array('name'=>$key,'attributes'=>$checkout_field))))?>" />
                                    <td><?php echo esc_html($key); ?></td>
                                    <td><?php echo esc_html(isset($checkout_field['label'])?$checkout_field['label']:''); ?></td>
                                    <td><?php echo esc_html(isset($checkout_field['type'])?$checkout_field['type']:''); ?></td>
                                    <td><?php echo esc_html(isset($checkout_field['placeholder'])?$checkout_field['placeholder']:''); ?></td>
                                    <td><?php echo esc_html(implode(',',(isset($checkout_field['validate']) && is_array($checkout_field['validate']))?$checkout_field['validate']:array())); ?></td>
                                    <td><span  class="<?php echo esc_attr(esc_html((isset($checkout_field['required']) && $checkout_field['required']=='1')?'dashicons dashicons-yes tips':'')); ?>"></span></td>
                                    <td><span  class="checkout-disabled <?php echo esc_attr(esc_html((isset($checkout_field['disabled']) && $checkout_field['disabled']=='1')?'dashicons dashicons-yes tips':'')); ?>"></span></td>
                                    <td>
                                        <?php if(is_plugin_active('service-booking-manager-pro/MPWPB_Plugin_Pro.php')): ?>
                                        <?php do_action('mpwpb_wc_checkout_action','account',$key,$checkout_field); ?>
                                        <?php else: ?>
                                        <?php MPWPB_Wc_Checkout_Fields::switch_button($key,'checkoutSwitchButton',$key,$status,array('key'=>'account','name'=>$key)); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                                            
                            <?php endforeach; ?>
						</tbody>
					</table>
                    </div>
                </div>
            <?php
        }

        public function save_mpwpb_wc_account_field_settings()
        {
            // Save the
        }

        public function mp_admin_notice()
        {				
            MPWPB_Wc_Checkout_Fields::mp_error_notice($this->error);
        }
        
    }

    new MPWPB_Wc_Checkout_Account();
}