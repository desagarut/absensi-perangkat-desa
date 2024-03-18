<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <?php if (session()->getFlashdata('msg')) : ?>
               <div class="pb-2 px-3">
                  <div class="alert alert-success">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                     </button>
                     <?= session()->getFlashdata('msg') ?>
                  </div>
               </div>
            <?php endif; ?>
            <a class="btn btn-primary ml-3 pl-3 py-3" href="<?= base_url('admin/perangkat/create'); ?>">
               <i class="material-icons mr-2">add</i> Tambah data perangkat
            </a>
            <div class="card">
               <div class="card-header card-header-tabs card-header-primary">
                  <div class="nav-tabs-navigation">
                     <div class="row">
                        <div class="col-md-2">
                           <h4 class="card-title"><b>Daftar Perangkat</b></h4>
                        </div>
                        <div class="col-md-4">
                           <div class="nav-tabs-wrapper">
                              <span class="nav-tabs-title">Kategori:</span>
                              <ul class="nav nav-tabs" data-tabs="tabs">
                                 <li class="nav-item">
                                    <a class="nav-link active" onclick="kelas = null; trig()" href="#" data-toggle="tab">
                                       <i class="material-icons">check</i> Semua
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                                 <li class="nav-item">
                                    <a class="nav-link" onclick="kelas = 'Desa'; trig();" href="#" data-toggle="tab">
                                       <i class="material-icons">school</i> Desa
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                                 <li class="nav-item">
                                    <a class="nav-link" onclick="kelas = 'Kelurahan'; trig();" href="#" data-toggle="tab">
                                       <i class="material-icons">school</i> Kelurahan
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                              </ul>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="nav-tabs-wrapper">
                              <span class="nav-tabs-title">Nama:</span>
                              <ul class="nav nav-tabs" data-tabs="tabs">
                                 <li class="nav-item">
                                    <a class="nav-link active" onclick="jurusan = null; trig()" href="#" data-toggle="tab">
                                       <i class="material-icons">check</i> Semua
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                                 <?php
                                 $tempJurusan = [];

                                 foreach ($kelas as $kls) : ?>
                                    <?php if (!in_array($kls['jurusan'], $tempJurusan)) : ?> <li class="nav-item">
                                          <a class="nav-link" onclick="jurusan = '<?= $kls['jurusan']; ?>'; trig();" href="#" data-toggle="tab">
                                             <i class="material-icons">work</i> <?= $kls['jurusan']; ?>
                                             <div class="ripple-container"></div>
                                          </a>
                                       </li>
                                       <?php array_push($tempJurusan, $kls['jurusan']); ?>
                                    <?php endif; ?>
                                 <?php endforeach; ?>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div id="dataPerangkat">
                  <p class="text-center mt-3">Daftar perangkat muncul disini</p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   var kelas = null;
   var jurusan = null;

   getDataPerangkat(kelas, jurusan);

   function trig() {
      getDataPerangkat(kelas, jurusan);
   }

   function getDataPerangkat(_kelas = null, _jurusan = null) {
      jQuery.ajax({
         url: "<?= base_url('/admin/perangkat'); ?>",
         type: 'post',
         data: {
            'kelas': _kelas,
            'jurusan': _jurusan
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
   }
</script>
<?= $this->endSection() ?>