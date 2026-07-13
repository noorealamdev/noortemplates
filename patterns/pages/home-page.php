<?php
/**
 * Home page template.
 *
 * @package NoorBlocks
 */

use NoorBlocks\Patterns\Manager;

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Home Page', 'noorblocks' ),
	'description'   => __( 'A complete home page: hero, features, testimonials and call to action.', 'noorblocks' ),
	'keywords'      => array( 'home', 'landing', 'front page' ),
	'blockTypes'    => array( 'core/post-content' ),
	'postTypes'     => array( 'page' ),
	'viewportWidth' => 1400,
	'content'       => implode(
		"\n\n",
		array(
			Manager::get_section_content( 'hero' ),
			Manager::get_section_content( 'features' ),
			Manager::get_section_content( 'stats' ),
			Manager::get_section_content( 'testimonials' ),
			Manager::get_section_content( 'call-to-action' ),
		)
	),
);
