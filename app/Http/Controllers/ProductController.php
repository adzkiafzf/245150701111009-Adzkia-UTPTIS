<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

#[OA\Info(title: "API E-Commerce UTP", version: "1.0.0", description: "Dokumentasi API untuk UTP dengan Mock Data")]
class ProductController extends Controller
{
    //datadummy
    private static $products = [
        ['id' => 1, 'nama' => 'Sepatu Adidas', 'harga' => 500000],
        ['id' => 2, 'nama' => 'Kaos Polos', 'harga' => 75000],
    ];

    //get
    #[OA\Get(path: '/api/products', summary: 'Menampilkan semua item barang', tags: ['Products'])]
    #[OA\Response(response: 200, description: 'Berhasil mengambil data')]
    public function index() {
        return response()->json(self::$products, 200);
    }

    //get(id)
    #[OA\Get(path: '/api/products/{id}', summary: 'Menampilkan item berdasarkan ID', tags: ['Products'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Berhasil mengambil data')]
    #[OA\Response(response: 404, description: 'Item tidak ditemukan')]
    public function show($id) {
        $product = collect(self::$products)->firstWhere('id', $id);

        //errorHandling
        if (!$product) {
            return response()->json(["message" => "Item dengan ID {$id} tidak Ditemukan"], 404);
        }
        return response()->json($product, 200);
    }

    //post
    #[OA\Post(path: '/api/products', summary: 'Membuat item baru', tags: ['Products'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
        new OA\Property(property: 'nama', type: 'string', example: 'Topi Baseball'),
        new OA\Property(property: 'harga', type: 'integer', example: 80000),
    ]))]
    #[OA\Response(response: 201, description: 'Item berhasil dibuat')]
    #[OA\Response(response: 422, description: 'Error validasi')]
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
    #[OA\Put(path: '/api/products/{id}', summary: 'Update seluruh data item', tags: ['Products'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
        new OA\Property(property: 'nama', type: 'string', example: 'Sepatu Nike'),
        new OA\Property(property: 'harga', type: 'integer', example: 600000),
    ]))]
    #[OA\Response(response: 200, description: 'Seluruh data barang berhasil diedit')]
    #[OA\Response(response: 404, description: 'Item tidak ditemukan')]
    #[OA\Response(response: 422, description: 'Error validasi')]
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
    #[OA\Patch(path: '/api/products/{id}', summary: 'Update sebagian data item', tags: ['Products'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: false, content: new OA\JsonContent(properties: [
        new OA\Property(property: 'nama', type: 'string', example: 'Sepatu Nike'),
        new OA\Property(property: 'harga', type: 'integer', example: 600000),
    ]))]
    #[OA\Response(response: 200, description: 'Data barang berhasil diedit sebagian')]
    #[OA\Response(response: 404, description: 'Item tidak ditemukan')]
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
    #[OA\Delete(path: '/api/products/{id}', summary: 'Hapus item berdasarkan ID', tags: ['Products'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Barang berhasil dihapus')]
    #[OA\Response(response: 404, description: 'Item tidak ditemukan')]
    public function destroy($id) {
        $productIndex = collect(self::$products)->search(fn($item) => $item['id'] == $id);

        //error Handling
        if ($productIndex === false) {
            return response()->json(["message" => "Item dengan ID {$id} tidak Ditemukan"], 404);
        }

        return response()->json(["message" => "Barang dengan ID {$id} berhasil dihapus"], 200);
    }
}
