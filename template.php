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
 * Preprocess function for theme('link').
 *
 * Remove the 'active' class from anchor tags since they are not required.
 */
function minima_preprocess_link(&$variables) {
  if (!empty($variables['options']['attributes']['class'])) {
    $class = &$variables['options']['attributes']['class'];

    if (is_array($class)) {
      $class = array_diff($class, array('active'));
    }
    elseif (strstr($class, 'active')) {
      $class = str_replace('active', '', $class);
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
 * Overrides theme_item_list().
 *
 * Returns HTML for a list or nested list of items.
 *
 * @param $variables
 *   An associative array containing:
 *   - items: An array of items to be displayed in the list. If an item is a
 *     string, then it is used as is. If an item is an array, then the "data"
 *     element of the array is used as the contents of the list item. If an item
 *     is an array with a "children" element, those children are displayed in a
 *     nested list. All other elements are treated as attributes of the list
 *     item element.
 *   - title: The title of the list.
 *   - type: The type of list to return (e.g. "ul", "ol").
 *   - attributes: The attributes applied to the list element.
 */
function minima_item_list($variables) {
  $output = '';
  $items = $variables['items'];
  $title = $variables['title'];
  $type = $variables['type'];
  $attributes = $variables['attributes'];

  // Only output the list container and title, if there are any list items.
  // Check to see whether the block title exists before adding a header.
  // Empty headers are not semantic and present accessibility challenges.
  if (isset($title) && $title !== '') {
    $output .= '<h3>' . $title . '</h3>';
  }

  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';
    $num_items = count($items);
    $i = 0;
    foreach ($items as $item) {
      $attributes = array();
      $children = array();
      $data = '';
      $i++;
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {
        // Render nested list.
        $data .= theme('item_list', array('items' => $children, 'title' => NULL, 'type' => $type, 'attributes' => $attributes));
      }
      if ($i == 1) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_items) {
        $attributes['class'][] = 'last';
      }
      $output .= '<li' . drupal_attributes($attributes) . '>' . $data . "</li>\n";
    }
    $output .= "</$type>";
  }

  return $output;
}

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

    $output = '<div class="breadcrumb">';

    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .is-invisible.
    $output .= '<h2 class="is-invisible">' . t('You are here') . '</h2>';

    $output .= theme('item_list', array(
      'items' => $breadcrumb,
      'type' => 'ol',
    )) . '</div>';

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
 * Overrides theme_status_messages().
 */
function minima_status_messages($variables) {
  $display = $variables['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
  );

  $icon_class = array(
    'status' => 'icon--info-sign',
    'error' => 'icon--exclamation-sign',
    'warning' => 'icon--warning-sign',
  );

  foreach (drupal_get_messages($display) as $type => $messages) {
    $output .= "<div class=\"messages is-$type\">\n";
    if (!empty($status_heading[$type])) {
      $output .= '<h2 class="is-invisible">' . $status_heading[$type] . "</h2>\n";
    }
    if (!empty($icon_class[$type])) {
      $output .= '<i class="messages__icon ' . $icon_class[$type] . "\"></i>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul class=\"messages__content\">\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= '<div class="messages__content">' . $messages[0] . '</div>';
    }
    $output .= "</div>\n";
  }

  if (!empty($output)) {
    $output = '<div id="messages">' . $output . '</div>';
  }

  return $output;
}


/**
 * Overides theme_form_element().
 *
 * Returns HTML for a form element.
 *
 * Each form element is wrapped in a DIV container having the following CSS
 * classes:
 * - form-item: Generic for all form elements.
 * - form-type-#type: The internal element #type.
 * - form-item-#name: The internal form element #name (usually derived from the
 *   $form structure and set via form_builder()).
 * - form-disabled: Only set if the form element is #disabled.
 *
 * In addition to the element itself, the DIV contains a label for the element
 * based on the optional #title_display property, and an optional #description.
 *
 * The optional #title_display property can have these values:
 * - before: The label is output before the element. This is the default.
 *   The label includes the #title and the required marker, if #required.
 * - after: The label is output after the element. For example, this is used
 *   for radio and checkbox #type elements as set in system_element_info().
 *   If the #title is empty but the field is #required, the label will
 *   contain only the required marker.
 * - invisible: Labels are critical for screen readers to enable them to
 *   properly navigate through forms but can be visually distracting. This
 *   property hides the label for everyone except screen readers.
 * - attribute: Set the title attribute on the element to create a tooltip
 *   but output no label element. This is supported only for checkboxes
 *   and radios in form_pre_render_conditional_form_element(). It is used
 *   where a visual label is not needed, such as a table of checkboxes where
 *   the row and column provide the context. The tooltip will include the
 *   title and required marker.
 *
 * If the #title property is not set, then the label and any required marker
 * will not be output, regardless of the #title_display or #required values.
 * This can be useful in cases such as the password_confirm element, which
 * creates children elements that have their own labels and required markers,
 * but the parent element should have neither. Use this carefully because a
 * field without an associated label can cause accessibility challenges.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #title_display, #description, #id, #required,
 *     #children, #type, #name.
 *
 * @ingroup themeable
 */
function minima_form_element($variables) {
  $element = &$variables['element'];

  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  $attributes['class'] = array('form-item');

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }

  $prefix = '';
  $suffix = '';
  $field_attributes = array('class' => array('form-item__field'));
  foreach (array('prefix', 'suffix') as $fix) {
    if (!empty($element["#field_$fix"])) {
      if ($element["#field_$fix"] == strip_tags($element["#field_$fix"])) {
        $field_attributes['class'][] = 'form-item__field--' . $fix;
        $$fix = '<span class="field-item__' . $fix . '">' . trim($element["#field_$fix"]) . '</span> ';
      }
      else {
        $$fix = $element["#field_$fix"];
      }
    }
  }

  $output = '<div' . drupal_attributes($attributes) . '>';

  if (in_array($element['#title_display'], array('before', 'invisible'))) {
    $output .= '  ' . theme('form_element_label', $variables);
  }

  $output .= '<div' . drupal_attributes($field_attributes) . '>' . "\n";

  $output .= '  ' . $prefix . $element['#children'] . $suffix . "\n";


  if ($element['#title_display'] == 'after') {
    $output .= '  ' . theme('form_element_label', $variables) . "\n";
  }

  if (!empty($element['#description'])) {
    $output .= '<div class="form-item__help">' . $element['#description'] . "</div>\n";
  }

  $output .= "</div>";
  $output .= "</div>\n";

  return $output;
}

/**
 * Returns HTML for a marker for required form elements.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *
 * @ingroup themeable
 */
function minima_form_required_marker($variables) {
  // This is also used in the installer, pre-database setup.
  $t = get_t();
  $attributes = array(
    'class' => 'is-required',
    'title' => $t('This field is required.'),
  );
  return '<span' . drupal_attributes($attributes) . '>*</span>';
}

/**
 * Returns HTML for a form element label and required marker.
 *
 * Form element labels include the #title and a #required marker. The label is
 * associated with the element itself by the element #id. Labels may appear
 * before or after elements, depending on theme_form_element() and
 * #title_display.
 *
 * This function will not be called for elements with no labels, depending on
 * #title_display. For elements that have an empty #title and are not required,
 * this function will output no label (''). For required elements that have an
 * empty #title, this will output the required marker alone within the label.
 * The label will use the #id to associate the marker with the field that is
 * required. That is especially important for screenreader users to know
 * which field is required.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #required, #title, #id, #value, #description.
 *
 * @ingroup themeable
 */
function minima_form_element_label($variables) {
  $element = $variables['element'];
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '') && empty($element['#required'])) {
    return '';
  }

  // If the element is required, a required marker is appended to the label.
  $required = !empty($element['#required']) ? theme('form_required_marker', array('element' => $element)) : '';

  $title = filter_xss_admin($element['#title']);

  $attributes = array('class' => array('form-item__label'));
  // Style the label as class option to display inline with the element.
  if ($element['#title_display'] == 'after') {
    $attributes['class'] = 'form-item__label--option';
  }
  // Show label only to screen readers to avoid disruption in visual flows.
  elseif ($element['#title_display'] == 'invisible') {
    $attributes['class'] = 'is-invisible';
  }

  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  return '<label' . drupal_attributes($attributes) . '>' . $t('!title !required', array('!title' => $title, '!required' => $required)) . "</label>";
}


/**
 * Returns HTML for a fieldset form element and its children.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #attributes, #children, #collapsed, #collapsible,
 *     #description, #id, #title, #value.
 *
 * @ingroup themeable
 */
function minima_fieldset($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id'));

  $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    $output .= '<legend>' . $element['#title'] . '</legend>';
  }
  if (!empty($element['#description'])) {
    $output .= '<div class="fieldset__help">' . $element['#description'] . '</div>';
  }
  $output .= $element['#children'];
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  $output .= "</fieldset>\n";
  return $output;
}

/**
 * Returns HTML for a single local action link.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: A render element containing:
 *     - #link: A menu link array with 'title', 'href', and 'localized_options'
 *       keys.
 *
 * @ingroup themeable
 */
function minima_menu_local_action($variables) {
  $link = $variables['element']['#link'];

  // Add button classes.
  $options = isset($link['localized_options']) ? $link['localized_options'] : array();
  $options['attributes']['class'][] = 'button button--small';

  // Add plus icon.
  $options['html'] = TRUE;
  $link['title'] = '<span class="icon--plus"></span> ' . $link['title'];

  $output = '<li>';
  if (isset($link['href'])) {
    $output .= l($link['title'], $link['href'], $options);
  }
  elseif (!empty($link['localized_options']['html'])) {
    $output .= $link['title'];
  }
  else {
    $output .= check_plain($link['title']);
  }
  $output .= "</li>\n";

  return $output;
}

/**
 * Override of theme_pager().
 */
function minima_pager($variables) {
  global $pager_total;

  $element = $variables['element'];
  $pager_max = $pager_total[$element];

  if ($pager_max <= 1) {
    return '';
  }

  global $pager_page_array;

  $output = '';
  $tags = $variables['tags'];
  $parameters = $variables['parameters'];
  $quantity = $variables['quantity'];

  // Determine which pages we're running to/from.
  $pager_middle = ceil($quantity / 2);
  $pager_current = $pager_page_array[$element] + 1;
  $pager_first = $pager_current - $pager_middle + 1;
  $pager_last = $pager_current + $quantity - $pager_middle;

  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Don't go past the last page.
    $i += $pager_max - $pager_last;
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Similarly, don't output pages before the first.
    $i = 1;
    $pager_last = $pager_last + (1 - $i);
  }

  // Only generate the pager items if there is more than one page.
  if ($i != $pager_max) {
    $links['pager-first'] = theme('pager_first', array(
      'text' => isset($tags[0]) ? $tags[0] : t('First'),
      'element' => $element,
      'parameters' => $parameters,
    ));

    $links['pager-previous'] = theme('pager_previous', array(
      'text' => isset($tags[1]) ? $tags[1] : t('Prev'),
      'element' => $element,
      'interval' => 1,
      'parameters' => $parameters,
    ));

    for ($i; $i <= $pager_last && $i <= $pager_max; $i++) {
      if ($i < $pager_current) {
        $links["pager-item"] = theme('pager_previous', array(
          'text' => $i,
          'element' => $element,
          'interval' => $pager_current - $i,
          'parameters' => $parameters,
        ));
      }

      if ($i == $pager_current) {
        $links["pager-current"] = array('title' => $i);
      }

      if ($i > $pager_current) {
        $links["pager-item"] = theme('pager_next', array(
          'text' => $i,
          'element' => $element,
          'interval' => $i - $pager_current,
          'parameters' => $parameters,
        ));
      }
    }

    $links['pager-next'] = theme('pager_next', array(
      'text' => isset($tags[3]) ? $tags[3] : t('Next'),
      'element' => $element,
      'interval' => 1,
      'parameters' => $parameters,
    ));

    $links['pager-last'] = theme('pager_last', array(
      'text' => isset($tags[4]) ? $tags[4] : t('Last'),
      'element' => $element,
      'parameters' => $parameters,
    ));

    $output = theme('links', array(
      'links' => array_filter($links),
      'attributes' => array(
        'class' => 'pager list--inline',
      ),
    ));
  }

  return $output;
}

/**
 * Override of theme_pager_link().
 *
 * Unforunately theme_pager_link() doesn't use l() to generate markup. Return an
 * array suitable for theme_links() rather than marked up HTML.
 */
function minima_pager_link($variables) {
  $text = $variables['text'];
  $page_new = $variables['page_new'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $attributes = $variables['attributes'];

  $page = isset($_GET['page']) ? $_GET['page'] : '';
  if ($new_page = implode(',', pager_load_array($page_new[$element], $element, explode(',', $page)))) {
    $parameters['page'] = $new_page;
  }

  $query = array();
  if (count($parameters)) {
    $query = drupal_get_query_parameters($parameters, array());
  }
  if ($query_pager = pager_get_query_parameters()) {
    $query = array_merge($query, $query_pager);
  }

  // Set each pager link title
  if (!isset($attributes['title'])) {
    static $titles = NULL;
    if (!isset($titles)) {
      $titles = array(
        t('« first') => t('Go to first page'),
        t('‹ previous') => t('Go to previous page'),
        t('next ›') => t('Go to next page'),
        t('last »') => t('Go to last page'),
      );
    }
    if (isset($titles[$text])) {
      $attributes['title'] = $titles[$text];
    }
    elseif (is_numeric($text)) {
      $attributes['title'] = t('Go to page @number', array('@number' => $text));
    }
  }

  return array(
    'title' => $text,
    'href' => $_GET['q'],
    'attributes' => $attributes,
    'query' => count($query) ? $query : NULL,
  );
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
