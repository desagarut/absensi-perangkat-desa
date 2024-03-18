<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\GuruModel;
use App\Models\PerangkatModel;
use App\Models\KelasModel;
use App\Models\PetugasModel;
use App\Models\PresensiGuruModel;
use App\Models\PresensiPerangkatModel;
use CodeIgniter\I18n\Time;

class Dashboard extends BaseController
{
   protected PerangkatModel $perangkatModel;
   protected GuruModel $guruModel;

   protected KelasModel $KelasModel;

   protected PresensiPerangkatModel $presensiPerangkatModel;
   protected PresensiGuruModel $presensiGuruModel;

   protected PetugasModel $petugasModel;

   public function __construct()
   {
      $this->perangkatModel = new PerangkatModel();
      $this->guruModel = new GuruModel();
      $this->KelasModel = new KelasModel();
      $this->presensiPerangkatModel = new PresensiPerangkatModel();
      $this->presensiGuruModel = new PresensiGuruModel();
      $this->petugasModel = new PetugasModel();
   }

   public function index()
   {
      $now = Time::now();

      $dateRange = [];
      $perangkatKehadiranArray = [];
      $guruKehadiranArray = [];

      for ($i = 6; $i >= 0; $i--) {
         $date = $now->subDays($i)->toDateString();
         if ($i == 0) {
            $formattedDate = "Hari ini";
         } else {
            $t = $now->subDays($i);
            $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
         }
         array_push($dateRange, $formattedDate);
         array_push(
            $perangkatKehadiranArray,
            count($this->presensiPerangkatModel
               ->join('tb_perangkat', 'tb_presensi_perangkat.id_perangkat = tb_perangkat.id_perangkat', 'left')
               ->where(['tb_presensi_perangkat.tanggal' => "$date", 'tb_presensi_perangkat.id_kehadiran' => '1'])->findAll())
         );
         array_push(
            $guruKehadiranArray,
            count($this->presensiGuruModel
               ->join('tb_guru', 'tb_presensi_guru.id_guru = tb_guru.id_guru', 'left')
               ->where(['tb_presensi_guru.tanggal' => "$date", 'tb_presensi_guru.id_kehadiran' => '1'])->findAll())
         );
      }

      $today = $now->toDateString();

      $data = [
         'title' => 'Dashboard',
         'ctx' => 'dashboard',

         'perangkat' => $this->perangkatModel->getAllPerangkatWithKelas(),
         'guru' => $this->guruModel->getAllGuru(),

         'kelas' => $this->KelasModel->getAllKelas(),

         'dateRange' => $dateRange,
         'dateNow' => $now->toLocalizedString('d MMMM Y'),

         'grafikKehadiranPerangkat' => $perangkatKehadiranArray,
         'grafikkKehadiranGuru' => $guruKehadiranArray,

         'jumlahKehadiranPerangkat' => [
            'hadir' => count($this->presensiPerangkatModel->getPresensiByKehadiran('1', $today)),
            'sakit' => count($this->presensiPerangkatModel->getPresensiByKehadiran('2', $today)),
            'izin' => count($this->presensiPerangkatModel->getPresensiByKehadiran('3', $today)),
            'alfa' => count($this->presensiPerangkatModel->getPresensiByKehadiran('4', $today))
         ],

         'jumlahKehadiranGuru' => [
            'hadir' => count($this->presensiGuruModel->getPresensiByKehadiran('1', $today)),
            'sakit' => count($this->presensiGuruModel->getPresensiByKehadiran('2', $today)),
            'izin' => count($this->presensiGuruModel->getPresensiByKehadiran('3', $today)),
            'alfa' => count($this->presensiGuruModel->getPresensiByKehadiran('4', $today))
         ],

         'petugas' => $this->petugasModel->getAllPetugas()
      ];

      return view('admin/dashboard', $data);
   }
}
