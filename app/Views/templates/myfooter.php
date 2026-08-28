<!-- End footer -->

</div> <!-- Close body-wrapper -->
</div> <!-- Close page-wrapper -->
</div> <!-- Close main-wrapper -->

<!-- ============================================ -->
<!-- LOGOUT CONFIRMATION MODAL -->
<!-- ============================================ -->
<div class="modal fade logout-modal" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-logout me-2"></i> Confirm Logout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle" style="font-size: 48px; color: #dc2626; opacity: 0.6; margin-bottom: 16px;"></i>
                <h4 class="mb-2">Are you sure?</h4>
                <p class="text-muted mb-0">You are about to log out of the QCPD Shooting Range Management System.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i> Cancel
                </button>
                <form action="<?= site_url('mylogout'); ?>" method="post" id="logoutForm">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-logout me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Scripts -->
<script src="<?=base_url('assets/js/vendor.min.js')?>"></script>
<script src="<?=base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')?>"></script>
<script src="<?=base_url('assets/libs/simplebar/dist/simplebar.min.js')?>"></script>
<script src="<?=base_url('assets/js/theme/app.init.js')?>"></script>
<script src="<?=base_url('assets/js/theme/theme.js')?>"></script>
<script src="<?=base_url('assets/js/theme/app.min.js')?>"></script>
<script src="<?=base_url('assets/js/theme/sidebarmenu.js')?>"></script>
<script src="<?=base_url('assets/libs/owl.carousel/dist/owl.carousel.min.js')?>"></script>
<script src="<?=base_url('assets/js/plugins/toastr-init.js')?>"></script>

<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');
  const pageWrapper = document.getElementById('pageWrapper');
  const overlay = document.getElementById('sidebarOverlay');
  const mobileToggle = document.getElementById('mobileMenuToggle');
  const closeSidebar = document.getElementById('closeSidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const logoutBtn = document.getElementById('logoutBtn');
  const profileLogoutBtn = document.getElementById('profileLogoutBtn');
  const logoutForm = document.getElementById('logoutForm');
  const currentUrl = window.location.href;
  
  document.querySelectorAll('.sidebar-item a').forEach(link => {
    if (link.href === currentUrl) {
      link.parentElement.classList.add('active');
    }
  });
  
  // Desktop sidebar toggle (collapse/expand)
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
      pageWrapper.classList.toggle('expanded');
    });
  }

  // Mobile sidebar open
  if (mobileToggle) {
    mobileToggle.addEventListener('click', function() {
      sidebar.classList.add('open');
      if (overlay) overlay.classList.add('active');
    });
  }

  // Mobile sidebar close
  function closeSidebarMenu() {
    sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
  }

  if (closeSidebar) {
    closeSidebar.addEventListener('click', closeSidebarMenu);
  }

  if (overlay) {
    overlay.addEventListener('click', closeSidebarMenu);
  }

  // ============================================ //
  // LOGOUT MODAL TRIGGER - Sidebar Logout
  // ============================================ //
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      var logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
      logoutModal.show();
    });
  }

  // ============================================ //
  // LOGOUT MODAL TRIGGER - Profile Dropdown Logout
  // ============================================ //
  if (profileLogoutBtn) {
    profileLogoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      var logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
      logoutModal.show();
    });
  }

  // Close mobile sidebar when a link is clicked
  document.querySelectorAll('.sidebar-link').forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
      }
    });
  });
});
</script>
</body>
</html>