<?php

namespace App\Models;

use App\Models\PresensiBaseModel;

use CodeIgniter\I18n\Time;

class PresensiPerangkatModel extends PresensiBaseModel implements PresensiInterface
{
   protected $allowedFields = [
      'id_perangkat',
      'id_kelas',
      'tanggal',
      'jam_masuk',
      'jam_keluar',
      'id_kehadiran',
      'keterangan'
   ];

   protected $table = 'tb_presensi_perangkat';

   public function cekAbsen(string|int $id, string|Time $date)
   {
      $result = $this->where(['id_perangkat' => $id, 'tanggal' => $date])->first();

      if (empty($result)) return false;

      return $result[$this->primaryKey];
   }

   public function absenMasuk(string $id,  $date, $time, $idKelas = '')
   {
      $this->save([
         'id_perangkat' => $id,
         'id_kelas' => $idKelas,
         'tanggal' => $date,
         'jam_masuk' => $time,
         // 'jam_keluar' => '',
         'id_kehadiran' => Kehadiran::Hadir->value,
         'keterangan' => ''
      ]);
   }

   public function absenKeluar(string $id, $time)
   {
      $this->update($id, [
         'jam_keluar' => $time,
         'keterangan' => ''
      ]);
   }

   public function getPresensiByIdPerangkatTanggal($idPerangkat, $date)
   {
      return $this->where(['id_perangkat' => $idPerangkat, 'tanggal' => $date])->first();
   }

   public function getPresensiById($idPresensi)
   {
      return $this->where([$this->primaryKey => $idPresensi])->first();
   }

   public function getPresensiByKelasTanggal($idKelas, $tanggal)
   {
      return $this->setTable('tb_perangkat')
         ->select('*')
         ->join(
            "(SELECT id_presensi, id_perangkat AS id_perangkat_presensi, tanggal, jam_masuk, jam_keluar, id_kehadiran, keterangan FROM tb_presensi_perangkat)tb_presensi_perangkat",
            "{$this->table}.id_perangkat = tb_presensi_perangkat.id_perangkat_presensi AND tb_presensi_perangkat.tanggal = '$tanggal'",
            'left'
         )
         ->join(
            'tb_kehadiran',
            'tb_presensi_perangkat.id_kehadiran = tb_kehadiran.id_kehadiran',
            'left'
         )
         ->where("{$this->table}.id_kelas = $idKelas")
         ->orderBy("nama_perangkat")
         ->findAll();
   }

   public function getPresensiByKehadiran(string $idKehadiran, $tanggal)
   {
      $this->join(
         'tb_perangkat',
         "tb_presensi_perangkat.id_perangkat = tb_perangkat.id_perangkat AND tb_presensi_perangkat.tanggal = '$tanggal'",
         'right'
      );

      if ($idKehadiran == '4') {
         $result = $this->findAll();

         $filteredResult = [];

         foreach ($result as $value) {
            if ($value['id_kehadiran'] != ('1' || '2' || '3')) {
               array_push($filteredResult, $value);
            }
         }

         return $filteredResult;
      } else {
         $this->where(['tb_presensi_perangkat.id_kehadiran' => $idKehadiran]);
         return $this->findAll();
      }
   }

   public function updatePresensi($idPresensi = NULL, $idPerangkat, $idKelas, $tanggal, $idKehadiran, $jamMasuk = NULL, $jamKeluar, $keterangan = NULL)
   {
      $presensi = $this->getPresensiByIdPerangkatTanggal($idPerangkat, $tanggal);

      $data = [
         'id_perangkat' => $idPerangkat,
         'id_kelas' => $idKelas,
         'tanggal' => $tanggal,
         'id_kehadiran' => $idKehadiran,
         'keterangan' => $keterangan ?? $presensi['keterangan'] ?? ''
      ];

      if ($idPresensi != null) {
         $data[$this->primaryKey] = $idPresensi;
      }

      if ($jamMasuk != null) {
         $data['jam_masuk'] = $jamMasuk;
      }

      if ($jamKeluar != null) {
         $data['jam_keluar'] = $jamKeluar;
      }

      return $this->save($data);
   }
}
