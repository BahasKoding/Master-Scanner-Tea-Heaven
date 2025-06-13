<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ukuran',
        'jumlah',
        'stok_awal',
        'stok_masuk',
        'produksi',
        'defect',
        'sisa',
        'status'
    ];

    /**
     * Get the product that owns the sticker.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get purchase stickers for this sticker's product and ukuran
     */
    public function purchaseStickers()
    {
        return $this->hasMany(PurchaseSticker::class, 'product_id', 'product_id')
            ->where('ukuran_stiker', $this->ukuran);
    }

    /**
     * Get catatan produksi records for this product
     */
    public function catatanProduksi()
    {
        return $this->hasMany(CatatanProduksi::class, 'product_id', 'product_id');
    }

    /**
     * Get dynamic stok_masuk from purchase_stickers
     */
    public function getStokMasukDynamicAttribute()
    {
        return $this->purchaseStickers()->sum('stok_masuk');
    }

    /**
     * Get dynamic produksi from catatan_produksi
     * Summing all quantity from production records
     */
    public function getProduksiDynamicAttribute()
    {
        return $this->catatanProduksi()->sum('quantity');
    }

    /**
     * Get auto-calculated status based on sisa
     */
    public function getAutoStatusAttribute()
    {
        if ($this->sisa_dynamic < 30) {
            return 'need_order';
        }
        return 'available';
    }

    /**
     * Calculate total sisa dynamically
     * Formula: stok_awal + stok_masuk_dynamic - produksi_dynamic - defect
     * Note: produksi mengurangi stok sticker karena sticker digunakan untuk produksi
     */
    public function getSisaDynamicAttribute()
    {
        return $this->stok_awal + $this->stok_masuk_dynamic - $this->produksi_dynamic - $this->defect;
    }

    /**
     * Get status options for stickers
     */
    public static function getStatusOptions()
    {
        return [
            'available' => 'Available',
            'need_order' => 'Need Order',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'out_of_stock' => 'Out of Stock'
        ];
    }

    /**
     * Get the ukuran sticker options based on packaging
     * P1 = EXTRA SMALL PACK, T1 = TIN CANISTER, T2 = TIN CANISTER CUSTOM, No Packaging = JAPANESE TEABAGS
     */
    public static function getUkuranSticker()
    {
        return [
            'P1' => "5 X 17",      // EXTRA SMALL PACK (15-100 GRAM) -> 14 sticker per A3
            'T1' => "11.5 X 5.7",  // TIN CANISTER SERIES -> 17 sticker per A3
            'T2' => "13 X 5",      // TIN CANISTER SERIES (CUSTOM) -> 16 sticker per A3
            '-' => "10 X 3"        // JAPANESE TEABAGS (No packaging) -> 42 sticker per A3
        ];
    }

    /**
     * Get jumlah sticker per A3 based on packaging
     */
    public static function getJumlahPerA3()
    {
        return [
            'P1' => 14,   // 5 X 17
            'T1' => 17,   // 11.5 X 5.7
            'T2' => 16,   // 13 X 5
            '-' => 42     // 10 X 3
        ];
    }

    /**
     * Get products that are eligible for stickers
     * Based on labels: EXTRA SMALL PACK (1), TIN CANISTER SERIES (5), JAPANESE TEABAGS (10)
     * And specific packaging requirements
     */
    public static function getEligibleProducts()
    {
        return Product::whereIn('label', [1, 2, 5, 10]) // Include label 2 for consistency
            ->whereIn('packaging', ['P1', 'T1', 'T2', '-'])
            ->orderBy('name_product')
            ->get();
    }

    /**
     * Get products that are eligible for stickers but don't have stickers yet
     * This is used for the add sticker form to prevent duplicate stickers
     */
    public static function getAvailableProductsForStickers()
    {
        return Product::whereIn('label', [1, 2, 5, 10]) // Include label 2 for consistency
            ->whereIn('packaging', ['P1', 'T1', 'T2', '-'])
            ->whereDoesntHave('stickers') // Only products without existing stickers
            ->orderBy('name_product')
            ->get();
    }

    /**
     * Check if a product is eligible for stickers
     * This method provides a centralized way to check eligibility
     */
    public static function isProductEligible($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return false;
        }

        // Check label eligibility
        $eligibleLabels = [1, 2, 5, 10];
        if (!in_array($product->label, $eligibleLabels)) {
            return false;
        }

        // Check packaging eligibility
        $eligiblePackaging = ['P1', 'T1', 'T2', '-'];
        if (!in_array($product->packaging, $eligiblePackaging)) {
            return false;
        }

        return true;
    }

    /**
     * Update produksi automatically based on catatan produksi
     * This method can be called when CatatanProduksi is created/updated/deleted
     */
    public function updateProduksiFromCatatanProduksi()
    {
        // Calculate total production quantity for this product
        $totalProduksi = $this->catatanProduksi()->sum('quantity');

        // Update the produksi field
        $this->update(['produksi' => $totalProduksi]);

        return $this;
    }

    /**
     * Create or update sticker data for a product
     * This method ensures that when a product is used in production, its sticker record exists
     */
    public static function ensureStickerExists($productId)
    {
        // Use centralized eligibility check
        if (!static::isProductEligible($productId)) {
            return null; // Product is not eligible for stickers
        }

        $sticker = static::where('product_id', $productId)->first();

        if (!$sticker) {
            $product = Product::find($productId);

            // Determine sticker specifications based on packaging
            $ukuranMapping = static::getUkuranSticker();
            $jumlahMapping = static::getJumlahPerA3();

            $packaging = $product->packaging ?: '-';
            $ukuran = $ukuranMapping[$packaging] ?? '10 X 3';
            $jumlah = $jumlahMapping[$packaging] ?? 42;

            $sticker = static::create([
                'product_id' => $productId,
                'ukuran' => $ukuran,
                'jumlah' => (string) $jumlah,
                'stok_awal' => 0,
                'stok_masuk' => 0,
                'produksi' => 0,
                'defect' => 0,
                'sisa' => 0,
                'status' => 'active'
            ]);
        }

        // Update produksi from catatan produksi
        $sticker->updateProduksiFromCatatanProduksi();

        return $sticker;
    }
}
