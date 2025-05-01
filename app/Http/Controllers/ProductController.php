<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'category.required' => 'Please select a category for the supplier',
            'category.string' => 'Category must be text',
            'category.max' => 'Category name is too long (maximum is 255 characters)',

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
                ->select([
                    'id',
                    'category',
                    'code',
                    'product_name',
                    'unit',
                    'created_at',
                    'updated_at'
                ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->orderColumn('category', function ($query, $order) {
                    $query->orderBy('category', $order);
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
                ->filterColumn('category', function ($query, $keyword) {
                    $query->where('category', 'like', "%{$keyword}%");
                })
                ->filterColumn('code', function ($query, $keyword) {
                    $query->where('code', 'like', "%{$keyword}%");
                })
                ->filterColumn('product_name', function ($query, $keyword) {
                    $query->where('product_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('unit', function ($query, $keyword) {
                    $query->where('unit', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        // Get initial data for the view with pagination
        $items = [
            'Product List' => route('suppliers.index'),
        ];

        return view('product.index', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category'          => 'required|string|max:255',
                'code'              => 'required|string|max:255|unique:suppliers',
                'product_name'      => 'required|string|max:255',
                'unit'              => 'required|string|max:255',
            ], $this->getValidationMessages());

            $product = Supplier::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Great! The supplier has been successfully added to the system.',
                'data' => $product
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Oops! Something went wrong while adding the supplier. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $product)
    {
        try {
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry! We could not find the supplier information. Please refresh and try again.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $product)
    {
        try {
            $validated = $request->validate([
                'category' => 'required|string|max:255',
                'code' => 'required|string|max:255|unique:suppliers,code,' . $product->id,
                'product_name' => 'required|string|max:255',
                'unit' => 'required|string|max:255',
            ], $this->getValidationMessages());

            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Perfect! The supplier information has been successfully updated.',
                'data' => $product
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Oops! Something went wrong while updating the supplier. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $product)
    {
        try {
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'The supplier has been successfully removed from the system.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry! We could not delete the supplier at this time. Please try again.'
            ], 500);
        }
    }
}
