<div id="page">
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

  <?php if ($page['navigation']): ?>
  <nav id="navigation" role="navigation" class="container">
    <div class="container__inner">
      <div class="grid">
        <?php print render($page['navigation']); ?>
      </div>
    </div>
  </nav>
  <?php endif; ?>

  <?php if ($page['top']): ?>
  <div id="top" class="container">
    <div class="container__inner">
      <div class="grid">
        <?php print render($page['top']); ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

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

          <?php if ($page['content_top']): ?>
          <div id="content-top class="grid">
            <?php print render($page['content_top']); ?>
          </div>
          <?php endif; ?>

          <?php if ($page['content']): ?>
          <div id="content" class="grid">
            <?php print render($page['content']); ?>
          </div>
          <?php endif; ?>

          <?php print $feed_icons; ?>

          <?php if ($page['content_bottom']): ?>
          <div id="content-bottom" class="grid">
            <?php print render($page['content_bottom']); ?>
          </div>
          <?php endif; ?>
        </div>

        <?php if ($page['secondary']): ?>
          <div id="secondary" class="grid__cell">
            <div class="grid">
              <?php print render($page['secondary']); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($page['tertiary']): ?>
          <div id="tertiary" class="grid__cell">
            <div class="grid">
              <?php print render($page['tertiary']); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>


  <?php if ($page['bottom']): ?>
  <div id="bottom" class="container">
    <div class="container__inner">
      <div class="grid">
        <?php print render($page['bottom']); ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($page['footer']): ?>
  <footer id="footer" class="container">
    <div class="container__inner">
      <div class="grid">
        <?php print render($page['footer']); ?>
      </div>
    </div>
  </footer>
  <?php endif; ?>
</div>
