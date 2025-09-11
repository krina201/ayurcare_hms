<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\MasterMedicineType;
use App\Models\MasterMedicineCategory;
use App\Models\MasterManufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicineSearchService
{
    /**
     * Search for medicines with common functionality
     */
    public function searchMedicines(Request $request, string $module = 'general', array $additionalFields = [], int $limit = 10, array $filters = [])
    {
        try {
            // Log the incoming request
            Log::info("Medicine search request received from {$module}", [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'query' => $request->all(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'module' => $module
            ]);

            $query = trim($request->get('query', ''));

            if (empty($query) || strlen($query) < 2) {
                Log::info("Medicine search: Query too short or empty", [
                    'query' => $query,
                    'module' => $module
                ]);
                return response()->json([]);
            }

            // Sanitize the query to prevent SQL injection
            $query = strip_tags($query);

            // Log the search attempt for debugging
            Log::info("Medicine search attempt", [
                'query' => $query,
                'user_id' => Auth::id(),
                'timestamp' => now(),
                'module' => $module
            ]);

            // Check if Medicine model exists and is accessible
            if (!class_exists(Medicine::class)) {
                Log::error("Medicine model not found", ['module' => $module]);
                throw new \Exception("Medicine model not accessible");
            }

            // Check database connection
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                Log::error("Database connection failed: " . $e->getMessage(), ['module' => $module]);
                throw new \Exception("Database connection failed");
            }

            // Default fields to select
            $defaultFields = [
                'id',
                'name',
                'code',
                'medicine_type_id',
                'medicine_category_id',
                'manufacturer_id',
                'selling_price',
                'initial_stock_quantity',
                'strength_dosage',
                'status'
            ];

            // Merge additional fields if provided
            $selectFields = array_unique(array_merge($defaultFields, $additionalFields));

            // Build the query with relationships
            $medicinesQuery = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('code', 'LIKE', "%{$query}%")
                        ->orWhere('strength_dosage', 'LIKE', "%{$query}%");
                });

            // Apply additional filters
            foreach ($filters as $field => $value) {
                if (!empty($value)) {
                    $medicinesQuery->where($field, $value);
                }
            }

            // Apply default filters
            if (!isset($filters['status'])) {
                $medicinesQuery->where('status', 1); // Only active medicines by default
            }

            $medicines = $medicinesQuery
                ->select($selectFields)
                ->limit($limit)
                ->get();

            // Log the search results for debugging
            Log::info("Medicine search results", [
                'query' => $query,
                'count' => $medicines->count(),
                'module' => $module,
                'results' => $medicines->toArray()
            ]);

            // If no results found, return empty array
            if ($medicines->isEmpty()) {
                Log::info("Medicine search: No results found", [
                    'query' => $query,
                    'module' => $module
                ]);
                return response()->json([]);
            }

            // Format the results
            $formattedMedicines = $medicines->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->name ?? 'N/A',
                    'code' => $medicine->code ?? 'N/A',
                    'type' => $medicine->medicineType->name ?? 'N/A',
                    'category' => $medicine->medicineCategory->name ?? 'N/A',
                    'manufacturer' => $medicine->manufacturer->name ?? 'N/A',
                    'selling_price' => $medicine->selling_price ?? 0,
                    'stock_quantity' => $medicine->initial_stock_quantity ?? 0,
                    'strength_dosage' => $medicine->strength_dosage ?? 'N/A',
                    'measurement' => $medicine->measurement->name ?? 'unit',
                    'status' => $medicine->status ?? 0,
                    'expiry_date' => $medicine->expiry_date ?? null,
                    'minimum_stock_level' => $medicine->minimum_stock_level ?? 0,
                    'batch_number' => $medicine->batch_number ?? 'N/A',
                    'description' => $medicine->description ?? 'N/A'
                ];
            });

            Log::info("Medicine search: Returning formatted results", [
                'query' => $query,
                'formatted_count' => $formattedMedicines->count(),
                'module' => $module
            ]);

            return response()->json($formattedMedicines);
        } catch (\Exception $e) {
            Log::error('Medicine search error: ' . $e->getMessage(), [
                'query' => $request->get('query'),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'module' => $module
            ]);

            return response()->json([
                'error' => 'An error occurred while searching medicines',
                'message' => $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'module' => $module
                ]
            ], 500);
        }
    }

    /**
     * Search medicines by type (e.g., oils, herbs, medicines)
     */
    public function searchMedicinesByType(Request $request, string $module = 'general', int $medicineTypeId = null)
    {
        $filters = [];
        if ($medicineTypeId) {
            $filters['medicine_type_id'] = $medicineTypeId;
        }

        return $this->searchMedicines($request, $module, [], 10, $filters);
    }

    /**
     * Search medicines with stock availability
     */
    public function searchMedicinesWithStock(Request $request, string $module = 'general', bool $inStockOnly = true)
    {
        $additionalFields = ['initial_stock_quantity', 'minimum_stock_level', 'expiry_date'];

        $medicines = $this->searchMedicines($request, $module, $additionalFields);

        if ($inStockOnly && $medicines->getStatusCode() === 200) {
            $data = $medicines->getData();
            $filteredData = array_filter($data, function ($medicine) {
                return $medicine->stock_quantity > 0;
            });
            return response()->json(array_values($filteredData));
        }

        return $medicines;
    }

    /**
     * Get medicine by ID with full details
     */
    public function getMedicineById(int $medicineId, string $module = 'general')
    {
        try {
            $medicine = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])
                ->find($medicineId);

            if (!$medicine) {
                return response()->json([
                    'error' => 'Medicine not found'
                ], 404);
            }

            $formattedMedicine = [
                'id' => $medicine->id,
                'name' => $medicine->name ?? 'N/A',
                'code' => $medicine->code ?? 'N/A',
                'type' => $medicine->medicineType->name ?? 'N/A',
                'category' => $medicine->medicineCategory->name ?? 'N/A',
                'manufacturer' => $medicine->manufacturer->name ?? 'N/A',
                'selling_price' => $medicine->selling_price ?? 0,
                'stock_quantity' => $medicine->initial_stock_quantity ?? 0,
                'strength_dosage' => $medicine->strength_dosage ?? 'N/A',
                'measurement' => $medicine->measurement->name ?? 'unit',
                'status' => $medicine->status ?? 0,
                'expiry_date' => $medicine->expiry_date ?? null,
                'minimum_stock_level' => $medicine->minimum_stock_level ?? 0,
                'batch_number' => $medicine->batch_number ?? 'N/A',
                'description' => $medicine->description ?? 'N/A',
                'purchase_price' => $medicine->purchase_price ?? 0,
                'mrp' => $medicine->mrp ?? 0,
                'gst_percentage' => $medicine->gst_percentage ?? 0,
                'created_at' => $medicine->created_at ?? null,
                'updated_at' => $medicine->updated_at ?? null
            ];

            return response()->json($formattedMedicine);
        } catch (\Exception $e) {
            Log::error('Get medicine by ID error: ' . $e->getMessage(), [
                'medicine_id' => $medicineId,
                'user_id' => Auth::id(),
                'module' => $module
            ]);

            return response()->json([
                'error' => 'An error occurred while fetching medicine details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medicines by category
     */
    public function getMedicinesByCategory(Request $request, string $module = 'general', int $categoryId = null)
    {
        try {
            $query = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])
                ->where('status', 1);

            if ($categoryId) {
                $query->where('medicine_category_id', $categoryId);
            }

            $medicines = $query->get();

            $formattedMedicines = $medicines->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->name ?? 'N/A',
                    'code' => $medicine->code ?? 'N/A',
                    'type' => $medicine->medicineType->name ?? 'N/A',
                    'category' => $medicine->medicineCategory->name ?? 'N/A',
                    'manufacturer' => $medicine->manufacturer->name ?? 'N/A',
                    'selling_price' => $medicine->selling_price ?? 0,
                    'stock_quantity' => $medicine->initial_stock_quantity ?? 0,
                    'strength_dosage' => $medicine->strength_dosage ?? 'N/A',
                    'measurement' => $medicine->measurement->name ?? 'unit'
                ];
            });

            return response()->json($formattedMedicines);
        } catch (\Exception $e) {
            Log::error('Get medicines by category error: ' . $e->getMessage(), [
                'category_id' => $categoryId,
                'user_id' => Auth::id(),
                'module' => $module
            ]);

            return response()->json([
                'error' => 'An error occurred while fetching medicines by category',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get low stock medicines
     */
    public function getLowStockMedicines(string $module = 'general')
    {
        try {
            $medicines = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])
                ->where('status', 1)
                ->whereRaw('initial_stock_quantity <= minimum_stock_level')
                ->get();

            $formattedMedicines = $medicines->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->name ?? 'N/A',
                    'code' => $medicine->code ?? 'N/A',
                    'type' => $medicine->medicineType->name ?? 'N/A',
                    'category' => $medicine->medicineCategory->name ?? 'N/A',
                    'manufacturer' => $medicine->manufacturer->name ?? 'N/A',
                    'selling_price' => $medicine->selling_price ?? 0,
                    'stock_quantity' => $medicine->initial_stock_quantity ?? 0,
                    'minimum_stock_level' => $medicine->minimum_stock_level ?? 0,
                    'strength_dosage' => $medicine->strength_dosage ?? 'N/A',
                    'measurement' => $medicine->measurement->name ?? 'unit'
                ];
            });

            return response()->json($formattedMedicines);
        } catch (\Exception $e) {
            Log::error('Get low stock medicines error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'module' => $module
            ]);

            return response()->json([
                'error' => 'An error occurred while fetching low stock medicines',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
