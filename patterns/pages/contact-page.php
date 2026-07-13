<?php
/**
 * Contact page template.
 *
 * @package NoorBlocks
 */

use NoorBlocks\Patterns\Manager;

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Contact Page', 'noorblocks' ),
	'description'   => __( 'A complete contact page: contact details and call to action.', 'noorblocks' ),
	'keywords'      => array( 'contact', 'get in touch' ),
	'blockTypes'    => array( 'core/post-content' ),
	'postTypes'     => array( 'page' ),
	'viewportWidth' => 1400,
	'content'       => implode(
		"\n\n",
		array(
			Manager::get_section_content( 'contact' ),
			Manager::get_section_content( 'call-to-action' ),
		)
	),
);
