<section class="section">
    <div class="section-header">
        <h1>Products</h1>
    </div>

    <div class="section-body">
        <b>Ini adalah products</b>
    </div>
    <table id="datatable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Email</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Email</th>
            </tr>
        </tfoot>
    </table>
</section>
<script>
    // Fungsi inisialisasi DataTables khusus untuk halaman ini
  function initDataTable() {
    if ($.fn.dataTable.isDataTable('#datatable')) {
    $('#datatable').DataTable().clear().destroy(); // Hancurkan DataTable yang sudah ada
  }
    $('#datatable').DataTable({
      ajax: '<?= base_url()?>/products',
      processing: true,
      serverSide: true,
      columns: [
        { data: 'user_id', name: 'user_id' },
        { data: 'username', name: 'username' },
        { data: 'email', name: 'email' }
      ]
    });
  }

  // Panggil initDataTable saat halaman Products dimuat
  $(document).ready(function(){
    initDataTable();
  });
</script>
