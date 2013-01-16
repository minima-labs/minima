<header id="header" role="header" class="container">
  <div class="container__inner">
    <div class="grid">
      <div class="branding grid__cell">
        <?php print $branding_logo; ?>
        <?php print $branding_name; ?>
        <?php if ($branding_slogan): ?>
          <div class="branding__slogan"><?php print $branding_slogan; ?></div>
        <?php endif; ?>
      </div>

      <?php print render($page['header']); ?>
    </div>
  </div>
</header>

<?php print render($page['navigation']); ?>

<?php print render($page['top']); ?>

<div id="main" class="container">
  <div class="container__inner">
    <div class="grid">
      <div id="primary" class="grid__cell">
        <header>
          <?php if ($breadcrumb): ?>
          <div id="breadcrumb">
            <?php print $breadcrumb; ?>
          </div>
          <?php endif; ?>

          <?php if ($messages): ?>
          <div id="messages">
            <?php print $messages; ?>
          </div>
          <?php endif; ?>

          <?php print render($title_prefix); ?>
          <?php if ($title): ?>
          <h1 id="page-title"><?php print $title; ?></h1>
          <?php endif; ?>
          <?php print render($title_suffix); ?>

          <?php if ($tabs): ?>
          <div class="tabs">
            <?php print render($tabs); ?>
          </div>
          <?php endif; ?>

          <?php print render($page['help']); ?>

          <?php if ($action_links): ?>
            <ul class="action-links">
              <?php print render($action_links); ?>
            </ul>
          <?php endif; ?>
        </header>

        <?php print render($page['content_top']); ?>
        <?php print render($page['content']); ?>
        <?php print $feed_icons; ?>
        <?php print render($page['content_bottom']); ?>
      </div>

      <?php print render($page['secondary']); ?>
      <?php print render($page['tertiary']); ?>
    </div>
  </div>
</div>

<?php print render($page['bottom']); ?>
<?php print render($page['footer']); ?>
