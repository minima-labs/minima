<?php

/**
 * Implementation of hook_theme().
 */
function minima_theme() {
  $items = array();

  // Consolidate a variety of theme functions under a single template type.
  $items['block'] = array(
    'arguments' => array('block' => NULL),
    'template' => 'box',
    'path' => drupal_get_path('theme', 'minima') .'/templates',
  );

  return $items;
}

/**
 * Preprocess functions ========================================================
 */

/**
 * Preprocess variables for html.tpl.php
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case).
 */
function minima_preprocess_html(&$variables, $hook) {

  // Attributes for html element.
  $variables['html_attributes_array'] = array(
    'lang' => $variables['language']->language,
    'dir' => $variables['language']->dir,
  );

  // Send X-UA-Compatible HTTP header to force IE to use the most recent
  // rendering engine or use Chrome's frame rendering engine if available.
  // This also prevents the IE compatibility mode button to appear when using
  // conditional classes on the html tag.
  if (is_null(drupal_get_http_header('X-UA-Compatible'))) {
    drupal_add_http_header('X-UA-Compatible', 'IE=edge,chrome=1');
  }

  // Remove superfluous body classes provided by Drupal core.
  // This is a bit barok, but classes can always be added again if required.
  $variables['classes_array'] = array();

  // Add a class that tells us whether we're on the front page or not.
  $variables['classes_array'][] = $variables['is_front'] ? 'front' : 'not-front';

  // Add a class that tells us whether the page is viewed by an authenticated user or not.
  $variables['classes_array'][] = $variables['logged_in'] ? 'logged-in' : 'not-logged-in';

  // Add secondary/tertiary classes to help with layout.
  if (!empty($variables['page']['secondary'])) {
    $variables['classes_array'][] = 'secondary';
  }
  if (!empty($variables['page']['tertiary'])) {
    $variables['classes_array'][] = 'tertiary';
  }

  // Add class for each site section.
  if (!$variables['is_front']) {
    $variables['classes_array'][] = drupal_html_class('section-' . arg(0, request_path()));
  }
}

/**
 * Process variables for html.tpl.php
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
function minima_process_html(&$variables, $hook) {
  // Flatten out html_attributes.
  $variables['html_attributes'] = drupal_attributes($variables['html_attributes_array']);
  //$variables['html_attributes'] = join(' ', array($variables['html_attributes'], $variables['rdf_namespaces']));
}

/**
 * Override or insert variables in the html_tag theme function.
 */
function minima_process_html_tag(&$variables) {
  $tag = &$variables['element'];

  if ($tag['#tag'] == 'style' || $tag['#tag'] == 'script') {
    // Remove redundant type attribute and CDATA comments.
    unset($tag['#attributes']['type'], $tag['#value_prefix'], $tag['#value_suffix']);

    // Remove media="all" but leave others unaffected.
    if (isset($tag['#attributes']['media']) && $tag['#attributes']['media'] === 'all') {
      unset($tag['#attributes']['media']);
    }
  }
}

/**
 * Implement hook_html_head_alter().
 */
function minima_html_head_alter(&$head) {
  // Simplify the meta tag for character encoding.
  if (isset($head['system_meta_content_type']['#attributes']['content'])) {
    $content = $head['system_meta_content_type']['#attributes']['content'];
    $head['system_meta_content_type']['#attributes'] = array(
      'charset' => str_replace('text/html; charset=', '', $content),
    );
  }
}

/**
 * Preprocess variables for page.tpl.php
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case).
 */
function minima_preprocess_page(&$variables, $hook) {
  // Branding - logo.
  $logo_path = DRUPAL_ROOT . parse_url($variables['logo'], PHP_URL_PATH);
  if (file_exists($logo_path)) {
    $variables['branding_logo'] = theme('image', array(
      'path' => $variables['logo'],
      'alt' => $variables['site_name'] . "'s logo",
      'title' => NULL,
      'width' => NULL,
      'height' => NULL,
      'attributes' => array('class' => 'branding__logo'),
    ));
  }
  else {
    $variables['branding_logo'] = '';
  }

  // Branding - name.
  $name_wrapper = drupal_is_front_page() ? 'h1' : 'div';
  $variables['branding_name'] = '<' .$name_wrapper .' class="branding__name">'.
                                $variables['site_name'] .
                                '</' .$name_wrapper .'>';

  // Branding - slogan.
  $variables['branding_slogan'] = $variables['site_slogan'];

  // Layout classes
  $variables['primary_classes'] = '';
  $variables['secondary_classes'] = ' desk--one-quarter lap--one-third';
  $variables['tertiary_classes'] = ' desk--one-quarter desk--pull--three-quarters';

  if ($variables['page']['secondary'] && $variables['page']['tertiary']) {
    $variables['primary_classes'] = ' desk--one-half';
  }
  elseif ($variables['page']['secondary'] || $variables['page']['tertiary']) {
    $variables['primary_classes'] = ' desk--three-quarters';
  }

  if ($variables['page']['secondary']) {
    $variables['primary_classes'] .= ' lap--two-thirds';
  }
  if ($variables['page']['tertiary']) {
    $variables['primary_classes'] .= ' desk--push--one-quarter';
    $variables['secondary_classes'] .= ' desk--push--one-quarter';
  }
}

/**
 * Preprocess variables for block.tpl.php
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case).
 */
function minima_preprocess_block(&$variables, $hook) {
  $block = $variables['block'];

  $variables['attributes_array']['class'] = array('box');
  $variables['title_attributes_array']['class'] = array('box__title');
  $variables['content_attributes_array']['class'] = array('box__content');
  $variables['title'] = !empty($block->subject) ? $block->subject : '';

  // Add classes to attributes array.
  foreach ($variables['classes_array'] as $class) {
    // Ignore core block classes.
    if (strpos($class, 'block') !== 0) {
      $variables['attributes_array']['class'][] = $class;
    }
  }
}

/**
 * Preprocess variables for node.tpl.php
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case).
 */
function minima_preprocess_node(&$variables, $hook) {

}

/**
 * Preprocess variables for comment.tpl.php
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case).
 */
function minima_preprocess_comment(&$variables, $hook) {

}

/**
 * Preprocess variables for theme_menu_link
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case).
 */
function minima_preprocess_menu_link(&$variables, $hook) {
  $classes = array();

  // Prepend state classes with 's-' and remove others.
  foreach ($variables['element']['#attributes']['class'] as $class) {
    switch ($class) {
      case 'active-trail':
        $classes[] = 'is-active';
        break;
      case 'expanded':
        $classes[] = 'is-expanded';
        break;
      case 'collapsed':
        $classes[] = 'is-collapsed';
        break;
    }
  }
  $variables['element']['#attributes']['class'] = $classes;

  // Remove link classes.
  unset($variables['element']['#localized_options']['attributes']['class']);
}

/**
 * Preprocess variables for fieldset.tpl.php
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("fieldset" in this case).
 */
function minima_preprocess_fieldset(&$variables, $hook) {

}

/**
 * Implements template_preprocess_HOOK() for theme_menu_tree().
 */
function minima_preprocess_menu_tree(&$variables) {
  // Add default class for all menus.
  $variables['attributes_array']['class'][] = 'nav';

  // Merge attributes from the tree (these can be set in hook_block_view_alter).
  // See minima_block_view_alter().
  if (!empty($variables['tree']['#attributes'])) {
    $variables['attributes_array'] = array_merge_recursive(
      $variables['attributes_array'],
      $variables['tree']['#attributes']
    );
  }

  // Set the children (default functionality).
  $variables['tree'] = $variables['tree']['#children'];
}


/**
 * Theme overrides =============================================================
 */

/**
 * Overrides theme_menu_tree().
 */
function minima_menu_tree($variables) {
  return '<ul' . drupal_attributes($variables['attributes_array']) . '>' . $variables['tree'] . '</ul>';
}

/**
 * Overrides theme_breadcrumb().
 */
function minima_breadcrumb($variables) {
  if (!empty($variables['breadcrumb'])) {
    $breadcrumb = $variables['breadcrumb'];

    // Add separators if there is more than one item.
    if (($count = count($breadcrumb)) > 1) {
      for ($i = 0; $i < $count - 1; ++$i) {
        $breadcrumb[$i] .= '<i class="breadcrumb__separator icon--angle-right"></i>';
      }
    }

    $output = '<nav class="breadcrumb">';

    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .is-invisible.
    $output .= '<h2 class="is-invisible">' . t('You are here') . '</h2>';

    $output .= theme('item_list', array(
      'type' => 'ol',
      'items' => $breadcrumb,
      'attributes' => array(
        'class' => 'list--inline',
      ),
    )) . '</nav>';

    return $output;
  }
}

/**
 * Overrides theme_menu_local_tasks().
 *
 * Returns HTML for primary and secondary local tasks.
 *
 * @param $variables
 *   An associative array containing:
 *     - primary: (optional) An array of local tasks (tabs).
 *     - secondary: (optional) An array of local tasks (tabs).
 *
 * @ingroup themeable
 * @see menu_local_tasks()
 */
function minima_menu_local_tasks(&$variables) {
  $output = '';

  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<h2 class="is-invisible">' . t('Primary tabs') . '</h2>';
    $variables['primary']['#prefix'] .= '<ul class="nav nav--tabs">';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['primary']);
  }
  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<h2 class="is-invisible">' . t('Secondary tabs') . '</h2>';
    $variables['secondary']['#prefix'] .= '<ul class="nav nav--pills">';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['secondary']);
  }

  return $output;
}

/**
 * Overrides theme_menu_local_task().
 *
 * Returns HTML for a single local task link.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: A render element containing:
 *     - #link: A menu link array with 'title', 'href', and 'localized_options'
 *       keys.
 *     - #active: A boolean indicating whether the local task is active.
 *
 * @ingroup themeable
 */
function minima_menu_local_task($variables) {
  $link = $variables['element']['#link'];
  $link_text = $link['title'];

  if (!empty($variables['element']['#active'])) {
    // Add text to indicate active tab for non-visual users.
    $active = '<span class="is-invisible"> ' . t('(active tab)') . '</span>';

    // If the link does not contain HTML already, check_plain() it now.
    // After we set 'html'=TRUE the link will not be sanitized by l().
    if (empty($link['localized_options']['html'])) {
      $link['title'] = check_plain($link['title']);
    }
    $link['localized_options']['html'] = TRUE;
    $link_text = t('!local-task-title!active', array('!local-task-title' => $link['title'], '!active' => $active));
  }

  // Tab attributes.
  $attributes_array = array();
  if (!empty($variables['element']['#active'])) {
    $attributes_array['class'][] = 'is-active';
  }

  return '<li' . drupal_attributes($attributes_array) . '>' . l($link_text, $link['href'], $link['localized_options']) . "</li>\n";
}

/**
 * Alter hooks =================================================================
 */

/**
 * Implements hook_theme_registry_alter().
 */
function minima_theme_registry_alter(&$theme_registry) {
  // Unset defaut template_preprocess_menu_tree so we can access other tree
  // properties (such as #attributes) in our custom implementation.
  // See minima_preprocess_menu_tree().
  foreach ($theme_registry['menu_tree']['preprocess functions'] as $key => $value) {
    if ($value == 'template_preprocess_menu_tree') {
      unset($theme_registry['menu_tree']['preprocess functions'][$key]);
    }
  }
}

/**
 * Implements hook_block_view_alter().
 */
function minima_block_view_alter(&$data, $block) {
  // Add inline modifier class to menu blocks in navigation region.
  if ($block->region == 'navigation') {
    // Does this block contain a menu?
    $is_menu_block = ((
         $block->module == 'system'
      && in_array($block->delta, array_keys(menu_get_menus())))
      || in_array($block->module, array('menu', 'menu_block'))
    );

    if ($is_menu_block) {
      $content = &$data['content'];

      // The menu_block module puts content in #content.
      if ($block->module == 'menu_block') {
        $content = $content['#content'];
      }

      $content['#attributes']['class'][] = 'list--inline';
    }
  }
}

/**
 * Implements hook_node_view_alter().
 */
function minima_entity_view_alter(&$build) {
  // Tidy up classes on node/comment links.
  if (isset($build['links'])) {
    $classes = &$build['links']['#attributes']['class'];

    // Add inline list modifier class if this list should be inline.
    if (in_array('inline', $classes)) {
      array_unshift($classes, 'list--inline');
    }

    // Remove core drupal classes.
    $classes = array_diff($classes, array('links', 'inline'));
  }
}

/**
 * Implements hook_page_alter().
 */
function minima_page_alter(&$page) {
  //dpm($page);
}
