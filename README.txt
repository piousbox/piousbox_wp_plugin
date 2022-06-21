
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

 docker build . -t piousbox_wp_plugin_tester

 composer update
 ./bin/install-wp-tests.sh piousbox_wp_plugin_test root <local-root-passwd>
 phpunit

 docker exec <container-id> bash -c "cd /var/www/html/wp-content/plugins/piousbox_wp_plugin ; ./vendor/bin/phpunit"
 docker run piousbox_wp_plugin_tester "cd /var/www/html/wp-content/plugins/piousbox_wp_plugin ; ./vendor/bin/phpunit"

 docker run --mount type=bind,source="$(pwd)",target=/opt/projects/php/piousbox_wp_plugin \
   piousbox_wp_plugin_tester ./vendor/bin/phpunit
   
 docker run --mount type=bind,source="$(pwd)",target=/opt/projects/php/piousbox_wp_plugin piousbox_wp_plugin_tester pwd

# Deploy
Take a look at what's in the scripts/ folder.

Changelog.txt is accurate



