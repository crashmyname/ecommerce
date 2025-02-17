<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Admin Dashboard</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="<?= asset('stisla-1-2.2.0/dist/assets/modules/bootstrap/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= asset('stisla-1-2.2.0/dist/assets/modules/fontawesome/css/all.min.css') ?>">

  <!-- CSS Libraries -->

  <!-- Template CSS -->
  <link rel="stylesheet" href="<?= asset('stisla-1-2.2.0/dist/assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?= asset('stisla-1-2.2.0/dist/assets/css/components.css') ?>">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <!-- Start GA -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-94034622-3');
  </script>
  <!-- /END GA -->
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>
        </form>
      </nav>
      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="<?= base_url() ?>">Ecommerce</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="<?= base_url() ?>">E</a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Me And U</li>
            <li class=""><a class="nav-link" href="<?= base_url() ?>/home"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>
            <li class="menu-header">Starter</li>
            <li class=""><a class="nav-link" href="<?= base_url() ?>/product"><i class="fas fa-fire"></i> <span>Users</span></a></li>
            <li class=""><a class="nav-link" href=""><i class="fas fa-fire"></i> <span>Products</span></a></li>
            <li class=""><a class="nav-link" href=""><i class="far fa-square"></i> <span>Transactions</span></a></li>
          </ul>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <?= $content ?>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2018 <div class="bullet"></div> Design By <a href="https://nauval.in/">Muhamad Nauval Azhar</a>
        </div>
        <div class="footer-right">

        </div>
      </footer>
    </div>
  </div>
  <script>
    $(document).ready(function() {
      function setActiveMenu() {
        let currentPath = window.location.pathname; // Ambil URL path saat ini
        $('.sidebar-menu li').removeClass('active'); // Hapus semua class 'active'
        $('.sidebar-menu .nav-link').each(function() {
          if ($(this).attr('href') === currentPath) {
            $(this).parent('li').addClass('active'); // Tambahkan class 'active' ke <li> yang sesuai
          }
        });
      }

      // Panggil fungsi untuk menetapkan menu aktif saat halaman pertama kali dimuat
      setActiveMenu();

      // Ketika item sidebar diklik
      $('.sidebar-menu .nav-link').on('click', function(e) {
        e.preventDefault();
        let url = $(this).attr('href'); // Ambil URL dari href
        $('.sidebar-menu li').removeClass('active');

        // Tambahkan class "active" ke menu yang diklik
        $(this).parent('li').addClass('active');

        // Ubah URL di address bar menggunakan pushState tanpa reload halaman
        history.pushState({
          path: url
        }, '', url);

        // Lakukan Ajax untuk mengambil konten dari URL tersebut
        $.ajax({
          url: url,
          method: 'GET',
          success: function(response) {
            // Ambil hanya konten dari response dan perbarui bagian konten
            let content = $(response).find('.main-content').html(); // Mengambil konten dari response
            $('.main-content').html(content); // Update hanya bagian .main-content

            // Jika ada JavaScript yang perlu dijalankan kembali, jalankan di sini
            if (typeof initDataTable === 'function') {
              initDataTable(); // Jika ada fungsi DataTable yang perlu dipanggil, jalankan kembali
            }
          },
          error: function() {
            $('.main-content').html('<p>Error loading content</p>');
          }
        });
      });

      // Tangani perubahan URL melalui tombol browser kembali/maju
      window.onpopstate = function(event) {
        if (event.state && event.state.path) {
          // Lakukan Ajax untuk memuat konten sesuai URL
          $.ajax({
            url: event.state.path,
            method: 'GET',
            success: function(response) {
              // Ambil konten yang sesuai dan perbarui halaman
              let content = $(response).find('.main-content').html();
              $('.main-content').html(content);

              // Jika ada JavaScript yang perlu dijalankan kembali, jalankan di sini
              if (typeof initDataTable === 'function') {
                initDataTable(); // Reinitialize DataTable jika ada
              }
              setActiveMenu();
            },
            error: function() {
              $('.main-content').html('<p>Error loading content</p>');
            }
          });
          let currentPath = event.state.path;
          $('.sidebar-menu li').removeClass('active');
          $('.sidebar-menu li').each(function() {
            if ($(this).attr('href') === currentPath) {
              $(this).parent('li').addClass('active');
            }
          });
        }
      };
    });
  </script>


  <!-- General JS Scripts -->
  <!-- <script src="<?= asset('stisla-1-2.2.0/dist/assets/modules/jquery.min.js') ?>"></script> -->
  <script src="<?= asset('stisla-1-2.2.0/dist/assets/modules/popper.js') ?>"></script>
  <script src="<?= asset('stisla-1-2.2.0/dist/assets/modules/tooltip.js') ?>"></script>
  <script src="<?= asset('stisla-1-2.2.0/dist/assets/modules/bootstrap/js/bootstrap.min.js') ?>"></script>
  <script src="<?= asset('stisla-1-2.2.0/dist/assets/modules/nicescroll/jquery.nicescroll.min.js') ?>"></script>
  <script src="<?= asset('stisla-1-2.2.0/dist/assets/modules/moment.min.js') ?>"></script>
  <script src="<?= asset('stisla-1-2.2.0/dist/assets/js/stisla.js') ?>"></script>
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

  <!-- JS Libraies -->

  <!-- Page Specific JS File -->

  <!-- Template JS File -->
  <script src="<?= asset('stisla-1-2.2.0/dist/assets/js/scripts.js') ?>"></script>
  <script src="<?= asset('stisla-1-2.2.0/dist/assets/js/custom.js') ?>"></script>
</body>

</html>