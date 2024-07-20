<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;

class TransaksiMenuController extends BaseController
{
    protected $transaksi;

    function __construct()
    {
        $this->transaksi = new TransactionModel();
    }

    public function index()
    {
        $transaksi = $this->transaksi->findAll();
        $data['transaksi'] = $transaksi;

        return view('v_transaksi', $data);
    }

    public function edit($id)
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'status' => 'required|in_list[0,1]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $dataTransaksi = $this->transaksi->find($id);

        $dataForm = [
            'status' => $this->request->getPost('status'),
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $this->transaksi->update($id, $dataForm);

        return redirect()->to('transaksi')->with('success', 'Data Berhasil Diubah');
    }

    public function download()
    {
        $transaksi = $this->transaksi->findAll();

        $html = view('v_transaksiPDF', ['transaksi' => $transaksi]);

        $filename = date('y-m-d-H-i-s') . '-transaksi';

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        // load HTML content
        $dompdf->loadHtml($html);

        // (optional) setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // render html as PDF
        $dompdf->render();

        // output the generated pdf
        $dompdf->stream($filename);
    }
}
