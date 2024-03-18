<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-body">
                  <div class="row justify-content-between">
                     <div class="col">
                        <div class="pt-3 pl-3">
                           <h4><b>Daftar Kelas</b></h4>
                           <p>Silakan pilih kelas</p>
                        </div>
                     </div>
                     <div class="col-sm-auto">
                        <button data-toggle="modal" data-target="#tambahKelasModal" class="btn btn-primary pl-3 py-3 mr-3 mt-3">
                           <i class="material-icons mr-2">add</i> Tambah Data Kelas
                        </button>
                     </div>
                  </div>

                  <div class="card-body pt-1 px-3">
                     <div class="row">
                        <?php foreach ($kelas as $value) : ?>
                           <?php
                           $idKelas = $value['id_kelas'];
                           $namaKelas =  $value['kelas'] . ' ' . $value['jurusan'];
                           ?>
                           <div class="col-md-3">
                              <button id="kelas-<?= $idKelas; ?>" onclick="getPerangkat(<?= $idKelas; ?>, '<?= $namaKelas; ?>')" class="btn btn-primary w-100">
                                 <?= $namaKelas; ?>
                              </button>
                           </div>
                        <?php endforeach; ?>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-3">
                        <div class="pt-3 pl-3 pb-2">
                           <h4><b>Tanggal</b></h4>
                           <input class="form-control" type="date" name="tangal" id="tanggal" value="<?= date('Y-m-d'); ?>" onchange="onDateChange()">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card" id="dataPerangkat">
         <div class="card-body">
            <div class="row justify-content-between">
               <div class="col-auto me-auto">
                  <div class="pt-3 pl-3">
                     <h4><b>Absen Perangkat</b></h4>
                     <p>Daftar Perangkat muncul disini</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal tambah kelas -->
   <div class="modal fade" id="tambahKelasModal" tabindex="-1" aria-labelledby="tambahKelasModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <form id="formTambahKelas" action="#">
               <div class="modal-header">
                  <h5 class="modal-title" id="modalUbahKehadiran">Tambah Data Instansi</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <div class="container-fluid">
                     <div class="form-group mt-2">
                        <label for="kelas">Instansi Induk</label>
                        <select class="custom-select custom-select-sm" id="kelas" name="kelas" required>
                           <option value="">--Pilih--</option>
                           <option value="DESA">DESA</option>
                           <option value="KELURAHAN">KELURAHAN</option>
                           <option value="KECAMATAN">KECAMATAN</option>
                        </select>
                     </div>
                     <div class="form-group mt-4">
                        <label for="jurusan">Nama Instansi Induk</label>
                        <input type="text" id="jurusan" class="form-control" name="jurusan" placeholder="Nama Instansi Induk" required>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                  <button type="submit" onclick="tambahDataKelas()" class="btn btn-primary">Simpan</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Modal ubah kehadiran -->
   <div class="modal fade" id="ubahModal" tabindex="-1" aria-labelledby="modalUbahKehadiran" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modalUbahKehadiran">Ubah kehadiran</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div id="modalFormUbahPerangkat"></div>
         </div>
      </div>
   </div>
</div>
<script>
   var lastIdKelas;
   var lastKelas;

   function onDateChange() {
      if (lastIdKelas != null && lastKelas != null) getPerangkat(lastIdKelas, lastKelas);
   }

   function getPerangkat(idKelas, kelas) {
      var tanggal = $('#tanggal').val();

      updateBtn(idKelas);

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-perangkat'); ?>",
         type: 'post',
         data: {
            'kelas': kelas,
            'id_kelas': idKelas,
            'tanggal': tanggal
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#dataPerangkat').html(response);

            $('html, body').animate({
               scrollTop: $("#dataPerangkat").offset().top
            }, 500);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#dataPerangkat').html(thrown);
         }
      });

      lastIdKelas = idKelas;
      lastKelas = kelas;
   }

   function updateBtn(id_btn) {
      for (let index = 1; index <= <?= count($kelas); ?>; index++) {
         if (index != id_btn) {
            $('#kelas-' + index).removeClass('btn-success');
            $('#kelas-' + index).addClass('btn-primary');
         } else {
            $('#kelas-' + index).removeClass('btn-primary');
            $('#kelas-' + index).addClass('btn-success');
         }
      }
   }

   function getDataKehadiran(idPresensi, idPerangkat) {
      jQuery.ajax({
         url: "<?= base_url('/admin/absen-perangkat/kehadiran'); ?>",
         type: 'post',
         data: {
            'id_presensi': idPresensi,
            'id_perangkat': idPerangkat
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#modalFormUbahPerangkat').html(response);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#modalFormUbahPerangkat').html(thrown);
         }
      });
   }

   function ubahKehadiran() {
      var tanggal = $('#tanggal').val();

      var form = $('#formUbah').serializeArray();

      form.push({
         name: 'tanggal',
         value: tanggal
      });

      console.log(form);

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-perangkat/edit'); ?>",
         type: 'post',
         data: form,
         success: function(response, status, xhr) {
            // console.log(status);

            if (response['status']) {
               getPerangkat(lastIdKelas, lastKelas);
               alert('Berhasil ubah kehadiran : ' + response['nama_perangkat']);
            } else {
               alert('Gagal ubah kehadiran : ' + response['nama_perangkat']);
            }
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            alert('Gagal ubah kehadiran\n' + thrown);
         }
      });
   }

   function tambahDataKelas() {
      var form = $('#formTambahKelas').serializeArray();

      jQuery.ajax({
         url: "<?= base_url('/admin/tambah-kelas'); ?>",
         type: 'post',
         data: form,
         success: function(response, status, xhr) {
            // console.log(status);

            if (response['status']) {
               getPerangkat(lastIdKelas, lastKelas);
               alert('Berhasil tambah kelas : ' + response['kelas']);
            } else {
               alert('Gagal ubah kehadiran : ' + response['kelas']);
            }
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            alert('Gagal menambah kelas\n' + thrown);
         }
      });
   }
</script>
<?= $this->endSection() ?>