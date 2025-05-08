<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\CategorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Suppliers List', ['only' => ['index']]);
        $this->middleware('permission:Suppliers Create', ['only' => ['store']]);
        $this->middleware('permission:Suppliers Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Suppliers Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Suppliers View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'category_supplier_id.required' => 'Silahkan pilih kategori untuk supplier',
            'category_supplier_id.exists' => 'Kategori yang dipilih tidak ditemukan',

            'code.required' => 'Silahkan masukkan kode supplier',
            'code.string' => 'Kode supplier harus berupa teks',
            'code.max' => 'Kode supplier terlalu panjang (maksimal 255 karakter)',
            'code.unique' => 'Kode supplier ini sudah digunakan. Silahkan gunakan kode yang berbeda',

            'product_name.required' => 'Silahkan masukkan nama produk',
            'product_name.string' => 'Nama produk harus berupa teks',
            'product_name.max' => 'Nama produk terlalu panjang (maksimal 255 karakter)',

            'unit.required' => 'Silahkan pilih satuan untuk produk',
            'unit.string' => 'Satuan harus berupa teks',
            'unit.max' => 'Nama satuan terlalu panjang (maksimal 255 karakter)',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Supplier::query()
                ->join('category_suppliers', 'suppliers.category_supplier_id', '=', 'category_suppliers.id')
                ->select([
                    'suppliers.id',
                    'suppliers.category_supplier_id',
                    'suppliers.code',
                    'suppliers.product_name',
                    'suppliers.unit',
                    'suppliers.created_at',
                    'suppliers.updated_at',
                    'category_suppliers.name as category_name'
                ]);

            // Apply filters from request
            if ($request->filled('category')) {
                $query->where('category_suppliers.name', $request->category);
            }

            if ($request->filled('code')) {
                $query->where('suppliers.code', 'like', '%' . $request->code . '%');
            }

            if ($request->filled('product_name')) {
                $query->where('suppliers.product_name', 'like', '%' . $request->product_name . '%');
            }

            if ($request->filled('unit')) {
                $query->where('suppliers.unit', $request->unit);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('suppliers.created_at', $order);
                })
                ->addColumn('category', function ($row) {
                    return $row->category_name;
                })
                ->addColumn('action', function ($row) {
                    return $row->id;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
                })
                ->filterColumn('category', function ($query, $keyword) {
                    $query->where('category_suppliers.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('code', function ($query, $keyword) {
                    $query->where('suppliers.code', 'like', "%{$keyword}%");
                })
                ->filterColumn('product_name', function ($query, $keyword) {
                    $query->where('suppliers.product_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('unit', function ($query, $keyword) {
                    $query->where('suppliers.unit', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        // Get categories for the dropdown
        $categories = CategorySupplier::orderBy('name')->get();

        // Get initial data for the view with pagination
        $items = [
            'Daftar Supplier' => route('suppliers.index'),
        ];

        // Log activity
        addActivity('supplier', 'view', 'Pengguna melihat daftar supplier', null);

        return view('supplier.index', compact('items', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_supplier_id' => 'required|exists:category_suppliers,id',
                'code'                 => 'required|string|max:255|unique:suppliers',
                'product_name'         => 'required|string|max:255',
                'unit'                 => 'required|string|max:255',
            ], $this->getValidationMessages());

            $supplier = Supplier::create($validated);

            // Log activity
            addActivity('supplier', 'create', 'Pengguna membuat supplier baru: ' . $supplier->product_name, $supplier->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Supplier telah ditambahkan ke dalam sistem.',
                'data' => $supplier
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat membuat supplier', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat menambahkan supplier. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        try {
            // Eager load the category
            $supplier->load('categorySupplier');

            Log::info('Permintaan edit supplier diterima', ['supplier' => $supplier->toArray()]);

            // Log activity
            addActivity('supplier', 'edit', 'Pengguna melihat form edit supplier: ' . $supplier->product_name, $supplier->id);

            return response()->json([
                'success' => true,
                'data' => $supplier
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit supplier', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi supplier. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        try {
            $validated = $request->validate([
                'category_supplier_id' => 'required|exists:category_suppliers,id',
                'code'                 => 'required|string|max:255|unique:suppliers,code,' . $supplier->id,
                'product_name'         => 'required|string|max:255',
                'unit'                 => 'required|string|max:255',
            ], $this->getValidationMessages());

            // Store old values for logging
            $oldValues = $supplier->toArray();

            $supplier->update($validated);

            // Log activity
            addActivity('supplier', 'update', 'Pengguna mengubah supplier: ' . $supplier->product_name, $supplier->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Informasi supplier telah diperbarui.',
                'data' => $supplier
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui supplier', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui supplier. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $supplierName = $supplier->product_name;
            $supplierId = $supplier->id;

            $supplier->delete();

            // Log activity
            addActivity('supplier', 'delete', 'Pengguna menghapus supplier: ' . $supplierName, $supplierId);

            return response()->json([
                'success' => true,
                'message' => 'Supplier telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menghapus supplier', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus supplier saat ini. Silahkan coba lagi.'
            ], 500);
        }
    }
}
