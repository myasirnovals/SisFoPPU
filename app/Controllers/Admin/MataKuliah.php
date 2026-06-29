<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MataKuliahModel;

class MataKuliah extends BaseController
{
    protected MataKuliahModel $model;

    public function __construct()
    {
        $this->model = new MataKuliahModel();
    }

    // ─── READ ───────────────────────────────────────────────
    public function index()
    {
        $data = [
            'title'       => 'Manajemen Mata Kuliah - Sisfo Praktikum',
            'matakuliah'  => $this->model->orderBy('kode_mk', 'ASC')->findAll(),
        ];

        return view('admin/mata_kuliah', $data);
    }

    // ─── CREATE ─────────────────────────────────────────────
    public function store()
    {
        $rules = [
            'kode_mk'  => 'required|max_length[20]|is_unique[mata_kuliah.kode_mk]',
            'nama_mk'  => 'required|max_length[100]',
            'sks'      => 'required|integer|greater_than[0]|less_than[5]',
            'semester' => 'required|in_list[Ganjil,Genap]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('open_modal', 'tambah');
        }

        $this->model->insert([
            'kode_mk'  => strtoupper($this->request->getPost('kode_mk')),
            'nama_mk'  => $this->request->getPost('nama_mk'),
            'sks'      => $this->request->getPost('sks'),
            'semester' => $this->request->getPost('semester'),
        ]);

        return redirect()->to('/admin/matakuliah')
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    // ─── GET DATA FOR EDIT (JSON) ────────────────────────────
    public function edit($id)
    {
        $mk = $this->model->find($id);

        if (! $mk) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        return $this->response->setJSON(['status' => 'ok', 'data' => $mk]);
    }

    // ─── UPDATE ─────────────────────────────────────────────
    public function update($id)
    {
        $mk = $this->model->find($id);

        if (! $mk) {
            return redirect()->to('/admin/matakuliah')
                ->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'kode_mk'  => "required|max_length[20]|is_unique[mata_kuliah.kode_mk,id,{$id}]",
            'nama_mk'  => 'required|max_length[100]',
            'sks'      => 'required|integer|greater_than[0]|less_than[5]',
            'semester' => 'required|in_list[Ganjil,Genap]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('open_modal', 'edit')
                ->with('edit_id', $id);
        }

        $this->model->update($id, [
            'kode_mk'  => strtoupper($this->request->getPost('kode_mk')),
            'nama_mk'  => $this->request->getPost('nama_mk'),
            'sks'      => $this->request->getPost('sks'),
            'semester' => $this->request->getPost('semester'),
        ]);

        return redirect()->to('/admin/matakuliah')
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    // ─── DELETE ─────────────────────────────────────────────
    public function delete($id)
    {
        $mk = $this->model->find($id);

        if (! $mk) {
            return redirect()->to('/admin/matakuliah')
                ->with('error', 'Data tidak ditemukan.');
        }

        $this->model->delete($id);

        return redirect()->to('/admin/matakuliah')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
