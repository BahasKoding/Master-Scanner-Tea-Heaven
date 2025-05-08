<?php

namespace App\Http\Controllers;

use App\Models\CategorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CategorySupplierController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Category Suppliers List', ['only' => ['index']]);
        $this->middleware('permission:Category Suppliers Create', ['only' => ['store']]);
        $this->middleware('permission:Category Suppliers Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Category Suppliers Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Category Suppliers View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'name.required' => 'Silahkan masukkan nama kategori',
            'name.string' => 'Nama kategori harus berupa teks',
            'name.max' => 'Nama kategori terlalu panjang (maksimal 255 karakter)',
            'name.unique' => 'Nama kategori ini sudah digunakan. Silahkan gunakan nama yang berbeda',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CategorySupplier::query()
                ->select([
                    'id',
                    'name',
                    'created_at',
                    'updated_at'
                ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('name', $order);
                })
                ->addColumn('action', function ($row) {
                    return $row->id;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '';
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        // Get initial data for the view with pagination
        $items = [
            'Daftar Kategori Supplier' => route('category-suppliers.index'),
        ];

        // Log activity
        addActivity('category_supplier', 'view', 'Pengguna melihat daftar kategori supplier', null);

        return view('category-supplier.index', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:category_suppliers',
            ], $this->getValidationMessages());

            $categorySupplier = CategorySupplier::create($validated);

            // Log activity
            addActivity('category_supplier', 'create', 'Pengguna membuat kategori supplier baru: ' . $categorySupplier->name, $categorySupplier->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Kategori telah ditambahkan ke dalam sistem.',
                'data' => $categorySupplier
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat menambahkan kategori. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategorySupplier $categorySupplier)
    {
        try {
            Log::info('Permintaan edit kategori supplier diterima', ['categorySupplier' => $categorySupplier->toArray()]);

            // Log activity
            addActivity('category_supplier', 'edit', 'Pengguna melihat form edit kategori supplier: ' . $categorySupplier->name, $categorySupplier->id);

            return response()->json([
                'success' => true,
                'data' => $categorySupplier
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit kategori supplier', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi kategori. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategorySupplier $categorySupplier)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:category_suppliers,name,' . $categorySupplier->id,
            ], $this->getValidationMessages());

            $oldName = $categorySupplier->name;
            $categorySupplier->update($validated);

            // Log activity
            addActivity('category_supplier', 'update', 'Pengguna mengubah kategori supplier dari "' . $oldName . '" menjadi "' . $categorySupplier->name . '"', $categorySupplier->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Informasi kategori telah diperbarui.',
                'data' => $categorySupplier
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui kategori. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategorySupplier $categorySupplier)
    {
        try {
            $categoryName = $categorySupplier->name;
            $categoryId = $categorySupplier->id;

            // Check if this category is being used by any suppliers using the relationship
            $suppliersCount = $categorySupplier->suppliers()->count();

            if ($suppliersCount > 0) {
                // Get supplier names to show in the error message
                $suppliers = $categorySupplier->suppliers()
                    ->select('product_name')
                    ->take(3)
                    ->get()
                    ->pluck('product_name')
                    ->toArray();

                $supplierNames = count($suppliers) > 0
                    ? '"' . implode('", "', $suppliers) . '"' . (count($suppliers) < $suppliersCount ? ' dan ' . ($suppliersCount - count($suppliers)) . ' lainnya' : '')
                    : '';

                // Category is in use, prevent deletion
                // Log activity
                addActivity('category_supplier', 'delete_failed', 'Pengguna mencoba menghapus kategori supplier yang sedang digunakan: ' . $categoryName, $categoryId);

                return response()->json([
                    'success' => false,
                    'message' => 'Kategori ini tidak dapat dihapus karena sedang digunakan oleh ' . $suppliersCount . ' supplier' . ($suppliersCount > 1 ? '' : '') .
                        ($supplierNames ? ' termasuk: ' . $supplierNames : '') . '. Silahkan ubah kategori atau hapus supplier tersebut terlebih dahulu.',
                    'suppliers_count' => $suppliersCount
                ], 422);
            }

            $categorySupplier->delete();

            // Log activity
            addActivity('category_supplier', 'delete', 'Pengguna menghapus kategori supplier: ' . $categoryName, $categoryId);

            return response()->json([
                'success' => true,
                'message' => 'Kategori telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus kategori saat ini. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * List all suppliers for a specific category
     */
    public function listSuppliers(CategorySupplier $categorySupplier)
    {
        try {
            // Get all suppliers for this category with pagination
            $suppliers = $categorySupplier->suppliers()
                ->select(['id', 'code', 'product_name', 'unit'])
                ->orderBy('product_name')
                ->paginate(15);

            // Log activity
            addActivity('category_supplier', 'list_suppliers', 'Pengguna melihat supplier untuk kategori: ' . $categorySupplier->name, $categorySupplier->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $categorySupplier->name,
                    'suppliers' => $suppliers
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data supplier: ' . $e->getMessage()
            ], 500);
        }
    }
}
