#Bearded Routing

> Because why not make WordPress routing look and feel like Express?

This is a silly abstraction on top of the WordPress REST API. This was a fun experience learning how WordPress API worked and how we create routes and callbacks for those routes.

This is a **work in progress** and the API might change. Any and all PRs and issues are welcome!

##Install

This is made to work as a WordPress plugin but can be copy/pasted into your own projects. If you download this and activate it as a plugin in WordPress, it will expose BeardedRouter and BeardedRoute as classes to interact with the WP_REST classes. 

[See Documentation Here](./docs/index.html)

_**Running Tests**_

This throws errors because we don't understand PHP and WordPress well enough... Not tests for now.
```
$ ./vendor/bin/phpunit --bootstrap ./bearded-routing.php tests/BeardedRoute.spec 

```

#Contribution

Many hands make for light work. Pitch in and let's build something cool?

#License

Copy/pasta of WordPress's license page.