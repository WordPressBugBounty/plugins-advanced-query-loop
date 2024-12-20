<?php
/**
 * Class for generating the query params
 *
 * @package AdvancedQueryLoop
 */

namespace AdvancedQueryLoop;

/**
 * Class to handle creating the params.
 */
class Query_Params_Generator {

	use Traits\Multiple_Posts;
	use Traits\Exclude_Current;
	use Traits\Include_Posts;
	use Traits\Meta_Query;
	use Traits\Date_Query;
	use Traits\Disable_Pagination;
	use Traits\Tax_Query;
	use Traits\Post_Parent;


	/**
	 * The list of all custom params
	 */
	const KNOWN_PARAMS = array(
		'multiple_posts',
		'exclude_current',
		'include_posts',
		'meta_query',
		'date_query',
		'disable_pagination',
		'tax_query',
		'post_parent',
	);

	/**
	 * Default values from the default block.
	 *
	 * @var array
	 */
	private array $default_params;

	/**
	 * Custom values from AQL
	 *
	 * @var array
	 */
	private array $custom_params;

	/**
	 * Customized params to return
	 *
	 * @var array
	 */
	private array $custom_args = array();

	/**
	 * Construct method
	 *
	 * @param array $default_params Default values from the default block.
	 * @param array $custom_params  Custom values from AQL.
	 */
	public function __construct( $default_params, $custom_params ) {
		$this->default_params = is_array( $default_params ) ? $default_params : array();
		$this->custom_params  = is_array( $custom_params ) ? $custom_params : array();
	}


	/**
	 * Checks to see if the item that is passed is a post ID.
	 *
	 * This is used to check if the user is editing a template
	 *
	 * @param mixed $possible_post_id The potential post id
	 *
	 * @return bool Whether the passed item is a post id or not.
	 */
	private function is_post_id( $possible_post_id ) {
		return is_int( $possible_post_id ) || ! preg_match( '/[a-z\-]+\/\/[a-z\-]+/', $possible_post_id );
	}

	/**
	 * Check to see if a param is in the list.
	 *
	 * @param string $param_name The param to look for.
	 */
	public function has_custom_param( string $param_name ): bool {
		return array_key_exists( $param_name, $this->custom_params ) && ! empty( $this->custom_params[ $param_name ] );
	}

	/**
	 * Retrieve a single param.
	 *
	 * @param string $name The param to retrieve.
	 *
	 * @todo Return mixed type hint for 8.0
	 *
	 * @return mixed
	 */
	public function get_custom_param( string $name ) {
		if ( $this->has_custom_param( $name ) ) {
			return $this->custom_params[ $name ];
		}
		return false;
	}

	/**
	 * Process all params at once.
	 */
	public function process_all(): void {
		foreach ( self::KNOWN_PARAMS as $param_name ) {
			if ( $this->has_custom_param( $param_name ) ) {
				call_user_func( array( $this, 'process_' . $param_name ) );
			}
		}
	}

	/**
	 * Retrieve the custom args
	 */
	public function get_query_args(): array {
		return $this->custom_args;
	}

}
