<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //datadummy
    private static $products = [
        ['id' => 1, 'nama' => 'Sepatu Adidas', 'harga' => 500000],
        ['id' => 2, 'nama' => 'Kaos Polos', 'harga' => 75000],
    ];
//get
    public function index() {
        return response()->json(self::$products, 200);
    }
    //get(id)
    public function show($id) {
        $product = collect(self::$products)->firstWhere('id', $id);
        
//errorHandling
        if (!$product) {
            return response()->json(["message" => "Item dengan ID {$id} tidak Ditemukan"], 404);
        }
        return response()->json($product, 200);
    }
//post
    public function store(Request $request) {

//validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:1000',
        ]);
//errorHandling
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $newProduct = [
            'id' => count(self::$products) + 1,
            'nama' => $request->nama,
            'harga' => $request->harga,
        ];

        return response()->json([
            "message" => "Item berhasil dibuat",
            "data" => $newProduct
        ], 201);
    }
//put
    public function update(Request $request, $id) {
        $productIndex = collect(self::$products)->search(fn($item) => $item['id'] == $id);
        
       //error handling
        if ($productIndex === false) {
            return response()->json(["message" => "Item dengan ID {$id} tidak Ditemukan"], 404);
        }

        //validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'harga' => 'required|numeric',
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 422);

        self::$products[$productIndex]['nama'] = $request->nama;
        self::$products[$productIndex]['harga'] = $request->harga;

        return response()->json([
            "message" => "Seluruh data barang berhasil diedit",
            "data" => self::$products[$productIndex]
        ], 200);
    }
//patch
    public function patch(Request $request, $id) {
        $productIndex = collect(self::$products)->search(fn($item) => $item['id'] == $id);
        
        //errorhandling
        if ($productIndex === false) {
            return response()->json(["message" => "Item dengan ID {$id} tidak Ditemukan"], 404);
        }

        //updateSebagian
        if ($request->has('nama')) {
            self::$products[$productIndex]['nama'] = $request->nama;
        }
        if ($request->has('harga')) {
            self::$products[$productIndex]['harga'] = $request->harga;
        }

        return response()->json([
            "message" => "Data barang berhasil diedit sebagian",
            "data" => self::$products[$productIndex]
        ], 200);
    }
//delete
    public function destroy($id) {
        $productIndex = collect(self::$products)->search(fn($item) => $item['id'] == $id);
        
        //error Handling
        if ($productIndex === false) {
            return response()->json(["message" => "Item dengan ID {$id} tidak Ditemukan"], 404);
        }

        return response()->json(["message" => "Barang dengan ID {$id} berhasil dihapus"], 200);
    }
}
