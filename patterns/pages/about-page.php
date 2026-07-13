<?php
/**
 * About page template.
 *
 * @package NoorBlocks
 */

use NoorBlocks\Patterns\Manager;

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'About Page', 'noorblocks' ),
	'description'   => __( 'A complete about page: intro story, stats, team and call to action.', 'noorblocks' ),
	'keywords'      => array( 'about', 'company', 'team' ),
	'blockTypes'    => array( 'core/post-content' ),
	'postTypes'     => array( 'page' ),
	'viewportWidth' => 1400,
	'content'       => implode(
		"\n\n",
		array(
			Manager::get_section_content( 'about-intro' ),
			Manager::get_section_content( 'stats' ),
			Manager::get_section_content( 'team' ),
			Manager::get_section_content( 'call-to-action' ),
		)
	),
);
