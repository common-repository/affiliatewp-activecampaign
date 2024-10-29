<?php
/**
 * Actions
 *
 * @package     AffiliateWP\ActiveCampaign\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Add a checkbox to the registration page
 *
 * @since       1.0.0
 * @return      void
 */
function affiliatewp_activecampaign_add_signup_checkbox() {
	if( ! affiliate_wp()->settings->get( 'activecampaign_optin', false ) ) {
		?>
		<p>
			<label class="affwp-activecampaign" for="affwp-activecampaign">
				<input id="affwp-activecampaign" type="checkbox" name="affwp_activecampaign" />
				<?php _e( 'Opt-in to our Newsletter', 'affiliatewp-activecampaign' ); ?>
			</label>
		</p>
		<?php
	}
}
add_action( 'affwp_register_fields_before_tos', 'affiliatewp_activecampaign_add_signup_checkbox' );


/**
 * Register a user
 *
 * @since       1.0.0
 * @return      void
 */
function affiliatewp_activecampaign_subscribe_user() {
	$auto_register = affiliate_wp()->settings->get( 'activecampaign_optin', false );
	$list = affiliate_wp()->settings->get( 'activecampaign_list', false );

	if( $auto_register || ! empty( $_POST['affwp_activecampaign'] ) ) {
		if( affiliatewp_activecampaign()->activecampaign && $list ) {
			// Check if the email is already subscribed
			$result = affiliatewp_activecampaign()->activecampaign->api( 'contact/list?filters[listid]=' . $list . '&filters[email]=' . $_POST['affwp_user_email'] );

			if( $result->result_code == 0 ) {
				$name = explode( ' ', $_POST['affwp_user_name'] );

				if( count( $name ) == 1 ) {
					$first_name = $name[0];
					$last_name  = '';
				} else {
					$last_name  = array_pop( $name );
					$first_name = implode( ' ', $name );
				}

				$data = array(
					'email'            => $_POST['affwp_user_email'],
					'p[' . $list . ']' => $list,
					'first_name'       => $first_name,
					'last_name'        => $last_name
				);

				$result = affiliatewp_activecampaign()->activecampaign->api( 'contact/add', $data );

				// Maybe add logging eventually...
				//if( $result->result_code != 1 ) {}
			}
		}
	}
}
add_action( 'affwp_process_register_form', 'affiliatewp_activecampaign_subscribe_user' );