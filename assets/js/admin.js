/* Universal CMS — Admin behaviour: builder, repeaters, media picker */
(function () {
  'use strict';
  var CSRF = (document.querySelector('meta[name=csrf-token]') || {}).content || '';
  var BASE = document.body.getAttribute('data-base') || '';

  function url(path){ return (window.CMS_BASE || '') + path; }

  /* ---- Collapsible builder sections ---- */
  document.addEventListener('click', function (e) {
    var head = e.target.closest('.builder-head');
    if (head && !e.target.closest('.actions')) {
      var body = head.parentElement.querySelector('.builder-body');
      if (body) body.classList.toggle('open');
    }
  });

  /* ---- Sortable: page sections ---- */
  var secList = document.getElementById('sectionList');
  if (secList && window.Sortable) {
    Sortable.create(secList, {
      handle: '.builder-head', animation: 150,
      onEnd: function () {
        var ids = Array.from(secList.querySelectorAll('[data-section-id]')).map(function (el) { return el.getAttribute('data-section-id'); });
        fetch(window.CMS_REORDER_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
          body: ids.map(function (id, i) { return 'order[' + i + ']=' + encodeURIComponent(id); }).join('&')
        });
      }
    });
  }

  /* ---- Sortable: menu items ---- */
  var menuList = document.getElementById('menuItemList');
  if (menuList && window.Sortable) {
    Sortable.create(menuList, {
      handle: '.drag', animation: 150,
      onEnd: function () {
        var ids = Array.from(menuList.querySelectorAll('[data-item-id]')).map(function (el) { return el.getAttribute('data-item-id'); });
        fetch(window.CMS_MENU_REORDER_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
          body: ids.map(function (id, i) { return 'order[' + i + ']=' + encodeURIComponent(id); }).join('&')
        });
      }
    });
  }

  /* ---- Repeater add/remove ---- */
  document.addEventListener('click', function (e) {
    var addBtn = e.target.closest('[data-repeater-add]');
    if (addBtn) {
      e.preventDefault();
      var wrap = addBtn.closest('.repeater');
      var tpl = wrap.querySelector('template');
      var idx = wrap.querySelectorAll('.repeater-item').length;
      var html = tpl.innerHTML.replace(/__i__/g, idx);
      var holder = wrap.querySelector('.repeater-items');
      holder.insertAdjacentHTML('beforeend', html);
    }
    var rm = e.target.closest('.remove-item');
    if (rm) { e.preventDefault(); rm.closest('.repeater-item').remove(); }
  });

  /* ---- Media picker ---- */
  var activeTarget = null, activeMode = 'single';
  window.openMediaPicker = function (inputId, mode) {
    activeTarget = inputId; activeMode = mode || 'single';
    var modalEl = document.getElementById('mediaModal');
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    loadMedia();
    modal.show();
  };

  function loadMedia(q) {
    var grid = document.getElementById('mediaModalGrid');
    if (!grid) return;
    grid.innerHTML = '<div class="text-center py-4 text-muted">Loading…</div>';
    fetch(window.CMS_PICKER_URL + (q ? ('?q=' + encodeURIComponent(q)) : ''))
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.items || !data.items.length) { grid.innerHTML = '<p class="text-muted text-center py-4">No media yet. Upload some in the Media Library.</p>'; return; }
        grid.innerHTML = data.items.map(function (m) {
          var isImg = (m.mime || '').indexOf('image') === 0;
          return '<div class="media-tile" style="cursor:pointer" data-path="' + m.path + '" data-url="' + m.url + '">' +
            (isImg ? '<img src="' + m.url + '">' : '<div class="m-name p-3"><i class="fa fa-file"></i></div>') +
            '<div class="m-name">' + m.name + '</div></div>';
        }).join('');
      });
  }

  document.addEventListener('click', function (e) {
    var tile = e.target.closest('#mediaModalGrid .media-tile');
    if (tile && activeTarget) {
      var path = tile.getAttribute('data-path');
      var url = tile.getAttribute('data-url');
      if (activeMode === 'multi') {
        addGalleryImage(activeTarget, path, url);
      } else {
        var input = document.getElementById(activeTarget);
        if (input) { input.value = path; }
        var prev = document.getElementById(activeTarget + '_preview');
        if (prev) { prev.src = url; prev.style.display = 'block'; }
      }
      bootstrap.Modal.getInstance(document.getElementById('mediaModal')).hide();
    }
  });

  var searchBox = document.getElementById('mediaModalSearch');
  if (searchBox) {
    var t; searchBox.addEventListener('input', function () { clearTimeout(t); t = setTimeout(function () { loadMedia(searchBox.value); }, 300); });
  }

  /* ---- Gallery multi-image field ---- */
  window.addGalleryImage = function (containerId, path, url) {
    var c = document.getElementById(containerId);
    if (!c) return;
    var name = c.getAttribute('data-name');
    var div = document.createElement('div');
    div.className = 'gallery-thumb position-relative';
    div.style.cssText = 'width:90px;height:70px;display:inline-block;margin:4px';
    div.innerHTML = '<img src="' + url + '" style="width:100%;height:100%;object-fit:cover;border-radius:8px">' +
      '<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 px-1 remove-gallery">&times;</button>' +
      '<input type="hidden" name="' + name + '" value="' + path + '">';
    c.appendChild(div);
  };
  document.addEventListener('click', function (e) {
    if (e.target.closest('.remove-gallery')) { e.target.closest('.gallery-thumb').remove(); }
  });

  /* =========================================================
     Flexible Layout — drag & drop block builder
     ========================================================= */
  var FLEX_LAYOUTS = {
    '12':['12'],'6-6':['6','6'],'4-4-4':['4','4','4'],'3-3-3-3':['3','3','3','3'],
    '8-4':['8','4'],'4-8':['4','8'],'3-6-3':['3','6','3'],'9-3':['9','3'],'3-9':['3','9']
  };
  var FLEX_LABELS = { heading:'Heading', text:'Text', image:'Image', button:'Button',
    icon:'Icon Box', video:'Video', divider:'Divider', spacer:'Spacer', html:'HTML' };
  var flexUid = 0;

  function esc(v){ return String(v == null ? '' : v).replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  function flexFields(type, d){
    d = d || {};
    var sel = function(field, opts, cur){
      return '<select data-field="'+field+'" class="form-select form-select-sm">' +
        opts.map(function(o){ return '<option value="'+o[0]+'"'+(cur===o[0]?' selected':'')+'>'+o[1]+'</option>'; }).join('') + '</select>';
    };
    switch(type){
      case 'heading':
        return '<input data-field="text" class="form-control form-control-sm mb-2" placeholder="Heading text" value="'+esc(d.text)+'">' +
          '<div class="row gx-2"><div class="col-6">'+sel('level',[['h2','H2'],['h3','H3'],['h4','H4'],['h5','H5']],d.level||'h2')+'</div>' +
          '<div class="col-6">'+sel('align',[['left','Left'],['center','Center'],['right','Right']],d.align||'left')+'</div></div>';
      case 'text':
        return '<textarea data-field="html" class="form-control form-control-sm" rows="3" placeholder="Text (HTML allowed)">'+esc(d.html)+'</textarea>';
      case 'image':
        var id = 'fleximg-'+(++flexUid);
        return '<div class="media-input-group mb-2"><input type="text" id="'+id+'" data-field="src" class="form-control form-control-sm" value="'+esc(d.src)+'" placeholder="No image" readonly>' +
          '<button type="button" class="btn btn-sm btn-outline-secondary" data-pick="'+id+'"><i class="fa fa-image"></i></button></div>' +
          '<input data-field="alt" class="form-control form-control-sm" placeholder="Alt text" value="'+esc(d.alt)+'">';
      case 'button':
        return '<input data-field="text" class="form-control form-control-sm mb-2" placeholder="Button text" value="'+esc(d.text)+'">' +
          '<input data-field="link" class="form-control form-control-sm mb-2" placeholder="Link URL" value="'+esc(d.link||'#')+'">' +
          sel('style',[['primary','Solid'],['outline','Outline']],d.style||'primary');
      case 'icon':
        return '<input data-field="icon" class="form-control form-control-sm mb-2" placeholder="fa-star" value="'+esc(d.icon||'fa-star')+'">' +
          '<input data-field="title" class="form-control form-control-sm mb-2" placeholder="Title" value="'+esc(d.title)+'">' +
          '<textarea data-field="text" class="form-control form-control-sm" rows="2" placeholder="Text">'+esc(d.text)+'</textarea>';
      case 'video':
        return '<input data-field="embed" class="form-control form-control-sm" placeholder="YouTube / Vimeo URL" value="'+esc(d.embed)+'">';
      case 'spacer':
        return '<div class="input-group input-group-sm"><input type="number" data-field="height" class="form-control" value="'+esc(d.height||30)+'"><span class="input-group-text">px height</span></div>';
      case 'html':
        return '<textarea data-field="html" class="form-control form-control-sm" rows="3" placeholder="<!-- custom HTML -->">'+esc(d.html)+'</textarea>';
      case 'divider':
      default:
        return '<div class="text-muted small">Horizontal divider line.</div>';
    }
  }

  function flexMakeBlock(type, d){
    var el = document.createElement('div');
    el.className = 'flex-block';
    el.setAttribute('data-type', type);
    el.innerHTML =
      '<div class="block-head"><i class="fa fa-grip-vertical block-drag"></i>' +
      '<span class="block-name"><i class="fa fa-cube me-1"></i>'+(FLEX_LABELS[type]||type)+'</span>' +
      '<button type="button" class="block-del" title="Remove">&times;</button></div>' +
      '<div class="block-body">'+flexFields(type, d)+'</div>';
    return el;
  }

  function flexInitColSortable(col){
    if (!window.Sortable || col._sortable) return;
    col._sortable = Sortable.create(col, {
      group: 'flexblocks', handle: '.block-drag', animation: 150, draggable: '.flex-block',
      onAdd: function(evt){
        var item = evt.item;
        if (item.classList.contains('palette-item')) {
          var t = item.getAttribute('data-block');
          var blk = flexMakeBlock(t, {});
          item.parentNode.replaceChild(blk, item);
        }
      }
    });
  }

  function flexMakeColumn(blocks){
    var col = document.createElement('div');
    col.className = 'flex-col';
    (blocks || []).forEach(function(b){ col.appendChild(flexMakeBlock(b.type, b.data || {})); });
    flexInitColSortable(col);
    return col;
  }

  function flexAddRow(builder, layout, columnsData){
    var widths = FLEX_LAYOUTS[layout] || ['12'];
    var row = document.createElement('div');
    row.className = 'flex-row';
    row.setAttribute('data-layout', layout);

    var opts = Object.keys(FLEX_LAYOUTS).map(function(k){
      return '<option value="'+k+'"'+(k===layout?' selected':'')+'>'+k.replace(/-/g,' / ')+'</option>';
    }).join('');
    var head = document.createElement('div');
    head.className = 'flex-row-head';
    head.innerHTML = '<i class="fa fa-grip-vertical row-drag" title="Drag to reorder row"></i>' +
      '<span class="small text-muted me-2">Row</span>' +
      '<select class="form-select form-select-sm row-layout" style="width:auto">'+opts+'</select>' +
      '<button type="button" class="row-del ms-auto" title="Delete row"><i class="fa fa-trash"></i></button>';
    row.appendChild(head);

    var cols = document.createElement('div');
    cols.className = 'flex-cols';
    widths.forEach(function(w, i){
      var c = flexMakeColumn(columnsData && columnsData[i] ? columnsData[i].blocks : []);
      c.style.flex = w;
      cols.appendChild(c);
    });
    row.appendChild(cols);

    builder.querySelector('.flex-rows').appendChild(row);
    flexRefresh(builder);
    return row;
  }

  function flexChangeLayout(row, newLayout){
    var widths = FLEX_LAYOUTS[newLayout] || ['12'];
    var oldCols = Array.prototype.slice.call(row.querySelectorAll('.flex-col'));
    // Gather blocks per old column
    var bucket = oldCols.map(function(c){ return Array.prototype.slice.call(c.querySelectorAll(':scope > .flex-block')); });
    var colsWrap = row.querySelector('.flex-cols');
    colsWrap.innerHTML = '';
    var newCols = [];
    widths.forEach(function(w){ var c = document.createElement('div'); c.className='flex-col'; c.style.flex=w; flexInitColSortable(c); colsWrap.appendChild(c); newCols.push(c); });
    // Redistribute: keep by index, overflow into last column
    bucket.forEach(function(blocks, idx){
      var target = newCols[Math.min(idx, newCols.length - 1)];
      blocks.forEach(function(b){ target.appendChild(b); });
    });
    row.setAttribute('data-layout', newLayout);
  }

  function flexRefresh(builder){
    var hasRows = builder.querySelectorAll('.flex-row').length > 0;
    var empty = builder.querySelector('.flex-empty');
    if (empty) empty.style.display = hasRows ? 'none' : 'block';
  }

  function flexSerialize(builder){
    var rows = [];
    builder.querySelectorAll('.flex-row').forEach(function(row){
      var columns = [];
      row.querySelectorAll('.flex-col').forEach(function(col){
        var blocks = [];
        col.querySelectorAll(':scope > .flex-block').forEach(function(bl){
          var data = {};
          bl.querySelectorAll('[data-field]').forEach(function(f){ data[f.getAttribute('data-field')] = f.value; });
          blocks.push({ type: bl.getAttribute('data-type'), data: data });
        });
        columns.push({ blocks: blocks });
      });
      rows.push({ layout: row.getAttribute('data-layout'), columns: columns });
    });
    return JSON.stringify({ rows: rows });
  }

  function flexInit(builder){
    if (builder._init) return; builder._init = true;
    var src = builder.querySelector('.flex-src');
    var data = {};
    try { data = JSON.parse(src ? src.textContent : '{}') || {}; } catch (e) { data = {}; }
    (data.rows || []).forEach(function(r){ flexAddRow(builder, r.layout || '12', r.columns || []); });
    flexRefresh(builder);

    // Palette: draggable clone into columns
    var palette = builder.querySelector('.flex-palette');
    if (palette && window.Sortable) {
      Sortable.create(palette, { group: { name: 'flexblocks', pull: 'clone', put: false }, sort: false, draggable: '.palette-item' });
    }
    // Rows: reorder
    var rowsWrap = builder.querySelector('.flex-rows');
    if (rowsWrap && window.Sortable) {
      Sortable.create(rowsWrap, { handle: '.row-drag', animation: 150, draggable: '.flex-row' });
    }
  }

  function flexInitAll(){ document.querySelectorAll('[data-builder]').forEach(flexInit); }
  flexInitAll();

  // Delegated events for builders
  document.addEventListener('click', function(e){
    var builder = e.target.closest('[data-builder]');
    if (!builder) {
      // media pick buttons can be outside? they're inside builder; ignore
    }
    // Add row
    var addRow = e.target.closest('.add-row');
    if (addRow) { e.preventDefault(); flexAddRow(addRow.closest('[data-builder]'), addRow.getAttribute('data-layout'), []); return; }
    // Delete row
    var rowDel = e.target.closest('.row-del');
    if (rowDel) { e.preventDefault(); var r = rowDel.closest('.flex-row'); var b = r.closest('[data-builder]'); r.remove(); flexRefresh(b); return; }
    // Delete block
    var blockDel = e.target.closest('.block-del');
    if (blockDel) { e.preventDefault(); blockDel.closest('.flex-block').remove(); return; }
    // Click palette item -> append to last column
    var pal = e.target.closest('.palette-item');
    if (pal && builder) {
      e.preventDefault();
      var cols = builder.querySelectorAll('.flex-col');
      if (!cols.length) { alert('Add a row first, then add blocks into its columns.'); return; }
      cols[cols.length - 1].appendChild(flexMakeBlock(pal.getAttribute('data-block'), {}));
      return;
    }
    // Image picker inside a block
    var pick = e.target.closest('[data-pick]');
    if (pick) { e.preventDefault(); window.openMediaPicker(pick.getAttribute('data-pick'), 'single'); return; }
  });

  // Layout change
  document.addEventListener('change', function(e){
    var ll = e.target.closest('.row-layout');
    if (ll) { flexChangeLayout(ll.closest('.flex-row'), ll.value); }
  });

  // Serialize builders into hidden input on section save
  document.addEventListener('submit', function(e){
    var form = e.target;
    form.querySelectorAll('[data-builder]').forEach(function(builder){
      var hidden = builder.querySelector('.flex-json');
      if (hidden) hidden.value = flexSerialize(builder);
    });
  }, true);

  /* ---- Slug auto-fill ---- */
  document.querySelectorAll('[data-slug-source]').forEach(function (src) {
    var target = document.querySelector(src.getAttribute('data-slug-source'));
    if (!target) return;
    src.addEventListener('input', function () {
      if (target.dataset.touched) return;
      target.value = src.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    });
    target.addEventListener('input', function () { target.dataset.touched = '1'; });
  });
})();
