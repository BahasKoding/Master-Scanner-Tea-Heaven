<?php

namespace App\Http\Controllers;

use App\Models\CategorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class CategorySupplierController extends Controller
{
    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'name.required' => 'Please enter the category name',
            'name.string' => 'Category name must be text',
            'name.max' => 'Category name is too long (maximum is 255 characters)',
            'name.unique' => 'This category name is already in use. Please use a different name',
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
            'Category Supplier List' => route('category-suppliers.index'),
        ];

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

            return response()->json([
                'success' => true,
                'message' => 'Great! The category has been successfully added to the system.',
                'data' => $categorySupplier
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
                'message' => 'Oops! Something went wrong while adding the category. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategorySupplier $categorySupplier)
    {
        try {
            Log::info('Edit category supplier request received', ['categorySupplier' => $categorySupplier->toArray()]);

            return response()->json([
                'success' => true,
                'data' => $categorySupplier
            ]);
        } catch (\Exception $e) {
            Log::error('Error in edit category supplier', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry! We could not find the category information. Please refresh and try again.'
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

            $categorySupplier->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Perfect! The category information has been successfully updated.',
                'data' => $categorySupplier
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
                'message' => 'Oops! Something went wrong while updating the category. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategorySupplier $categorySupplier)
    {
        try {
            $categorySupplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'The category has been successfully removed from the system.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry! We could not delete the category at this time. Please try again.'
            ], 500);
        }
    }
}
