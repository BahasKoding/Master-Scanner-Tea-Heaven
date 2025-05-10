<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class LabelController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Labels List', ['only' => ['index']]);
        $this->middleware('permission:Labels Create', ['only' => ['store']]);
        $this->middleware('permission:Labels Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Labels Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Labels View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'name.required' => 'Silahkan masukkan nama label',
            'name.string' => 'Nama label harus berupa teks',
            'name.max' => 'Nama label terlalu panjang (maksimal 255 karakter)',
            'name.unique' => 'Nama label ini sudah digunakan. Silahkan gunakan nama yang berbeda',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Label::query()
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
            'Daftar Label' => route('labels.index'),
        ];

        // Log activity
        addActivity('label', 'view', 'Pengguna melihat daftar label', null);

        return view('label.index', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:labels',
            ], $this->getValidationMessages());

            $label = Label::create($validated);

            // Log activity
            addActivity('label', 'create', 'Pengguna membuat label baru: ' . $label->name, $label->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Label telah ditambahkan ke dalam sistem.',
                'data' => $label
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
                'message' => 'Maaf! Terjadi kesalahan saat menambahkan label. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Label $label)
    {
        try {
            Log::info('Permintaan edit label diterima', ['label' => $label->toArray()]);

            // Log activity
            addActivity('label', 'edit', 'Pengguna melihat form edit label: ' . $label->name, $label->id);

            return response()->json([
                'success' => true,
                'data' => $label
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit label', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi label. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Label $label)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:labels,name,' . $label->id,
            ], $this->getValidationMessages());

            $oldName = $label->name;
            $label->update($validated);

            // Log activity
            addActivity('label', 'update', 'Pengguna mengubah label dari "' . $oldName . '" menjadi "' . $label->name . '"', $label->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Informasi label telah diperbarui.',
                'data' => $label
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
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui label. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Label $label)
    {
        try {
            $labelName = $label->name;
            $labelId = $label->id;

            // Check if this label is being used by any category products
            $categoryProductsCount = $label->categoryProducts()->count();

            if ($categoryProductsCount > 0) {
                // Get category product names to show in the error message
                $categoryProducts = $label->categoryProducts()
                    ->select('name')
                    ->take(3)
                    ->get()
                    ->pluck('name')
                    ->toArray();

                $categoryProductNames = count($categoryProducts) > 0
                    ? '"' . implode('", "', $categoryProducts) . '"' . (count($categoryProducts) < $categoryProductsCount ? ' dan ' . ($categoryProductsCount - count($categoryProducts)) . ' lainnya' : '')
                    : '';

                // Label is in use, prevent deletion
                // Log activity
                addActivity('label', 'delete_failed', 'Pengguna mencoba menghapus label yang sedang digunakan: ' . $labelName, $labelId);

                return response()->json([
                    'success' => false,
                    'message' => 'Label ini tidak dapat dihapus karena sedang digunakan oleh ' . $categoryProductsCount . ' produk' .
                        ($categoryProductNames ? ' termasuk: ' . $categoryProductNames : '') . '. Silahkan ubah label produk tersebut terlebih dahulu.',
                    'category_products_count' => $categoryProductsCount
                ], 422);
            }

            $label->delete();

            // Log activity
            addActivity('label', 'delete', 'Pengguna menghapus label: ' . $labelName, $labelId);

            return response()->json([
                'success' => true,
                'message' => 'Label telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus label saat ini. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * List all products for a specific label
     */
    public function listProducts(Label $label)
    {
        try {
            // Get all category products for this label with pagination
            $products = $label->categoryProducts()
                ->select(['id', 'name', 'created_at'])
                ->orderBy('name')
                ->paginate(15);

            // Log activity
            addActivity('label', 'list_products', 'Pengguna melihat produk untuk label: ' . $label->name, $label->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'label' => $label->name,
                    'products' => $products
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk: ' . $e->getMessage()
            ], 500);
        }
    }
}