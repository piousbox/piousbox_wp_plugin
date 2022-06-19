<?php
/**
 * Plugin Name: Piousbox Wordpress Plugin
 */
function my_scripts() {
  wp_register_style('myCSS', plugins_url('piousbox_wp_plugin/style.css'));
  wp_enqueue_style( 'myCSS');
}
add_action( 'wp_enqueue_scripts', 'my_scripts' );

/*
 * [foobar]
 */
function foobar_func( $atts = [] ){
  return "foo and bar";
}
add_shortcode( 'foobar', 'foobar_func' );

/**
 * [catlist parent="technique" parent_id=1||null ]
 */
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
  // $raw_cats = get_categories( $args );
  echo "<ul>";
  echo wp_list_categories( $args );
  echo "</ul>";

  /*
  // in-memory shuffling of the categories
  $cats = array();
  foreach($cats as &$cat) { }
  var_dump( $cats );

  $r_cats = ""; // r for render
  foreach ($cats as &$cat) {
    $r_cats = $r_cats . "<li>".$cat->name."</li>";
  }
  $rendered = "<ul>".$r_cats."</ul>";
  return $rendered;
   */
}
add_shortcode( 'catlist', 'catlist_func' );

/**
 * 20200217
 * [scrum_widget]
 */
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
 * [category_widget slug='interviewing']
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
  $title = $attrs['show_title'] == "yes" ? "<h1 class='header'><a href='${cat_link}'><u>{$cat->name}</u></a></h1>" : "";

  $out = <<<EOT
    <div class="CategoryWidget">{$title}{$postsRendered}</div>
EOT;

  return $out;
}
add_shortcode( 'category_widget', 'category_widget_shortcode' );

/*
 * [feature idx=0]
 */
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
 */
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
 */
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
 */
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

