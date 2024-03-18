<?php

namespace App\Models;

use CodeIgniter\Model;

class PerangkatModel extends Model
{
   protected function initialize()
   {
      $this->allowedFields = [
         'nipd',
         'nama_perangkat',
         'id_kelas',
         'jenis_kelamin',
         'no_hp',
         'unique_code'
      ];
   }

   protected $table = 'tb_perangkat';

   protected $primaryKey = 'id_perangkat';

   public function cekPerangkat(string $unique_code)
   {
      $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_perangkat.id_kelas',
         'LEFT'
      );
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getPerangkatById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function getAllPerangkatWithKelas($kelas = null, $jurusan = null)
   {
      $query = $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_perangkat.id_kelas',
         'LEFT'
      );

      if (!empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas, 'jurusan' => $jurusan]);
      } else if (empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['jurusan' => $jurusan]);
      } else if (!empty($kelas) && empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas]);
      } else {
         $query = $this;
      }

      return $query->orderBy('nama_perangkat')->findAll();
   }

   public function getPerangkatByKelas($id_kelas)
   {
      return $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_perangkat.id_kelas',
         'LEFT'
      )->where(['tb_perangkat.id_kelas' => $id_kelas])->findAll();
   }

   public function savePerangkat($idPerangkat, $nipd, $namaPerangkat, $idKelas, $jenisKelamin, $noHp)
   {
      return $this->save([
         $this->primaryKey => $idPerangkat,
         'nipd' => $nipd,
         'nama_perangkat' => $namaPerangkat,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'unique_code' => sha1($namaPerangkat . md5($nipd . $noHp . $namaPerangkat)) . substr(sha1($nipd . rand(0, 100)), 0, 24)
      ]);
   }
}
