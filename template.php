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
}

/**
 * Preprocess variables for region.tpl.php
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("region" in this case).
 */
function minima_preprocess_region(&$variables, $hook) {
  $variables['attributes_array']['id'] = drupal_html_id($variables['region']);
  //$variables['attributes_array']['class'] = $variables['classes_array'];

  // Set default wrapper.
  $variables['wrapper'] = 'div';
  $variables['container'] = FALSE;
  $variables['grid_cell'] = FALSE;

  switch ($variables['region']) {
    case 'header':
      $variables['wrapper'] = FALSE;
      break;

    case 'navigation':
      $variables['wrapper'] = 'nav';
      $variables['container'] = TRUE;
      $variables['attributes_array']['role'] = 'navigation';
      break;

    case 'secondary':
    case 'tertiary':
      $variables['grid_cell'] = TRUE;
      $variables['attributes_array']['class'][] = 'grid__cell';
      break;

    case 'top':
    case 'bottom':
      $variables['container'] = TRUE;
      break;

    case 'footer':
      $variables['wrapper'] = 'footer';
      $variables['container'] = TRUE;
      break;

    case 'page_top':
    case 'page_bottom':
      array_unshift($variables['theme_hook_suggestions'], 'region__no_wrapper');
      break;
  }

  if (!$variables['container'] && !$variables['grid_cell']) {
    $variables['attributes_array']['class'][] = 'grid';
  }

  if ($variables['container']) {
    $variables['attributes_array']['class'][] = 'container';
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

  $variables['attributes_array']['class'] = array('grid__cell', 'box');
  $variables['title_attributes_array']['class'] = array('box__title');
  $variables['content_attributes_array']['class'] = array('box__content');
  $variables['title'] = !empty($block->subject) ? $block->subject : '';

  // Add modifier class for menus.
  if (($block->module == 'system'
    && in_array($block->delta, array_keys(menu_get_menus())))
    || in_array($block->module, array('menu', 'menu_block'))
  ) {
    $variables['attributes_array']['class'][] = 'box--menu';
  }

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
  $classes = array('menu__item');

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
 * Implements hook_page_alter().
 */
function minima_page_alter(&$page) {
  //dpm($page);
}
