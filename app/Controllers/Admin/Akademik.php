<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudyProgramModel;
use App\Models\AcademicYearModel;

class Akademik extends BaseController
{
    protected $prodiModel;
    protected $tahunModel;

    public function __construct()
    {
        $this->prodiModel = new StudyProgramModel();
        $this->tahunModel = new AcademicYearModel();
    }

    // 1. READ (Menampilkan Data Prodi & Tahun Akademik sekaligus)
    public function index()
    {
        $data = [
            'title' => 'Data Akademik - Sisfo Praktikum',
            'prodi' => $this->prodiModel->findAll(),
            'tahun' => $this->tahunModel->orderBy('id', 'DESC')->findAll()
        ];

        return view('admin/data_akademik', $data);
    }

    // =========================================================================
    // FITUR PROGRAM STUDI
    // =========================================================================
    public function storeProdi()
    {
        $this->prodiModel->save([
            'program_code' => $this->request->getPost('program_code'),
            'program_name' => $this->request->getPost('program_name'),
            'degree_level' => $this->request->getPost('degree_level'),
            'status'       => 'aktif'
        ]);

        return redirect()->to(base_url('admin/akademik'))->with('msg', 'Program Studi berhasil ditambahkan!');
    }

    public function updateProdi()
    {
        $id = $this->request->getPost('id');
        $this->prodiModel->update($id, [
            'program_code' => $this->request->getPost('program_code'),
            'program_name' => $this->request->getPost('program_name'),
            'degree_level' => $this->request->getPost('degree_level'),
        ]);

        return redirect()->to(base_url('admin/akademik'))->with('msg', 'Program Studi berhasil diperbarui!');
    }

    public function deleteProdi($id)
    {
        $this->prodiModel->delete($id);
        return redirect()->to(base_url('admin/akademik'))->with('msg', 'Program Studi berhasil dihapus!');
    }

    // =========================================================================
    // FITUR TAHUN AKADEMIK
    // =========================================================================
    public function storeTahun()
    {
        // Menggabungkan input "2023/2024" dan "Ganjil" menjadi "2023/2024 Ganjil"
        $tahunTeks    = $this->request->getPost('tahun');
        $semesterTeks = $this->request->getPost('semester');
        $fullYearCode = trim($tahunTeks . ' ' . $semesterTeks);

        $this->tahunModel->save([
            'year_code' => $fullYearCode,
            'is_active' => 0 // Default saat dibuat selalu Tidak Aktif (0)
        ]);

        return redirect()->to(base_url('admin/akademik'))->with('msg', 'Tahun Akademik berhasil ditambahkan!');
    }

    public function setActiveTahun($id)
    {
        // 1. Matikan (set 0) semua tahun akademik di database
        $this->tahunModel->where('id >', 0)->set(['is_active' => 0])->update();

        // 2. Aktifkan (set 1) khusus untuk ID yang dipilih
        $this->tahunModel->update($id, ['is_active' => 1]);

        return redirect()->to(base_url('admin/akademik'))->with('msg', 'Status Tahun Akademik berhasil diubah menjadi Aktif!');
    }

    public function deleteTahun($id)
    {
        // Cek dulu apakah tahun yang mau dihapus sedang aktif
        $data = $this->tahunModel->find($id);
        if ($data && $data['is_active'] == 1) {
            return redirect()->to(base_url('admin/akademik'))->with('err', 'Gagal! Tahun akademik yang sedang AKTIF tidak boleh dihapus.');
        }

        $this->tahunModel->delete($id);
        return redirect()->to(base_url('admin/akademik'))->with('msg', 'Tahun Akademik berhasil dihapus!');
    }
}
