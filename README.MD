#Bearded Routing

> Because why not make WordPress routing look and feel like Express?

This is a silly abstraction on top of the WordPress REST API. This was a fun experience learning how WordPress API worked and how we create routes and callbacks for those routes.

This is a **work in progress** and the API might change. Any and all PRs and issues are welcome!

##Install

This is made to work as a WordPress plugin but can be copy/pasted into your own projects. If you download this and activate it as a plugin in WordPress, it will expose BeardedRouter and BeardedRoute as classes to interact with the WP_REST classes. 


#API

The two classes, `BeardedRouter` and `BeardedRoute`, offer a few methods and properties that we can use handle most instances for responding to a RESTful request. Let's look at `BeardedRoute` first:

##Route

```
$route = new BeardedRoute(
            [$route='/default']: string, 
            $callbacks: ['get'=>'callable']
        );
```

##Route#Params

###Route->route
```
What is it?
    The WordPress route that Route handles
    
What type is it?
    String
    
```

###Route->methods
```
What is it?
    An list verb=>callback groups. 
    When we use Route->handle_route,
    we look insideof this array for
    what callable to use
    
What type is it?
    Array: ['http_verb'=>'callable']
    
```

##Route#Methods

###Route->handle_route
```
What is it?
    A function that, given a WP_REST_Request
    object, tries to call the correct
    callable callback.
    
What does it take?
    WP_REST_Request: $request
        The request object that WordPress is
        giving us
 
What does it return?
    Either result of callable or WP_Error
    if given unknown verb
    
```

###Route->add_methods
```
What is it?
    The way we update Route->methods. 
    This updates the inner state and
    is up to us to keep track of!
    
What does it take?

    Array: ['verb'=>'callable'] $methods
        The new methods to merge into our
        already known methods. THIS
        CHANGES THE INNER STATE AND 
        OVERRIDES PAST METHODS IF SAME
        VERB IS ENCOUNTERED
    
What does it return?
    null|undefined
```

###Route->send
```
What is it?
    A simple wrapper for 
    rest_ensure_response
    
What does it take?

    *: $data
        The data that we want to 
        return to the client
    
What does it return?
   rest_ensure_response($data)
        the result of this expression
```

##Router

```
$router = new BeardedRouter(
            [$root='bearded-family']: string, 
        );
```

##Router#Params

###Router->root
```
What is it?
    The root of the group of endpoints that
    this router handles
    
What type is it?
    String
    
```

###Router->route_handlers
```
What is it?
    An list endpoint=>Route groups. 
    Acting as our way of referencing
    the handlers later
    
What type is it?
    Array: ['endpoint'=>Route]
    
```

##Router#Methods

###Router->_is_param
```
What is it?
    A function that checks where a
    string is an Express-ish param
    
What does it take?
    String: $str
        The string we want to check
 
What does it return?
    Boolean whether or not it is
    a parameter
    
```

###Router->_create_param
```
What is it?
    A function to take in an Express
    parameter and turn it into a
    WordPress parameter
    
What does it take?

    String: $str
        The Express style parameter string
    
What does it return?
    String: (?P<param>.*?)
        The WordPress style parameter string            
```

###Router->create_wordpress_route
```
What is it?
    Creates a WordPress valid route 
    from an Express valid route
    
What does it take?

    String: $str
        The Express style route
    
What does it return?
   String:
        The WordPress valid route string
```

###Router->get_endpoint
```
What is it?
    Gets just the endpoints after
    our root url
    
What does it take?

    String: $full_route
       /$this->root/some/route
    
What does it return?
   String:
        The string without /root
```

###Router->add_route
```
What is it?
    The way we add attach callbacks 
    to verbs and routes
    
What does it take?

    String: $route
        The Express style route to attach to
    String|Array: $callbacks
        Either a single callable or an array
        of ['http_verb'=>'callable']
    
What does it return?
   null/undefined
```

###Router->get_route_handlers
```
What is it?
    Returns the list of route handlers
    registers with this root
    
What does it take?

    
What does it return?
   Array:   Router->route_handlers
        An array of routes and handlers:
            ['/albums'=>Route]
```

###Router->get_route_handler
```
What is it?
    Gets a single handler for a given
    WordPress route
    
What does it take?

    String: $route
        The WordPress style route string
    
What does it return?
   BeardedRoute:
        The Route handler attached to this route
```



#Contribution

Many hands make for light work. Pitch in and let's build something cool?

#License

Copy/pasta of WordPress's license page.