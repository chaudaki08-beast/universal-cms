<?php
/**
 * Flexible Layout — front renderer.
 * Decodes the builder JSON and outputs responsive Bootstrap rows/columns
 * with content blocks. $data has 'heading' + 'builder' (JSON string).
 */
$d = $data;
$builder = json_decode($d['builder'] ?? '{}', true);
$rows = is_array($builder) ? ($builder['rows'] ?? []) : [];

$layouts = [
    '12' => ['12'], '6-6' => ['6','6'], '4-4-4' => ['4','4','4'],
    '3-3-3-3' => ['3','3','3','3'], '8-4' => ['8','4'], '4-8' => ['4','8'],
    '3-6-3' => ['3','6','3'], '9-3' => ['9','3'], '3-9' => ['3','9'],
];

/** Turn a YouTube/Vimeo URL (or raw embed) into a responsive iframe. */
$videoEmbed = function (string $url): string {
    $url = trim($url);
    if ($url === '') return '';
    if (stripos($url, '<iframe') !== false) return $url; // already embed code
    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([\w-]{11})~i', $url, $m)) {
        $src = 'https://www.youtube.com/embed/' . $m[1];
    } elseif (preg_match('~vimeo\.com/(\d+)~i', $url, $m)) {
        $src = 'https://player.vimeo.com/video/' . $m[1];
    } else {
        $src = $url;
    }
    return '<div class="ratio ratio-16x9"><iframe src="' . e($src) . '" frameborder="0" allowfullscreen loading="lazy"></iframe></div>';
};

/** Render one content block to safe HTML. */
$renderBlock = function (array $block) use ($videoEmbed): string {
    $type = $block['type'] ?? '';
    $b    = $block['data'] ?? [];
    $get  = fn($k, $def = '') => isset($b[$k]) ? $b[$k] : $def;

    switch ($type) {
        case 'heading':
            $lvl = in_array($get('level','h2'), ['h2','h3','h4','h5'], true) ? $get('level','h2') : 'h2';
            $al  = in_array($get('align','left'), ['left','center','right'], true) ? $get('align','left') : 'left';
            return "<$lvl class=\"flexb-heading\" style=\"text-align:" . e($al) . "\">" . e($get('text')) . "</$lvl>";

        case 'text':
            return '<div class="flexb-text rich-text">' . $get('html') . '</div>'; // sanitised on save

        case 'image':
            $src = $get('src'); if ($src === '') return '';
            $rounded = $get('rounded') === '1' ? 'style="border-radius:var(--radius)"' : '';
            return '<img class="flexb-image img-fluid" src="' . e(uploads_url($src)) . '" alt="' . e($get('alt')) . '" ' . $rounded . '>';

        case 'button':
            $txt = $get('text'); if ($txt === '') return '';
            $cls = $get('style','primary') === 'outline' ? 'btn btn-outline-dark' : 'btn btn-theme';
            return '<a class="flexb-button ' . $cls . '" href="' . e($get('link','#')) . '">' . e($txt) . '</a>';

        case 'icon':
            return '<div class="flexb-icon feature-card">'
                 . '<div class="feature-icon"><i class="fa ' . e($get('icon','fa-star')) . '"></i></div>'
                 . '<h4 class="feature-title">' . e($get('title')) . '</h4>'
                 . '<p class="feature-text">' . e($get('text')) . '</p></div>';

        case 'video':
            return '<div class="flexb-video">' . $videoEmbed((string) $get('embed')) . '</div>';

        case 'divider':
            return '<hr class="flexb-divider">';

        case 'spacer':
            $h = (int) $get('height', 30);
            return '<div class="flexb-spacer" style="height:' . $h . 'px"></div>';

        case 'html':
            return '<div class="flexb-html">' . $get('html') . '</div>'; // sanitised on save

        default:
            return '';
    }
};
?>
<div class="container">
  <?php if (!empty($d['heading'])): ?>
    <h2 class="section-heading text-center mb-5"><?= e($d['heading']) ?></h2>
  <?php endif; ?>

  <?php foreach ($rows as $row):
        $layout  = $row['layout'] ?? '12';
        $widths  = $layouts[$layout] ?? ['12'];
        $columns = $row['columns'] ?? [];
    ?>
    <div class="row g-4 flexb-row">
      <?php foreach ($columns as $i => $col):
            $w = $widths[$i] ?? '12'; ?>
        <div class="col-lg-<?= e($w) ?>">
          <?php foreach (($col['blocks'] ?? []) as $block): ?>
            <div class="flexb-block"><?= $renderBlock($block) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

  <?php if (empty($rows)): ?><p class="text-center text-muted">This layout is empty.</p><?php endif; ?>
</div>
