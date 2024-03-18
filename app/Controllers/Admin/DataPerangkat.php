<?php

namespace App\Controllers\Admin;

use App\Models\PerangkatModel;
use App\Models\KelasModel;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataPerangkat extends BaseController
{
   protected PerangkatModel $perangkatModel;
   protected KelasModel $kelasModel;

   protected $perangkatValidationRules = [
      'nipd' => [
         'rules' => 'required|max_length[20]|min_length[4]',
         'errors' => [
            'required' => 'NIPD harus diisi.',
            'is_unique' => 'NIPD ini telah terdaftar.',
            'min_length[4]' => 'Panjang NIPD minimal 4 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'id_kelas' => [
         'rules' => 'required',
         'errors' => [
            'required' => 'Kelas harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]'
   ];

   public function __construct()
   {
      $this->perangkatModel = new PerangkatModel();
      $this->kelasModel = new KelasModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Perangkat',
         'ctx' => 'perangkat',
         'kelas' => $this->kelasModel->getAllKelas()
      ];

      return view('admin/data/data-perangkat', $data);
   }

   public function ambilDataPerangkat()
   {
      $kelas = $this->request->getVar('kelas') ?? null;
      $jurusan = $this->request->getVar('jurusan') ?? null;

      $result = $this->perangkatModel->getAllPerangkatWithKelas($kelas, $jurusan);

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-perangkat', $data);
   }

   public function formTambahPerangkat()
   {
      $kelas = $this->kelasModel->getAllKelas();

      $data = [
         'ctx' => 'perangkat',
         'kelas' => $kelas,
         'title' => 'Tambah Data Perangkat'
      ];

      return view('admin/data/create/create-data-perangkat', $data);
   }

   public function savePerangkat()
   {
      // validasi
      if (!$this->validate($this->perangkatValidationRules)) {
         $kelas = $this->kelasModel->getAllKelas();

         $data = [
            'ctx' => 'perangkat',
            'kelas' => $kelas,
            'title' => 'Tambah Data Perangkat',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-perangkat', $data);
      }

      $nipd = $this->request->getVar('nipd');
      $namaPerangkat = $this->request->getVar('nama');
      $idKelas = intval($this->request->getVar('id_kelas'));
      $jenisKelamin = $this->request->getVar('jk');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->perangkatModel->savePerangkat(NULL, $nipd, $namaPerangkat, $idKelas, $jenisKelamin, $noHp);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/perangkat');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/perangkat/create');
   }

   public function formEditPerangkat($id)
   {
      $perangkat = $this->perangkatModel->getPerangkatById($id);
      $kelas = $this->kelasModel->getAllKelas();

      if (empty($perangkat) || empty($kelas)) {
         throw new PageNotFoundException('Data perangkat dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $perangkat,
         'kelas' => $kelas,
         'ctx' => 'perangkat',
         'title' => 'Edit Perangkat',
      ];

      return view('admin/data/edit/edit-data-perangkat', $data);
   }

   public function updatePerangkat()
   {
      $idPerangkat = $this->request->getVar('id');

      $perangkatLama = $this->perangkatModel->getPerangkatById($idPerangkat);

      if ($perangkatLama['nipd'] != $this->request->getVar('nipd')) {
         $this->perangkatValidationRules['nipd']['rules'] = 'required|max_length[20]|min_length[4]|is_unique[tb_perangkat.nipd]';
      }

      // validasi
      if (!$this->validate($this->perangkatValidationRules)) {
         $perangkat = $this->perangkatModel->getPerangkatById($idPerangkat);
         $kelas = $this->kelasModel->getAllKelas();

         $data = [
            'data' => $perangkat,
            'kelas' => $kelas,
            'ctx' => 'perangkat',
            'title' => 'Edit Perangkat',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-perangkat', $data);
      }

      $nipd = $this->request->getVar('nipd');
      $namaPerangkat = $this->request->getVar('nama');
      $idKelas = intval($this->request->getVar('id_kelas'));
      $jenisKelamin = $this->request->getVar('jk');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->perangkatModel->savePerangkat($idPerangkat, $nipd, $namaPerangkat, $idKelas, $jenisKelamin, $noHp);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/perangkat');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/perangkat/edit/' . $idPerangkat);
   }

   public function delete($id)
   {
      $result = $this->perangkatModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/perangkat');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/perangkat');
   }
}
