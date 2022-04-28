
# About

A plugin for some shared functionality for piousbox.com

# Develop
### REPL
<pre>
 php -a
 php > require 'scripts/repl.php';
 php > print_r(get_user_by('id', 1));
</pre>

 wp scaffold plugin-tests sample-plugin

# Test


 ./bin/install-wp-tests.sh piousbox_wp_plugin_test root <local-root-passwd>
 phpunit


# Deploy
Take a look at what's in the scripts/ folder.

Changelog.txt is accurate



