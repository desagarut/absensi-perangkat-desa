<?php
$context = $ctx ?? 'dashboard';
switch ($context) {
   case 'absen-perangkat':
   case 'perangkat':
      $sidebarColor = 'purple';
      break;
   case 'absen-guru':
   case 'guru':
      $sidebarColor = 'green';
      break;

   case 'qr':
      $sidebarColor = 'danger';
      break;

   default:
      $sidebarColor = 'azure';
      break;
}
?>
<div class="sidebar" data-color="<?= $sidebarColor; ?>" data-background-color="black" data-image="<?= base_url('public/assets/img/sidebar/sidebar-1.jpg'); ?>">
   <div class="logo">
      <a class="simple-text logo-normal">
         <b>Operator<br>Petugas Absensi</b>
      </a>
   </div>
   <div class="sidebar-wrapper">
      <ul class="nav">
         <li class="nav-item <?= $context == 'dashboard' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/dashboard'); ?>">
               <i class="material-icons">dashboard</i>
               <p>Dashboard</p>
            </a>
         </li>
         <li class="nav-item <?= $context == 'absen-perangkat' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/absen-perangkat'); ?>">
               <i class="material-icons">checklist</i>
               <p>Absensi Perangkat</p>
            </a>
         </li>
         <li class="nav-item <?= $context == 'absen-guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/absen-guru'); ?>">
               <i class="material-icons">checklist</i>
               <p>Absensi Kades / Lurah</p>
            </a>
         </li>
         <li class="nav-item <?= $context == 'perangkat' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/perangkat'); ?>">
               <i class="material-icons">person</i>
               <p>Data Perangkat</p>
            </a>
         </li>
         <li class="nav-item <?= $context == 'guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/guru'); ?>">
               <i class="material-icons">person_4</i>
               <p>Data Kades / Lurah</p>
            </a>
         </li>
         <li class="nav-item <?= $context == 'qr' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/generate'); ?>">
               <i class="material-icons">qr_code</i>
               <p>Generate QR Code</p>
            </a>
         </li>
         <li class="nav-item <?= $context == 'laporan' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= base_url('admin/laporan'); ?>">
               <i class="material-icons">print</i>
               <p>Generate Laporan</p>
            </a>
         </li>
         <?php if (user()->toArray()['is_superadmin'] ?? '0' == '1') : ?>
            <li class="nav-item <?= $context == 'petugas' ? 'active' : ''; ?>">
               <a class="nav-link" href="<?= base_url('admin/petugas'); ?>">
                  <i class="material-icons">computer</i>
                  <p>Data Petugas</p>
               </a>
            </li>
         <?php endif; ?>
      </ul>
   </div>
</div>