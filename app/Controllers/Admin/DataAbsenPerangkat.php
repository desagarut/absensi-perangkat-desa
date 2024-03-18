<?php

namespace App\Controllers\Admin;

use App\Models\KelasModel;

use App\Models\PerangkatModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiPerangkatModel;
use CodeIgniter\I18n\Time;
use mysqli;

class DataAbsenPerangkat extends BaseController
{
   protected KelasModel $kelasModel;

   protected PerangkatModel $perangkatModel;

   protected KehadiranModel $kehadiranModel;

   protected PresensiPerangkatModel $presensiPerangkat;

   protected string $currentDate;

   public function __construct()
   {
      $this->currentDate = Time::today()->toDateString();

      $this->perangkatModel = new PerangkatModel();

      $this->kehadiranModel = new KehadiranModel();

      $this->kelasModel = new KelasModel();

      $this->presensiPerangkat = new PresensiPerangkatModel();
   }

   public function index()
   {
      $kelas = $this->kelasModel->getAllKelas();

      $data = [
         'title' => 'Data Absen Perangkat',
         'ctx' => 'absen-perangkat',
         'kelas' => $kelas
      ];

      return view('admin/absen/absen-perangkat', $data);
   }

   public function ambilDataPerangkat()
   {
      // ambil variabel POST
      $kelas = $this->request->getVar('kelas');
      $idKelas = $this->request->getVar('id_kelas');
      $tanggal = $this->request->getVar('tanggal');

      $lewat = Time::parse($tanggal)->isAfter(Time::today());

      $result = $this->presensiPerangkat->getPresensiByKelasTanggal($idKelas, $tanggal);

      $data = [
         'kelas' => $kelas,
         'data' => $result,
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'lewat' => $lewat
      ];

      return view('admin/absen/list-absen-perangkat', $data);
   }

   public function ambilKehadiran()
   {
      $idPresensi = $this->request->getVar('id_presensi');
      $idPerangkat = $this->request->getVar('id_perangkat');

      $data = [
         'presensi' => $this->presensiPerangkat->getPresensiById($idPresensi),
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'data' => $this->perangkatModel->getPerangkatById($idPerangkat)
      ];

      return view('admin/absen/ubah-kehadiran-modal', $data);
   }

   public function ubahKehadiran()
   {
      // ambil variabel POST
      $idKehadiran = $this->request->getVar('id_kehadiran');
      $idPerangkat = $this->request->getVar('id_perangkat');
      $idKelas = $this->request->getVar('id_kelas');
      $tanggal = $this->request->getVar('tanggal');
      $jamMasuk = $this->request->getVar('jam_masuk');
      $jamKeluar = $this->request->getVar('jam_keluar');
      $keterangan = $this->request->getVar('keterangan');

      $cek = $this->presensiPerangkat->cekAbsen($idPerangkat, $tanggal);

      $result = $this->presensiPerangkat->updatePresensi(
         $cek == false ? NULL : $cek,
         $idPerangkat,
         $idKelas,
         $tanggal,
         $idKehadiran,
         $jamMasuk ?? NULL,
         $jamKeluar ?? NULL,
         $keterangan
      );

      $response['nama_perangkat'] = $this->perangkatModel->getPerangkatById($idPerangkat)['nama_perangkat'];

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }

   public function tambahKelas()
   {
      // ambil variabel POST
      $kelas = $this->request->getVar('kelas');
      $jurusan = $this->request->getVar('jurusan');

      $arrJurusan = $this->kelasModel->getAllKelas();

      $currentJurusan = [];

      foreach ($arrJurusan as $value) {
         array_push($currentJurusan, $value['jurusan']);
      }

      if (!in_array($jurusan, $currentJurusan)) {
         $filteredJurusan = [];

         foreach ($currentJurusan as $value) {
            if (!in_array($value, $filteredJurusan)) {
               array_push($filteredJurusan, $value);
            }
         }

         array_push($filteredJurusan, $jurusan);

         $this->kelasModel->db->query("ALTER TABLE `tb_kelas` CHANGE `jurusan` `jurusan` ENUM("
            . array_reduce($filteredJurusan, function ($ax, $dx) {
               if (empty($ax)) {
                  return "$ax" . "'$dx'";
               }
               return "$ax," . "'$dx'";
            }) .
            ") CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;");
      }

      $result = $this->kelasModel->tambahKelas($kelas, $jurusan);

      $response['kelas'] = "$kelas $jurusan";

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }
}
