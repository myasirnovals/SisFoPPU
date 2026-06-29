<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GradeScaleModel;

class HurufMutu extends BaseController
{
    protected $gradeScaleModel;
    protected $helpers = ['form'];

    public function __construct()
    {
        $this->gradeScaleModel = new GradeScaleModel();
    }

    public function index()
    {
        $data = [
            'title'        => 'Pengaturan Huruf Mutu - Sisfo Praktikum',
            'gradeScales'  => $this->gradeScaleModel->orderBy('min_score', 'DESC')->findAll(),
            'validation'   => \Config\Services::validation(),
        ];

        return view('admin/huruf_mutu', $data);
    }

    public function store()
    {
        $rules = [
            'grade_letter' => 'required|max_length[10]',
            'min_score'    => 'required|decimal|less_than_equal_to[100]',
            'max_score'    => 'required|decimal|less_than_equal_to[100]',
            'is_passing'   => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Cek overlap sederhana (opsional, bisa diperketat di validation custom)
        $this->gradeScaleModel->insert([
            'scale_code'   => $this->request->getPost('grade_letter'),
            'grade_letter' => $this->request->getPost('grade_letter'),
            'min_score'    => $this->request->getPost('min_score'),
            'max_score'    => $this->request->getPost('max_score'),
            'grade_point'  => $this->request->getPost('grade_point') ?: 0.00,
            'predicate'    => $this->request->getPost('predicate'),
            'is_passing'   => $this->request->getPost('is_passing'),
            'is_default'   => 0,
            'is_active'    => 1,
        ]);

        return redirect()->to('/admin/hurufmutu')->with('success', 'Rentang huruf mutu berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        if (! $id || ! $this->gradeScaleModel->find($id)) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'grade_letter' => 'required|max_length[10]',
            'min_score'    => 'required|decimal',
            'max_score'    => 'required|decimal',
            'is_passing'   => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->gradeScaleModel->update($id, [
            'scale_code'   => $this->request->getPost('grade_letter'),
            'grade_letter' => $this->request->getPost('grade_letter'),
            'min_score'    => $this->request->getPost('min_score'),
            'max_score'    => $this->request->getPost('max_score'),
            'grade_point'  => $this->request->getPost('grade_point') ?: 0.00,
            'predicate'    => $this->request->getPost('predicate'),
            'is_passing'   => $this->request->getPost('is_passing'),
        ]);

        return redirect()->to('/admin/hurufmutu')->with('success', 'Rentang huruf mutu berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if (! $id || ! $this->gradeScaleModel->find($id)) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $this->gradeScaleModel->delete($id);

        return redirect()->to('/admin/hurufmutu')->with('success', 'Rentang huruf mutu berhasil dihapus.');
    }
}
