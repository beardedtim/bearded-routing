<?php

/**
 * Plugin Name: Bearded Routing
 * Plugin URI: https://github.com/beardedtim/bearded-routing
 * Description: A basic Express.js style interface for WordPress' REST API.
 * Version: 0.1.0
 * Author: Timi Roberts <timiroberts@gmail.com>
 * Author URI: https://github.com/beardedtim
 * License: GPLv2 or later
 * Text Domain: bearded-routing
 * GitHub Plugin URI: beardedtim/bearded-routing
 */

/**
 * We are setting a constant here to be ALLMETHODS
 * from WP_REST_Server. We can change this to be
 * GET -> WP_REST_SERVER::GET, POST ->....
 *
 * If we do that, we will need to change how
 * BeardedRouter sets things, which will
 * probably be a better idea.
 */
define('ALL_OF_THEM',WP_REST_Server::ALLMETHODS);

if(!class_exists('BeardedRoute')){


	/**
	 * Basic Route Handler for WordPress.
	 *
	 * Holds callbacks for $this->route. Calls $this->methods[method]
	 * with arguments ($WP_REST_Response, $this). This function is
	 * expected to return to the client the response for the endpoint.
	 *
	 * Will return error if verb does not have a callback
	 *
	 *
	 * @since 0.1.0
	 */
	class BeardedRoute{

		/**
		 * The methods on this route
		 *
		 * ['http_verb' => 'callable']
		 *
		 * @since 0.1.0
		 * @var array $methods
		 */
		public $methods = [];

		/**
		 * The route that this handler is for
		 *
		 * WordPress-style syntax of route
		 *
		 * @since 0.1.0
		 * @var string $route
		 */
		public $route;

		/**
		 * BeardedRoute constructor.
		 *
		 * Sets route and starting callbacks
		 *
		 * @since 0.1.0
		 * @param string $route
		 * @param array $callbacks
		 */
		function __construct($route = '/default',$callbacks = []) {
			$this->route = $route;
			$this->methods = $callbacks;
		}

		/**
		 * Calls callback for specified route
		 *
		 * @since 0.1.0
		 * @param WP_REST_Request $request
		 * @return mixed|WP_Error
		 */
		public function handle_route($request){
			$method = strtolower($request->get_method());
			if(!isset($this->methods[$method])){
				return new WP_Error('no_method_found','There was no handler for this REST method.',500);
			}
			return call_user_func($this->methods[$method],$request,$this);
		}

		/**
		 * Adds/overrides REST verb methods
		 *
		 * This modifies $this->methods and might overwrite
		 * previous callbacks if the new $methods array
		 * has the same keys:
		 *
		 * Example:
		 *          $this->methods = ['get'=>'original_callback']
		 *          // Sometime later
		 *          $route->add_methods(['get'=>'new_callback'])
		 *          // $this state has changed to:
		 *          $this->methods = ['get'=>'new_callback']
		 *
		 *
		 * @since 0.1.0
		 * @param array $methods
		 */
		public function add_methods($methods){
			$this->methods = array_merge($this->methods,$methods);
		}

		/**
		 * Creates an ensured WP_REST_Response
		 *
		 * Simple wrapper so I don't have to remember
		 * what this function was called.
		 *
		 * @since 0.1.0
		 * @param * $data
		 * @return mixed|WP_REST_Response
		 */
		public function send($data){
			return rest_ensure_response($data);
		}
	}
}




if(!class_exists('BeardedRouter')){


	/**
	 * Points endpoints to handlers.
	 *
	 * Takes in Express.js style routes and creates
	 * WordPress routes, attaching a BeardedRoute handler
	 * to each.
	 *
	 * @since 0.1.0
	 */
	class BeardedRouter{
		/**
		 * The root of our API : .com/wp-json/$root
		 *
		 * @since 0.1.0
		 * @var string $root
		 */
		public $root;

		/**
		 * List of callbacks attached to routes
		 *
		 * @since 0.1.0
		 * @var array $routes
		 */
		public $route_handlers = [];


		/**
		 * BeardedRouter constructor.
		 *
		 * Sets $this->root to $root
		 *
		 * @since 0.1.0
		 * @param string $root
		 */
		function __construct($root = 'bearded-family') {
			$this->root = $root;
		}

		/**
		 * Cheaply checks if a string is possibly a route
		 *
		 * @since 0.1.0
		 * @param string $str
		 * @return bool
		 */
		public function _is_param($str){
			return strpos($str,':') === 0;
		}

		/**
		 * Creates a WordPress RegExp-ish string from Express-ish string
		 *
		 * :name => (?P<name>.*?)
		 *
		 * @since 0.1.0
		 * @param string $str
		 * @return string
		 */
		public function _create_param($str){
			return '(?P<'.substr($str, 1).'>.*?)';
		}

		/**
		 * Creates WordPress valid endpoint from Express-ish endpoint
		 *
		 * @since 0.1.0
		 * @param string $str
		 * @return string
		 */
		public function create_wordpress_route($str){
			$split = explode('/',$str);
			$made = array_map(function($val){
				if($this->_is_param($val)){
					return $this->_create_param($val);
				}else {
					return $val;
				}
			},$split);
			$endpoint = implode('/',$made);
			return $endpoint;
		}

		/**
		 * Returns the end of the route, without root
		 *
		 * @since 0.1.0
		 * @param string $full_route
		 * @return mixed
		 */
		public function get_endpoint($full_route) {
			return str_replace( '/' . $this->root, '', $full_route );
		}

		/**
		 * Attaches callbacks to a route
		 *
		 * @since 0.1.0
		 * @param string $route
		 * @param string[]|string $callbacks
		 *
		 * @return bool|WP_Error
		 */
		public function add_route($route = '/default',$callbacks){

			// If we are not given a callback, let's throw an Error
			if(!$callbacks){
				return new WP_Error('bad_route_added','You have not given add_route a callback',$callbacks);
			}

			// If they just gave us a single callback, treat it as
			// a GET handler and create an array
			if(is_string($callbacks)){
				$callbacks = [
					'get'=>$callbacks
				];
			}

			// Then we need to convert our Express-ish route
			// into a WordPress route
			$cleaned_route = $this->create_wordpress_route($route);

			// If we have not encountered this route before
			// Let's do all the setup for it
			if(!isset($this->route_handlers[$cleaned_route])){

				// We create a new Route Handler for this route
				$route_handler = new BeardedRoute($cleaned_route,$callbacks);
				// We register this endpoint with WordPress
				// giving all of the methods as possible endpoints
				// because in our $route_handler->handle_route function
				// we throw an error if a handler was not set
				register_rest_route($this->root,$cleaned_route,[
					'methods'=>ALL_OF_THEM,
					'callback'=>[$route_handler,'handle_route']
				]);

				// Then we add this new handler to our route_handlers
				// array
				$this->route_handlers[$cleaned_route] = $route_handler;
			}else{

				// If we have encountered this route before,
				// let's just add more methods to it
				//
				// THIS OVERRIDES OLD METHODS
				// OLD
				// get => ....
				//
				// NEW
				// get => new....
				//
				// FINAL
				// get => new....
				$handler = $this->route_handlers[$cleaned_route];
				$handler->add_methods($callbacks);
			}

		}

		/**
		 * Returns all route handlers
		 *
		 * @since 0.1.0
		 * @return array
		 */
		public function get_route_handlers(){
			return $this->route_handlers;
		}

		/**
		 * Returns the handler for a specified route
		 *
		 * @since 0.1.0
		 * @param string $route
		 * @return array|*
		 */
		public function get_route_handler($route){
			return $this->route_handlers[$route];
		}
	}
}
