<?php

namespace App\Http\Controllers;

use App\Models\HistorySale;
use Illuminate\Http\Request;

class HistorySaleController extends Controller
{
    protected $item = 'History Sale';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.sales.history')
            ->with('item', $this->item);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.sales.history-create')
            ->with('item', $this->item);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'no_resi' => 'required|string|unique:history_sales,no_resi',
                'no_sku' => 'required|array',
                'no_sku.*' => 'nullable|string|min:10',
                'qty' => 'sometimes|array',
                'qty.*' => 'sometimes|integer|min:1',
            ]);

            // Filter out empty SKUs and check for duplicates within current form submission
            $skus = [];
            $quantities = [];
            $seenSkus = [];

            foreach ($validatedData['no_sku'] as $index => $sku) {
                if (!empty(trim($sku))) {
                    // Check for duplicate SKU in current submission only
                    if (in_array($sku, $seenSkus)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Duplicate SKU detected: ' . $sku
                        ], 422);
                    }

                    $seenSkus[] = $sku;
                    $skus[] = $sku;
                    $quantities[] = isset($validatedData['qty']) && isset($validatedData['qty'][$index])
                        ? (int)$validatedData['qty'][$index]
                        : 1;
                }
            }

            if (empty($skus)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'At least one SKU is required'
                ], 422);
            }

            $historySale = HistorySale::create([
                'no_resi' => $validatedData['no_resi'],
                'no_sku' => $skus,
                'qty' => $quantities,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'History Sale added successfully with ' . count($skus) . ' SKU(s)',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(HistorySale $historySale)
    {
        return view('backend.sales.history-show', compact('historySale'))
            ->with('item', $this->item);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $historySale = HistorySale::findOrFail($id);

            // Pastikan data no_sku dan qty di-decode hanya jika berbentuk string
            $historySale->no_sku = is_string($historySale->no_sku) ? json_decode($historySale->no_sku) : $historySale->no_sku;
            $historySale->qty = is_string($historySale->qty) ? json_decode($historySale->qty) : $historySale->qty;

            return response()->json([
                'status' => 'success',
                'data' => $historySale
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error loading history sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $historySale = HistorySale::findOrFail($id);

            $validatedData = $request->validate([
                'no_resi' => 'required|string|unique:history_sales,no_resi,' . $id,
                'no_sku' => 'required|array',
                'no_sku.*' => 'nullable|string',
                'qty' => 'required|array',
                'qty.*' => 'required|integer|min:1',
            ]);

            // Check for duplicate SKUs
            $skus = [];
            $quantities = [];
            $seenSkus = [];

            foreach ($validatedData['no_sku'] as $index => $sku) {
                if (!empty(trim($sku))) {
                    // Check for duplicate SKU
                    if (in_array($sku, $seenSkus)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Duplicate SKU detected: ' . $sku
                        ], 422);
                    }

                    $seenSkus[] = $sku;
                    $skus[] = $sku;
                    $quantities[] = $validatedData['qty'][$index] ?? 1;
                }
            }

            if (empty($skus)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'At least one SKU is required'
                ], 422);
            }

            $historySale->update([
                'no_resi' => $validatedData['no_resi'],
                'no_sku' => $skus,
                'qty' => $quantities,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'History Sale updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $historySale = HistorySale::findOrFail($id);
            $historySale->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'History Sale deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data for DataTables.
     */
    public function data()
    {
        try {
            $historySales = HistorySale::orderBy('created_at', 'desc')->get();

            $data = [];
            foreach ($historySales as $key => $historySale) {
                $skus = is_string($historySale->no_sku) ? json_decode($historySale->no_sku, true) : $historySale->no_sku;
                $quantities = is_string($historySale->qty) ? json_decode($historySale->qty, true) : $historySale->qty;

                $displayItems = array_map(function ($sku, $qty) {
                    return $sku . ' (Qty: ' . $qty . ')';
                }, $skus, $quantities);

                $data[] = [
                    'no' => $key + 1,
                    'id' => $historySale->id,
                    'no_resi' => $historySale->no_resi,
                    'no_sku' => implode('<br>', $displayItems),
                    'created_at' => $historySale->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $historySale->updated_at->format('Y-m-d H:i:s'),
                    'actions' => '
                        <button onclick="editHistorySale(' . $historySale->id . ')" class="btn btn-sm btn-primary">Edit</button>
                        <button onclick="deleteHistorySale(' . $historySale->id . ')" class="btn btn-sm btn-danger">Delete</button>
                    '
                ];
            }

            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data to Excel/CSV
     */
    public function export(Request $request)
    {
        try {
            $query = HistorySale::query();

            // Apply date range filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = $request->input('start_date') . ' 00:00:00';
                $endDate = $request->input('end_date') . ' 23:59:59';
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            // Process in chunks to avoid memory issues
            $data = [];
            $query->orderBy('created_at', 'desc')
                ->chunk(1000, function ($historySales) use (&$data) {
                    foreach ($historySales as $historySale) {
                        $skus = is_string($historySale->no_sku) ? json_decode($historySale->no_sku, true) : $historySale->no_sku;
                        $quantities = is_string($historySale->qty) ? json_decode($historySale->qty, true) : $historySale->qty;

                        $skuQtyPairs = [];
                        foreach ($skus as $index => $sku) {
                            $qty = $quantities[$index] ?? 1;
                            $skuQtyPairs[] = $sku . ' (Qty: ' . $qty . ')';
                        }

                        $data[] = [
                            'ID' => $historySale->id,
                            'No Resi' => $historySale->no_resi,
                            'SKU & Qty' => implode(', ', $skuQtyPairs),
                            'Created At' => $historySale->created_at->format('Y-m-d H:i:s'),
                            'Updated At' => $historySale->updated_at->format('Y-m-d H:i:s'),
                        ];
                    }
                });

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while exporting data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate no_resi
     */
    public function validateNoResi(Request $request)
    {
        try {
            $noResi = $request->input('no_resi');

            if (empty($noResi)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'No Resi is required'
                ], 422);
            }

            // Check if no_resi already exists
            $exists = HistorySale::where('no_resi', $noResi)->exists();

            return response()->json([
                'valid' => !$exists,
                'message' => $exists ? 'No Resi already exists' : 'No Resi is valid'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 500);
        }
    }
}
