<?php

/**
  * This is inside of your own plugin
  * that you want to create endpoints
  * to
  */
  
function plugin_handle_users_get($request,$route){
  $users = []; // got users based on request
  return $route->send($users);
}

function plugin_handle_user_post($request,$route){
  $user_created = true; // handle create user request
  return $route->send($user_created);
}

function plugin_handle_get_user($request,$route){
  $id = $request->get_param('id');
  $user = []; // got user from db
  return $route->send($user);
}

function plugin_add_routes(){
  $router = new BeardedRouter('/plugin-api');
  $router->add_route('/users','plugin_handle_users_get');
  $router->add_route('/users',['post'=>'plugin_handle_user_post']);
  $router->add_route('/users/:id',['get'=>'plugin_handle_get_user']);
}

add_action('rest_api_init','plugin_add_routes');
