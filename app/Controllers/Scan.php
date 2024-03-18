<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\GuruModel;
use App\Models\PerangkatModel;
use App\Models\PresensiGuruModel;
use App\Models\PresensiPerangkatModel;
use App\Models\TipeUser;

class Scan extends BaseController
{
   protected PerangkatModel $perangkatModel;
   protected GuruModel $guruModel;

   protected PresensiPerangkatModel $presensiPerangkatModel;
   protected PresensiGuruModel $presensiGuruModel;

   public function __construct()
   {
      $this->perangkatModel = new PerangkatModel();
      $this->guruModel = new GuruModel();
      $this->presensiPerangkatModel = new PresensiPerangkatModel();
      $this->presensiGuruModel = new PresensiGuruModel();
   }

   public function index($t = 'Masuk')
   {
      $data = ['waktu' => $t, 'title' => 'Absensi Perangkat dan Guru Berbasis QR Code'];
      return view('scan/scan', $data);
   }

   public function cekKode()
   {
      // ambil variabel POST
      $uniqueCode = $this->request->getVar('unique_code');
      $waktuAbsen = $this->request->getVar('waktu');

      $status = false;
      $type = TipeUser::Perangkat;

      // cek data perangkat di database
      $result = $this->perangkatModel->cekPerangkat($uniqueCode);

      if (empty($result)) {
         // jika cek perangkat gagal, cek data guru
         $result = $this->guruModel->cekGuru($uniqueCode);

         if (!empty($result)) {
            $status = true;

            $type = TipeUser::Guru;
         } else {
            $status = false;

            $result = NULL;
         }
      } else {
         $status = true;
      }

      if (!$status) { // data tidak ditemukan
         return $this->showErrorView('Data tidak ditemukan');
      }

      // jika data ditemukan
      switch ($waktuAbsen) {
         case 'masuk':
            return $this->absenMasuk($type, $result);
            break;

         case 'pulang':
            return $this->absenPulang($type, $result);
            break;

         default:
            return $this->showErrorView('Data tidak valid');
            break;
      }
   }

   public function absenMasuk($type, $result)
   {
      // data ditemukan
      $data['data'] = $result;
      $data['waktu'] = 'masuk';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();

      // absen masuk
      switch ($type) {
         case TipeUser::Guru:
            $idGuru =  $result['id_guru'];
            $data['type'] = TipeUser::Guru;

            $sudahAbsen = $this->presensiGuruModel->cekAbsen($idGuru, $date);

            if ($sudahAbsen != false) {
               $data['presensi'] = $this->presensiGuruModel->getPresensiById($sudahAbsen);
               return $this->showErrorView('Anda sudah absen hari ini', $data);
            }

            $this->presensiGuruModel->absenMasuk($idGuru, $date, $time);

            $data['presensi'] = $this->presensiGuruModel->getPresensiByIdGuruTanggal($idGuru, $date);

            return view('scan/scan-result', $data);

         case TipeUser::Perangkat:
            $idPerangkat =  $result['id_perangkat'];
            $idKelas =  $result['id_kelas'];
            $data['type'] = TipeUser::Perangkat;

            $sudahAbsen = $this->presensiPerangkatModel->cekAbsen($idPerangkat, Time::today()->toDateString());

            if ($sudahAbsen != false) {
               $data['presensi'] = $this->presensiPerangkatModel->getPresensiById($sudahAbsen);
               return $this->showErrorView('Anda sudah absen hari ini', $data);
            }

            $this->presensiPerangkatModel->absenMasuk($idPerangkat, $date, $time, $idKelas);

            $data['presensi'] = $this->presensiPerangkatModel->getPresensiByIdPerangkatTanggal($idPerangkat, $date);

            return view('scan/scan-result', $data);

         default:
            return $this->showErrorView('Tipe tidak valid');
      }
   }

   public function absenPulang($type, $result)
   {
      // data ditemukan
      $data['data'] = $result;
      $data['waktu'] = 'pulang';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();

      // absen pulang
      switch ($type) {
         case TipeUser::Guru:
            $idGuru =  $result['id_guru'];
            $data['type'] = TipeUser::Guru;

            $sudahAbsen = $this->presensiGuruModel->cekAbsen($idGuru, $date);

            if ($sudahAbsen == false) {
               return $this->showErrorView('Anda belum absen hari ini', $data);
            }

            $this->presensiGuruModel->absenKeluar($sudahAbsen, $time);

            $data['presensi'] = $this->presensiGuruModel->getPresensiById($sudahAbsen);

            return view('scan/scan-result', $data);

         case TipeUser::Perangkat:
            $idPerangkat =  $result['id_perangkat'];
            $data['type'] = TipeUser::Perangkat;

            $sudahAbsen = $this->presensiPerangkatModel->cekAbsen($idPerangkat, $date);

            if ($sudahAbsen == false) {
               return $this->showErrorView('Anda belum absen hari ini', $data);
            }

            $this->presensiPerangkatModel->absenKeluar($sudahAbsen, $time);

            $data['presensi'] = $this->presensiPerangkatModel->getPresensiById($sudahAbsen);

            return view('scan/scan-result', $data);
         default:
            return $this->showErrorView('Tipe tidak valid');
      }
   }

   public function showErrorView(string $msg = 'no error message', $data = NULL)
   {
      $errdata = $data ?? [];
      $errdata['msg'] = $msg;

      return view('scan/error-scan-result', $errdata);
   }
}
