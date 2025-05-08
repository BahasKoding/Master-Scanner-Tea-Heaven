<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\CategorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'category_supplier_id.required' => 'Please select a category for the supplier',
            'category_supplier_id.exists' => 'The selected category does not exist',

            'code.required' => 'Please enter the supplier code',
            'code.string' => 'Supplier code must be text',
            'code.max' => 'Supplier code is too long (maximum is 255 characters)',
            'code.unique' => 'This supplier code is already in use. Please use a different code',

            'product_name.required' => 'Please enter the product name',
            'product_name.string' => 'Product name must be text',
            'product_name.max' => 'Product name is too long (maximum is 255 characters)',

            'unit.required' => 'Please select a unit for the product',
            'unit.string' => 'Unit must be text',
            'unit.max' => 'Unit name is too long (maximum is 255 characters)',
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
            'Supplier List' => route('suppliers.index'),
        ];

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

            return response()->json([
                'success' => true,
                'message' => 'Great! The supplier has been successfully added to the system.',
                'data' => $supplier
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating supplier', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Oops! Something went wrong while adding the supplier. Please try again.'
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

            Log::info('Edit supplier request received', ['supplier' => $supplier->toArray()]);

            return response()->json([
                'success' => true,
                'data' => $supplier
            ]);
        } catch (\Exception $e) {
            Log::error('Error in edit supplier', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry! We could not find the supplier information. Please refresh and try again.'
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

            $supplier->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Perfect! The supplier information has been successfully updated.',
                'data' => $supplier
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating supplier', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Oops! Something went wrong while updating the supplier. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'The supplier has been successfully removed from the system.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting supplier', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Sorry! We could not delete the supplier at this time. Please try again.'
            ], 500);
        }
    }
}
