<div class="grid__cell">
  <div<?php print $attributes; ?>>
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
    <h2<?php print $title_attributes; ?>><?php print $title ?></h2>
    <?php endif;?>
    <?php print render($title_suffix); ?>
    <div<?php print $content_attributes; ?>>
      <?php print $content ?>
    </div>
  </div>
</div>
