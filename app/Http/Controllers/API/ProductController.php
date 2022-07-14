<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $res = [
            'status' => 'Data berhasil ditampilkan',
            'data' => $products
        ];
        return response()->json($res, 200);
    }

    // detail product
    public function show($id)
    {
        $product = Product::find($id);
        $res = [
            'status' => 'Data detail produk berhasil ditampilkan',
            'data' => $product
        ];
        return response()->json($res, 200);
    }

    // tambah data produk
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'description' => 'required|string',
        ]);

        // logika image
        $input = $request->all();
        if ($image = $request->file('image')) {
            $targetPath = 'images/';
            $img_name = time() . '.' . $image->getClientOriginalExtension();
            $image = $image->move($targetPath, $img_name);
            // $input['image'] = $img_name;
            $input['image'] = URL::to($targetPath . $img_name);
        }
        Product::create($input);

        $res = [
            'status' => 'Data produk berhasil ditambahkan',
            'data' => $input
        ];
        return response()->json($res, 200);
    }

    // update data produk
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string',
            'price' => 'integer',
            'description' => 'string',
        ]);

        // logika image
        $input = $request->all();
        $product = Product::find($id);
        if ($image = $request->file('image')) {
            unlink('images/' . $product->image);
            $targetPath = 'images/';
            $img_name = time() . '.' . $image->getClientOriginalExtension();
            $image = $image->move($targetPath, $img_name);
            $input['image'] = $img_name;
        } else {
            unset($input['image']);
        }

        $product->update($input);

        $res = [
            'status' => 'Data produk berhasil diupdate',
            'data' => $product
        ];
        return response()->json($res, 200);
    }

    // hapus data produk
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product->image) {
            unlink('images/' . $product->image);
        }
        $product->delete();

        $res = [
            'status' => 'Data produk berhasil dihapus',
        ];
        return response()->json($res, 200);
    }
}
