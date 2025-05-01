<?php

namespace App\Http\Controllers;

use App\Models\HistorySale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                'no_sku.*' => 'nullable|string|min:3',
                'qty' => 'sometimes|array',
                'qty.*' => 'sometimes|integer|min:1',
            ]);

            // Filter out empty SKUs and check for duplicates
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
                    $quantities[] = isset($validatedData['qty']) && isset($validatedData['qty'][$index])
                        ? (int)$validatedData['qty'][$index]
                        : 1;
                }
            }

            // Create record even if no SKUs are provided
            $historySale = HistorySale::create([
                'no_resi' => $validatedData['no_resi'],
                'no_sku' => $skus,
                'qty' => $quantities,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'History Sale added successfully' . (count($skus) > 0 ? ' with ' . count($skus) . ' SKU(s)' : ''),
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
     * Display the report view.
     */
    public function report()
    {
        return view('backend.sales.report')
            ->with('item', $this->item);
    }

    /**
     * Get data for DataTables.
     */
    public function data(Request $request)
    {
        try {
            $query = HistorySale::query();

            // Handle date range filter
            if ($request->filled(['start_date', 'end_date'])) {
                $startDate = $request->input('start_date') . ' 00:00:00';
                $endDate = $request->input('end_date') . ' 23:59:59';
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            // Handle trashed records based on request
            if ($request->boolean('only_trashed')) {
                $query->onlyTrashed();
            } else {
                $query->whereNull('deleted_at');
            }

            // Get total records count (cap at a reasonable number to prevent timeouts)
            $totalRecords = $query->count();
            $maxRecordsForExport = 10000; // Set a reasonable limit

            // Handling pagination for normal view, but allowing all data for exports
            $start = $request->input('start', 0);
            $length = $request->input('length', 25);

            // Check if we're requesting all records
            $requestingAllRecords = $length == -1;

            // If requesting all records but there are too many, limit it
            if ($requestingAllRecords && $totalRecords > $maxRecordsForExport) {
                Log::warning("Large data export attempted. Total records: {$totalRecords}, limiting to {$maxRecordsForExport}");
                $length = $maxRecordsForExport;
                $requestingAllRecords = false;
            }

            // Handle search
            if ($searchValue = $request->input('search.value')) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('no_resi', 'like', "%{$searchValue}%")
                        ->orWhere('no_sku', 'like', "%{$searchValue}%");
                });
            }

            // Get filtered records count
            $filteredRecords = $query->count();

            // Handle ordering
            $orderColumn = $request->input('order.0.column', 4); // Default to created_at
            $orderDir = $request->input('order.0.dir', 'desc');
            $columns = ['no', 'no_resi', 'no_sku', 'qty', 'created_at', 'updated_at'];

            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }

            // Process data in chunks to avoid memory issues
            $data = [];
            $counter = $start + 1;

            if ($requestingAllRecords) {
                // For export all records, process in chunks
                $query->chunk(500, function ($historySales) use (&$data, &$counter, $request) {
                    foreach ($historySales as $historySale) {
                        $data[] = $this->formatHistorySaleForDataTable($historySale, $counter++, $request);
                    }
                });
            } else {
                // For regular pagination
                $historySales = $query->skip($start)->take($length)->get();
                foreach ($historySales as $key => $historySale) {
                    $data[] = $this->formatHistorySaleForDataTable($historySale, $start + $key + 1, $request);
                }
            }

            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error in data method: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Format a history sale record for DataTable display.
     */
    private function formatHistorySaleForDataTable($historySale, $counter, $request)
    {
        try {
            // Decode JSON data
            $skus = is_string($historySale->no_sku) ? json_decode($historySale->no_sku, true) : $historySale->no_sku;
            $quantities = is_string($historySale->qty) ? json_decode($historySale->qty, true) : $historySale->qty;

            if (!is_array($skus)) {
                Log::warning('Invalid SKU data for ID ' . $historySale->id, [
                    'skus' => $skus,
                    'raw_data' => $historySale->no_sku
                ]);
                $skus = [];
            }

            if (!is_array($quantities)) {
                $quantities = [];
            }

            // Format SKUs and quantities for display
            $skuDisplay = [];
            $qtyDisplay = [];
            foreach ($skus as $index => $sku) {
                $skuDisplay[] = $sku;
                $qtyDisplay[] = isset($quantities[$index]) ? $quantities[$index] : 1;
            }

            return [
                'DT_RowId' => 'row_' . $historySale->id,
                'no' => $counter,
                'id' => $historySale->id,
                'no_resi' => $historySale->no_resi,
                'no_sku' => implode('<br>', $skuDisplay),
                'qty' => implode('<br>', $qtyDisplay),
                'created_at' => $historySale->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $historySale->updated_at->format('Y-m-d H:i:s'),
                'deleted_at' => $historySale->deleted_at ? $historySale->deleted_at->format('Y-m-d H:i:s') : null,
                'actions' => $request->routeIs('history-sales.report') ? '' : $this->getActionButtons($historySale)
            ];
        } catch (\Exception $e) {
            Log::error('Error formatting history sale: ' . $e->getMessage(), [
                'history_sale_id' => $historySale->id ?? 'unknown',
                'exception' => $e
            ]);

            // Return a fallback record with error indication
            return [
                'DT_RowId' => 'row_error',
                'no' => $counter,
                'id' => $historySale->id ?? 'ERROR',
                'no_resi' => $historySale->no_resi ?? 'Error loading data',
                'no_sku' => 'Error: Could not load SKUs',
                'qty' => 'Error',
                'created_at' => $historySale->created_at ? $historySale->created_at->format('Y-m-d H:i:s') : '',
                'updated_at' => $historySale->updated_at ? $historySale->updated_at->format('Y-m-d H:i:s') : '',
                'deleted_at' => null,
                'actions' => ''
            ];
        }
    }

    /**
     * Get action buttons HTML for a history sale record.
     */
    private function getActionButtons($historySale)
    {
        if ($historySale->trashed()) {
            return '<div class="d-flex justify-content-center">
                        <button onclick="restoreHistorySale(' . $historySale->id . ')" class="btn btn-icon btn-success mx-1" title="Restore">
                            <i class="fas fa-trash-restore"></i>
                        </button>
                        <button onclick="forceDeleteHistorySale(' . $historySale->id . ')" class="btn btn-icon btn-danger mx-1" title="Delete Permanently">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';
        }

        return '<div class="d-flex justify-content-center">
                    <button onclick="editHistorySale(' . $historySale->id . ')" class="btn btn-icon btn-primary mx-1" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteHistorySale(' . $historySale->id . ')" class="btn btn-icon btn-danger mx-1" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>';
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

    /**
     * Restore a soft-deleted record.
     */
    public function restore($id)
    {
        try {
            $historySale = HistorySale::withTrashed()->findOrFail($id);
            $historySale->restore();

            return response()->json([
                'status' => 'success',
                'message' => 'History Sale restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete a record.
     */
    public function forceDelete($id)
    {
        try {
            $historySale = HistorySale::withTrashed()->findOrFail($id);
            $historySale->forceDelete();

            return response()->json([
                'status' => 'success',
                'message' => 'History Sale permanently deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
