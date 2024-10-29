<?php
/**
 * Helper functions
 *
 * @package     AffiliateWP\ActiveCampaign\Functions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Retrieve an array of available lists
 *
 * @since       1.0.0
 * @return      array $lists The available lists
 */
function affiliatewp_activecampaign_get_lists() {
	if( affiliatewp_activecampaign()->activecampaign ) {
		$ac_lists = affiliatewp_activecampaign()->activecampaign->api( 'list/paginator?limit=999' );

		if( $ac_lists && is_object( $ac_lists ) && $ac_lists->result_code == 0 ) {
			$lists = array(
				0 => __( 'The API key you entered is invalid. Please check your account details and try again.', 'affiliatewp-activecampaign' )
			);
		} elseif( $ac_lists && is_object( $ac_lists ) && $ac_lists->result_code == 1 ) {
			foreach( $ac_lists->rows as $list ) {
				$lists[$list->id] = $list->name;
			}
		} else {
			$lists = array(
				0 => __( 'An unknown error occurred. Please check your account details and try again.', 'affiliatewp-activecampaign' )
			);
		}
	} else {
		$lists = array(
			0 => __( 'Please enter your API URL and key to select a list.', 'affiliatewp-activecampaign' )
		);
	}

	return $lists;

	//var_dump( $lists );
}