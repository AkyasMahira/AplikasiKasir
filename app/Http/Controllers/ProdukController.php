<?php

namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Milon\Barcode\Facades\DNS1DFacade;

class ProdukController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $title = 'Produk';
        $subtitle = 'Index';
        $produks = Produk::all();
        return view('admin.produk.index', compact('title', 'subtitle', 'produks'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $title = 'Produk';
        $subtitle = 'Create';
        return view('admin.produk.create', compact('title', 'subtitle'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'NamaProduk' => 'required',
            'Harga' => 'required|numeric',
            'Stok' => 'required|numeric',
        ]);

        $validate['Users_id'] = Auth::user()->id;
        $simpan = Produk::create($validate);

        return $simpan
            ? response()->json(['status' => 200, 'message' => 'Produk berhasil ditambahkan'])
            : response()->json(['status' => 500, 'message' => 'Produk gagal ditambahkan']);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $title = 'Produk';
        $subtitle = 'Edit';
        $produk = Produk::findOrFail($id);

        return view('admin.produk.edit', compact('title', 'subtitle', 'produk'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $validate = $request->validate([
            'NamaProduk' => 'required',
            'Harga' => 'required|numeric',
            'Stok' => 'required|numeric',
        ]);

        $validate['Users_id'] = Auth::user()->id;
        $simpan = $produk->update($validate);

        return $simpan
            ? response()->json(['status' => 200, 'message' => 'Produk berhasil diupdate'])
            : response()->json(['status' => 500, 'message' => 'Produk gagal diupdate']);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);

        if ($produk) {
            $produk->delete();
            return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
        }
        return redirect()->route('produk.index')->with('error', 'Produk tidak ditemukan');
    }

    /**
     * Update stock for the specified product.
     */

    public function tambahStok(Request $request, $id)
    {
        // Validasi permintaan
        $request->validate([
            'Stok' => 'required|integer|min:1', // Validasi untuk memastikan stok yang ditambahkan adalah angka positif
        ]);

        $produk = Produk::find($id);

        // Tambah stok pada produk
        $produk->Stok += $request->Stok;

        // Simpan perubahan stok
        if ($produk->save()) {
            return response()->json(['status' => 200, 'message' => 'Stok berhasil ditambahkan']);
        } else {
            return response()->json(['status' => 500, 'message' => 'Stok gagal ditambahkan']);
        }
    }

    // public function updateStok(Request $request, $id)
    // {
    //     // Validasi permintaan
    //     $request->validate([
    //         'Stok' => 'required|numeric|min:1', // Validasi untuk memastikan stok yang diterima adalah angka positif
    //     ]);

    //     // Temukan produk berdasarkan ID
    //     $produk = Produk::find($id);

    //     // Update stok produk dengan nilai baru
    //     $produk->Stok = $request->Stok;

    //     // Simpan perubahan stok
    //     if ($produk->save()) {
    //         return response()->json(['status' => 200, 'message' => 'Stok berhasil diupdate']);
    //     } else {
    //         return response()->json(['status' => 500, 'message' => 'Stok gagal diupdate']);
    //     }
    // }


    /**
     * Display the stock log for products.
     */
    public function logproduk()
    {
        $title = 'Produk';
        $subtitle = 'Log Produk';
        $produks = LogStok::join('produks', 'log_stoks.ProdukId', '=', 'produks.id')
            ->join('users', 'log_stoks.UsersId', '=', 'users.id')
            ->select('log_stoks.JumlahProduk', 'log_stoks.created_at', 'produks.NamaProduk', 'users.name')
            ->get();

        return view('admin.produk.logproduk', compact('title', 'subtitle', 'produks'));
    }

    public function cetakLabel(Request $request)
    {
        $id_produk = $request->id_produk;
        $barcodes = [];

        if (is_array($id_produk)) {
            foreach ($id_produk as $id) {
                $id = (string) $id;
                $harga = Produk::find($id)->harga;
                $barcode = DNS1DFacade::getBarcodeHTML($id, 'C128');
                $barcodes[] = ['barcode' => $barcode, 'harga' => $harga];
            }
        } else {
            $id_produk = (string) $id_produk;
            $harga = Produk::find($id_produk)->harga;
            $barcode = DNS1DFacade::getBarcodeHTML($id_produk, 'C128');
            $barcodes[] = ['barcode' => $barcode, 'harga' => $harga];
        }
        $pdf = Pdf::loadView('admin.produk.cetakLabel', compact('barcodes'));

        $file_path = storage_path('app/public/barcodes.pdf');
        $pdf->save($file_path);

        return response()->json(['url' => asset('storage/barcodes.pdf')]);
    }
}
