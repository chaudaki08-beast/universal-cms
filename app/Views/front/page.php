<?php
/** @var array $sections */
$sections = $sections ?? [];

$padMap = ['none'=>'0','sm'=>'1.5rem','md'=>'3rem','lg'=>'5rem','xl'=>'7rem'];

foreach ($sections as $section):
    $type = $section['type'];
    $data = json_field($section['data']);
    $set  = json_field($section['settings']);
    $file = APP_PATH . '/Views/front/sections/' . preg_replace('/[^a-z]/', '', $type) . '.php';

    // ---- Resolve design / layout settings ----
    $align     = $set['align']      ?? 'inherit';      // left|center|right|inherit
    $width     = $set['width']      ?? 'boxed';        // boxed|narrow|full
    $textTheme = $set['text_theme'] ?? 'auto';         // auto|light|dark
    $bg        = trim($set['bg'] ?? '');
    $bgImage   = trim($set['bg_image'] ?? '');
    $cssClass  = trim($set['css_class'] ?? '');

    // Independent top/bottom padding (falls back to legacy single 'padding')
    $legacy    = $set['padding'] ?? 'lg';
    $padTop    = $set['pad_top']    ?? $legacy;
    $padBottom = $set['pad_bottom'] ?? $legacy;

    // ---- Build inline style + classes ----
    $styles = [];
    $styles[] = 'padding-top:'    . ($padMap[$padTop]    ?? $padMap['lg']);
    $styles[] = 'padding-bottom:' . ($padMap[$padBottom] ?? $padMap['lg']);
    if ($bg !== '')      { $styles[] = 'background-color:' . $bg; }
    if ($bgImage !== '') {
        $styles[] = "background-image:url('" . e(uploads_url($bgImage)) . "')";
        $styles[] = 'background-size:cover';
        $styles[] = 'background-position:center';
    }
    if (($align === 'left' || $align === 'center' || $align === 'right')) {
        $styles[] = 'text-align:' . $align;
    }

    $classes = ['cms-section', 'cms-' . preg_replace('/[^a-z]/', '', $type)];
    $classes[] = 'sec-width-' . $width;
    if ($textTheme === 'light') { $classes[] = 'sec-text-light'; }
    if ($textTheme === 'dark')  { $classes[] = 'sec-text-dark'; }
    if ($bgImage !== '' && !empty($set['bg_overlay'])) { $classes[] = 'sec-has-overlay'; }
    if ($align !== 'inherit')   { $classes[] = 'sec-align-' . $align; }
    if ($cssClass !== '')       { $classes[] = e($cssClass); }

    $overlayColor = $set['bg_overlay'] ?? '';
    ?>
    <section id="section-<?= (int) $section['id'] ?>"
             class="<?= implode(' ', $classes) ?>"
             style="<?= implode(';', $styles) ?>">
      <?php if ($bgImage !== '' && $overlayColor !== ''): ?>
        <div class="sec-overlay" style="background:<?= e($overlayColor) ?>"></div>
      <?php endif; ?>
      <div class="sec-inner">
      <?php
      if (is_file($file)) {
          require $file;          // has $data, $set in scope
      } else {
          echo '<div class="container"><em>Unknown section: ' . e($type) . '</em></div>';
      }
      ?>
      </div>
    </section>
<?php endforeach; ?>

<?php if (empty($sections)): ?>
  <div class="container section-pad text-center">
    <h2>This page has no sections yet.</h2>
    <p class="text-muted">Add sections from the admin page builder.</p>
  </div>
<?php endif; ?>
