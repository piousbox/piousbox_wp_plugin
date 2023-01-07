<?php
/**
 * Plugin Name: Piousbox Wordpress Plugin
**/
function my_scripts() {
  wp_register_style('myLoginCss', '/wp-admin/css/login.min.css?v=1.3.0');
  wp_enqueue_style( 'myLoginCss');

  wp_register_style('myCss', plugins_url('piousbox_wp_plugin/style.css?v=1.3.0'));
  wp_enqueue_style( 'myCss');
}
add_action( 'wp_enqueue_scripts', 'my_scripts' );

// add_action('login_form_register', function () { // debug
//   var_dump( $_POST );
//   exit();
// });

/* From: https://wordpress.stackexchange.com/questions/21765/redirect-to-custom-url-when-registration-fails */
// function binda_register_fail_redirect( $sanitized_user_login, $user_email, $errors ){
//   //this line is copied from register_new_user function of wp-login.php
//   $errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );
//   //this if check is copied from register_new_user function of wp-login.php
//   if ( $errors->get_error_code() ){
//     //setup your custom URL for redirection
//     $redirect_url = get_bloginfo('url') . '/w/login';
//     //add error codes to custom redirection URL one by one
//     foreach ( $errors->errors as $e => $m ){
//         $redirect_url = add_query_arg( $e, '1', $redirect_url );
//     }
//     //add finally, redirect to your custom page with all errors in attributes
//     wp_redirect( $redirect_url );
//     exit;
//   }
// }
// add_action('register_post', 'binda_register_fail_redirect', 99, 3);

/**
 * [catlist parent="technique" parent_id=1||null ]
**/
function catlist_func( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'parent' => 'technique',
    'parent_id' => null
  ), $raw_attrs );

  if (!$attrs['parent_id']) {
    $this_parent = get_category_by_slug($attrs['parent']);
    $attrs['parent_id'] = $this_parent->term_id;
  }

  $args = array(
    'child_of'            => $attrs['parent_id'],
    'hierarchical'        => true,
    'order'               => 'ASC',
    'orderby'             => 'name',
    'show_count'          => 1,
    'use_desc_for_title'  => 1,
  );
  echo "<ul>";
  echo wp_list_categories( $args );
  echo "</ul>";
}
add_shortcode( 'catlist', 'catlist_func' );

/**
 *  myLogin
 * _vp_ 2022-12-29
 *
**/
function login_widget_shortcode() {
  $out =<<<EOT
    <div class='myLogin login'>

      <script src="https://www.google.com/recaptcha/api.js"></script>
      <script>
        function onSubmit(token) {
          document.getElementById("registerform").submit();
        }
      </script>

      <h2>Login</h2>
      <form name="loginform" action="/wp-login.php" method="post">
        <p>
          <label for="user_login">Username or Email Address</label>
          <input type="text" name="log" id="user_login" aria-describedby="login_error" class="input" value="" size="20" autocapitalize="off">
        </p>

        <div class="user-pass-wrap">
          <label for="user_pass">Password</label>
          <div class="wp-pwd">
            <input type="password" name="pwd" id="user_pass" aria-describedby="login_error" class="input password-input" value="" size="20">
            <button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="Show password">
              <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
            </button>
          </div>
        </div>

        <div class='flex-row'>
          <div>
            <p class="forgetmenot">
              <input name="rememberme" type="checkbox" id="rememberme" value="forever">
              <label for="rememberme">Remember Me</label>
            </p>
            <p>
              <a href="/wp-login.php?action=lostpassword">Lost your password?</a>
            </p>
          </div>
          <div>
            <p class="submit">
              <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In">
              <input type="hidden" name="redirect_to" value="/wp-admin/">
              <input type="hidden" name="testcookie" value="1">
            </p>
          </div>
        </div>
      </form>

      <hr />
      <h2>Register</h2>
      <form name="registerform" id="registerform" action="/wp-login.php?action=register" method="post" novalidate="novalidate">
        <p>
          <label for="user_login">Username</label>
          <input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off">
        </p>
        <p>
          <label for="user_email">Email</label>
          <input type="email" name="user_email" id="user_email" class="input" value="" size="25">
        </p>
        <input type="hidden" name="redirect_to" value="/w/notice?checkemail=registered" >

        <div class='flex-row'>
          <p id="reg_passmail">Registration confirmation will be emailed to you.</p>
          <p class="submit">
            <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large g-recaptcha"
              value="Register"
              data-sitekey="6Lf5e7kjAAAAANBxV7SCqEl7eBZwy-ClQVZHxfY7"
              data-callback='onSubmit'
              data-action='submit'
            ></input>
          </p>
        </div>
      </form>

      <p>

      </p>
    </div>
EOT;
  return $out;
}
add_shortcode('login_widget', 'login_widget_shortcode' );

/**
 * 20200217
 * [scrum_widget]
**/
function category_expanded_widget_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'slug'       => 'scrum',
    'n_posts'    => 1,
    'show_title' => "yes",
    'show_meta'  => 'yes',
  ), $raw_attrs );
  $cat = get_category_by_slug( $attrs['slug'] );
  # var_dump( $attrs );
  $args = array(
    # 'offset'           => $attrs['idx'],
    'category'         => $cat->term_id,
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    // 'post_type'        => 'post',
    'post_status'      => 'publish',
    'numberposts'      => $attrs['n_posts'],
    'suppress_filters' => true
  );
  if ($attrs['n_posts'] != '0' && $attrs['n_posts'] != 0) {
    $args['numberposts'] = $attrs['n_posts'];
  }
  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
  $postsRendered = '';
  foreach ($recent_posts as &$post) {
    $format   = get_post_format($post['ID']);
    $author   = get_the_author_meta('display_name', $post->author);
    $date     = substr($post['post_date'], 0, 10);
    $subtitle = new WP_Subtitle( $post['ID'] );
    $s        = $subtitle->get_subtitle();
    $content  = $post['post_content'];

    if ('video' == $format) {
      $video    = trim( strtok($content, "\n") );
      $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
      preg_match($pattern, $video, $matches);
      $content = <<<EOT
        <iframe width="420" height="315" src="https://www.youtube.com/embed/{$matches[1]}"></iframe>
EOT;
    }

    $title =<<<EOT
      <div>
        <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
      </div>
EOT;
    $title = $attrs['show_title'] == "yes" ? $title : '';

    $meta = "<div class='meta' >By $author on {$date}</div>";
    $meta = $attrs['show_meta'] == "yes" ? $meta : '';

    // _vp_ 2022-05-05 $meta would have been displayed just below the title.

    $tmp = <<<EOT
      <div>
        $title
        <div class="description">
          $content
        </div>
      </div>
EOT;
    $postsRendered = "$postsRendered$tmp<br /><br />";
  }

  $readMore = '<div style="text-align: center"><button>Read More</button></div>';
  $readMore = '';

  $out = <<<EOT
    <div class="CategoryWidget" >
      <h1 class="header" >Scrum</h1>
        $postsRendered
        $readMore
    </div>
EOT;
  return $out;
}
add_shortcode('scrum_widget', 'category_expanded_widget_shortcode' );



/**
 * [category_widget slug='interviewing' n_posts=2 ]
 * Has subtitle, usually a pic.
 * Probably has body before "more" tag.
 * 2022-05-09 _vp_
**/
function category_widget_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'slug'       => 'tools',
    'n_posts'    => 1,
    'show_title' => "yes"
  ), $raw_attrs );
  $cat = get_category_by_slug( $attrs['slug'] );
  $cat_link = get_category_link( $cat->term_id );

  $title = '';
  if ($attrs['show_title'] == "yes") {
    $title = <<<EOT
      <div class='header'>
        <h1>
          <div class='line-1'></div>
          <a href='${cat_link}'>{$cat->name}</a>
        </h1>
      </div>
EOT;
  }

  $args = array(
    # 'offset'           => $attrs['idx'],
    # 'category'         => $cat->term_id, # and sub-cats
    'category__in' => [ $cat->term_id ], # only the parent cat
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
    'suppress_filters' => true
  );

  if ($attrs['n_posts'] != '0') {
    $args['numberposts'] = $attrs['n_posts'];
  }

  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );

  $postsRendered = '';
  foreach ($recent_posts as &$post) {

    // $author   = get_the_author_meta('display_name', $post->author);
    // $date     = substr($post['post_date'], 0, 10);
    // $meta = "<div class='meta' >By $author on {$date}</div>";

    $subtitle = new WP_Subtitle( $post['ID'] );
    $s = $subtitle->get_subtitle();

    $tmp = <<<EOT
      <div class='item-outer' >
        <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
        <div class="description"><a href="/index.php?p={$post['ID']}">$s</a></div>
      </div>
EOT;
    $postsRendered = "$postsRendered$tmp";
  }

  $cat_link = get_category_link( $cat->term_id );

  $title = "";
  if ($attrs['show_title'] == "yes") {
    $title = "<div class='header'><h1><div class='line-1'></div><a href='${cat_link}'>{$cat->name}</a></h1></div>";
  }

  $out = <<<EOT
    <div class="CategoryWidget">{$title}{$postsRendered}</div>
EOT;

  return $out;
}
add_shortcode( 'category_widget', 'category_widget_shortcode' );






/**
 * [category_toc_widget slug='interviewing']
 * Only titles of ALL posts in the category.
 * 2023-01-06 _vp_
**/
function category_toc_widget_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'slug'       => 'tools',
    'n_posts'    => 'ALL',
    'show_title' => "yes"
  ), $raw_attrs );
  $cat = get_category_by_slug( $attrs['slug'] );
  $cat_link = get_category_link( $cat->term_id );

  $title = '';
  if ($attrs['show_title'] == "yes") {
    $title = <<<EOT
      <div class='header'>
        <h1>
          <div class='line-1'></div>
          <a href='${cat_link}'>{$cat->name}</a>
        </h1>
      </div>
EOT;
  }

  $args = array(
    # 'offset'           => $attrs['idx'],
    # 'category'         => $cat->term_id, # and sub-cats
    'category__in' => [ $cat->term_id ], # only the parent cat
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
    'suppress_filters' => true,
    'posts_per_page' => -1, // From: https://stackoverflow.com/questions/21231683/wordpress-not-showing-more-than-10-posts
  );

  if ($attrs['n_posts'] != 'ALL') {
    $args['numberposts'] = $attrs['n_posts'];
  }

  $postsRendered = '';
  if ($attrs['n_posts'] == '0') {
    $postsRendered = "<a href='${cat_link}'>Click to see all</a>";
  } else {
    $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
    foreach ($recent_posts as &$post) {
      $author   = get_the_author_meta('display_name', $post->author);
      $date     = substr($post['post_date'], 0, 10);
      $meta = "<div class='meta' >By $author on {$date}</div>";
      $tmp = <<<EOT
        <li class='item-outer' ><h5><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h5>{$meta}</li>
EOT;
      $postsRendered = "$postsRendered$tmp";
    }
  }

  $cat_link = get_category_link( $cat->term_id );

  $title = "";
  if ($attrs['show_title'] == "yes") {
    $title = <<<EOT
      <div class='header'>
        <h1><div class='line-1'></div>
          <a href='${cat_link}'>{$cat->name} ({$cat->category_count}) </a>

        </h1>
      </div>
EOT;
  }

  $out = <<<EOT
    <div class="CategoryTocWidget">{$title}<ol>{$postsRendered}</ol></div>
EOT;

  return $out;
}
add_shortcode( 'category_toc_widget', 'category_toc_widget_shortcode' );






/**
 * [category_previews_widget slug='interviewing']
 * Whatever's before <more> is displayed. This is for print pages, detail.
 * 2023-01-06 _vp_
**/
function category_previews_widget_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'slug'       => 'tools',
    'n_posts'    => 'ALL',
    'show_title' => "yes"
  ), $raw_attrs );
  $cat = get_category_by_slug( $attrs['slug'] );
  $cat_link = get_category_link( $cat->term_id );

  $title = '';
  if ($attrs['show_title'] == "yes") {
    $title = <<<EOT
      <div class='header'>
        <h1>
          <div class='line-1'></div>
          <a href='${cat_link}'>{$cat->name}</a>
        </h1>
      </div>
EOT;
  }

  $args = array(
    # 'offset'           => $attrs['idx'],
    # 'category'         => $cat->term_id, # and sub-cats
    'category__in' => [ $cat->term_id ], # only the parent cat
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
    'suppress_filters' => true,
    'posts_per_page' => -1, // From: https://stackoverflow.com/questions/21231683/wordpress-not-showing-more-than-10-posts
  );

  if ($attrs['n_posts'] != 'ALL') {
    $args['numberposts'] = $attrs['n_posts'];
  }

  $postsRendered = '';
  if ($attrs['n_posts'] == '0') {
    $postsRendered = "<a href='${cat_link}'>Click to see all</a>";
  } else {
    $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
    foreach ($recent_posts as &$post) {
      $author   = get_the_author_meta('display_name', $post->author);
      $date     = substr($post['post_date'], 0, 10);
      $meta = "<div class='meta' >By $author on {$date}</div>";
      $content = get_post_field( 'post_content', $post['ID'] );
      $content_parts = get_extended( $content ); // From: https://wordpress.stackexchange.com/questions/149099/only-show-content-before-more-tag
      $this_length = strlen($content_parts['extended']);

      if ($this_length > 3) {
        $read_more = "<a href=/index.php?p={$post['ID']} >Read More &gt;&gt;&gt;</a>";
      } else {
        $read_more = 'FIN';
      }

      $tmp = <<<EOT
        <li class='item-outer' >
          <h5><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h5>
          {$meta}
          {$content_parts['main']}
          {$read_more}
        </li>
EOT;
      $postsRendered = "$postsRendered$tmp";
    }
  }

  $cat_link = get_category_link( $cat->term_id );

  $title = "";
  if ($attrs['show_title'] == "yes") {
    $title = <<<EOT
      <div class='header'>
        <h1><div class='line-1'></div>
          <a href='${cat_link}'>{$cat->name} ({$cat->category_count}) </a>
        </h1>
      </div>
EOT;
  }

  $out = <<<EOT
    <div class="CategoryPreviewsWidget">{$title}<ol>{$postsRendered}</ol></div>
EOT;

  return $out;
}
add_shortcode( 'category_previews_widget', 'category_previews_widget_shortcode' );



/*
 * [feature idx=0]
**/
function feature_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'idx' => 0
  ), $raw_attrs );

  $features_tag = 'Features';

  $args = array(
    'numberposts'      => 1,
    'offset'           => $attrs['idx'],
    'tag'              => $features_tag,
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
    'suppress_filters' => true
  );
  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
  $post = $recent_posts[0];
  $subtitle = new WP_Subtitle( $post['ID'] );
  $s = $subtitle->get_subtitle();
  $out = <<<EOT
    <div>
      <h1><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h1>
      <div class="description"><a href="/index.php?p={$post['ID']}">$s</a></div>
    </div>
EOT;
  return $out;
}
add_shortcode( 'feature', 'feature_shortcode' );

/**
 * Recent Posts
 * Also has subtitle.
 * _vp_ 2022-10-xx
**/
function recent_posts_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'n_posts' => 5
  ), $raw_attrs );

  $args = array(
    'numberposts' => $attrs['n_posts'],
    'orderby'     => 'post_date',
    'order'       => 'DESC',
    'post_type'   => 'post',
    'post_status'      => 'publish',
  );
  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
  $postsRendered = '';
  foreach ($recent_posts as &$post) {

    // var_dump($post);

    $subtitle = new WP_Subtitle( $post['ID'] );
    $author = get_the_author_meta('display_name', $post->author);
    $s = $subtitle->get_subtitle();
    $date = substr($post['post_date'], 0, 10);
    $tmp = <<<EOT
      <div class="item" >
        <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
        <div class="meta" >By $author on {$date}</div>
        <div class="description"><a href="/index.php?p={$post['ID']}">$s</a></div>
        <div class="divider"></div>
      </div>
EOT;
    $postsRendered = "$postsRendered$tmp";
  }

  $out = <<<EOT
    <div class="RecentPosts">
      <div class="header" >Recent Posts</div>
      {$postsRendered}
    </div>
EOT;

  return $out;
}
add_shortcode( 'recent_posts', 'recent_posts_shortcode' );

/**
 * CategoryVideo Widget
 * 20180707 _vp_
**/
function category_video_widget_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'slug'    => 'scrum-diary',
    'n_posts' => 1
  ), $raw_attrs );
  $cat = get_category_by_slug( $attrs['slug'] );
  $args = array(
    'numberposts'      => $attrs['n_posts'],
    'offset'           => $attrs['idx'],
    'category'         => $cat->term_id,
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
    'suppress_filters' => true
  );

  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );

  $postsRendered = '';
  foreach ($recent_posts as &$post) {
    $author   = get_the_author_meta('display_name', $post->author);
    $date     = substr($post['post_date'], 0, 10);
    $subtitle = new WP_Subtitle( $post['ID'] );
    $s        = $subtitle->get_subtitle();
    $content  = $post['post_content'];
    $video    = trim( strtok($content, "\n") );

    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
    preg_match( $pattern, $video, $matches);

    $content = "<div class='thumb'><img src='https://img.youtube.com/vi/{$matches[1]}/0.jpg' alt='' /></div>";

    $tmp = <<<EOT
      <div>
        <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
        <div class="meta" >By $author on {$date}</div>
        <div class="description">
          <a href="/index.php?p={$post['ID']}">$content</a><br />

          <ul class="actions">
            <li>
              <a href="/index.php?p={$post['ID']}">Play Video</a>
            </li>
          </ul>

        </div>
      </div>
EOT;
    $postsRendered = "$postsRendered$tmp<br /><br />";
  }

  $out = <<<EOT
    <div class="CategoryVideoWidget">
      <h1 class="header">{$cat->name}</h1>
      {$postsRendered}
    </div>
EOT;

  return $out;
}
add_shortcode( 'category_video_widget', 'category_video_widget_shortcode' );


/**
 * CategoryFull Widget
 * 20180707 _vp_
**/
function category_full_widget_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'slug'    => 'diary',
    'n_posts' => 1
  ), $raw_attrs );
  $cat = get_category_by_slug( $attrs['slug'] );
  $args = array(
    'numberposts'      => $attrs['n_posts'],
    # 'offset'           => $attrs['idx'],
    'category'         => $cat->term_id,
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
    'suppress_filters' => true
  );

  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );

  $postsRendered = '';
  foreach ($recent_posts as &$post) {
    $author   = get_the_author_meta('display_name', $post->author);
    $date     = substr($post['post_date'], 0, 10);
    $subtitle = new WP_Subtitle( $post['ID'] );
    $s        = $subtitle->get_subtitle();
    $content  = $post['post_content'];

    $tmp = <<<EOT
      <div>
        <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
        <div class="meta" >By $author on {$date}</div>
        <div class="description">
          <a href="/index.php?p={$post['ID']}">$content</a><br />
        </div>
      </div>
EOT;
    $postsRendered = "$postsRendered$tmp<br /><br />";
  }

  $out = <<<EOT
    <div class="CategoryVideoWidget">
      <h1 class="header">{$cat->name}</h1>
      {$postsRendered}
    </div>
EOT;

  return $out;
}
add_shortcode( 'category_full_widget', 'category_full_widget_shortcode' );

