<div class="modal fade" id="mediaModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Media</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="mediaModalSearch" class="form-control mb-3" placeholder="Search media…">
        <div class="media-grid" id="mediaModalGrid"></div>
      </div>
      <div class="modal-footer">
        <a href="<?= base_url('admin/media') ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Open Media Library</a>
      </div>
    </div>
  </div>
</div>
<script>
  window.CMS_BASE = "<?= base_url('') ?>";
  window.CMS_PICKER_URL = "<?= base_url('admin/media/picker') ?>";
  window.CMS_REORDER_URL = "<?= base_url('admin/pages/reorderSections') ?>";
  window.CMS_MENU_REORDER_URL = "<?= base_url('admin/menus/reorder') ?>";
</script>
