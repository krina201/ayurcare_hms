<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PatientSearchService
{
    /**
     * Search for patients with common functionality
     *
     * @param Request $request
     * @param string $module Module name for logging (e.g., 'appointment', 'treatment-plan', 'pharmacy')
     * @param array $additionalFields Additional fields to include in search
     * @param int $limit Maximum number of results to return
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPatients(Request $request, string $module = 'general', array $additionalFields = [], int $limit = 10)
    {
        try {
            // Log the incoming request
            Log::info("Patient search request received from {$module}", [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'query' => $request->all(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'module' => $module
            ]);

            $query = trim($request->get('query', ''));

            if (empty($query) || strlen($query) < 2) {
                Log::info("Patient search: Query too short or empty", [
                    'query' => $query,
                    'module' => $module
                ]);
                return response()->json([]);
            }

            // Sanitize the query to prevent SQL injection
            $query = strip_tags($query);

            // Log the search attempt for debugging
            Log::info("Patient search attempt", [
                'query' => $query,
                'user_id' => Auth::id(),
                'timestamp' => now(),
                'module' => $module
            ]);

            // Check if Patient model exists and is accessible
            if (!class_exists(Patient::class)) {
                Log::error("Patient model not found", ['module' => $module]);
                throw new \Exception("Patient model not accessible");
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
                'uhid',
                'full_name',
                'gender',
                'age',
                'mobile',
                'prakriti',
                'allergies',
                'photo_path'
            ];

            // Merge additional fields if provided
            $selectFields = array_unique(array_merge($defaultFields, $additionalFields));

            // Search for patients with better error handling
            $patients = Patient::where(function ($q) use ($query) {
                $q->where('uhid', 'LIKE', "%{$query}%")
                    ->orWhere('full_name', 'LIKE', "%{$query}%")
                    ->orWhere('mobile', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
                ->select($selectFields)
                ->limit($limit)
                ->get();

            // Log the search results for debugging
            Log::info("Patient search results", [
                'query' => $query,
                'count' => $patients->count(),
                'module' => $module,
                'results' => $patients->toArray()
            ]);

            // If no results found, return empty array
            if ($patients->isEmpty()) {
                Log::info("Patient search: No results found", [
                    'query' => $query,
                    'module' => $module
                ]);
                return response()->json([]);
            }

            // Format the results
            $formattedPatients = $patients->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'uhid' => $patient->uhid ?? 'N/A',
                    'full_name' => $patient->full_name ?? 'N/A',
                    'gender' => $patient->gender ?? 'N/A',
                    'age' => $patient->age ?? 'N/A',
                    'mobile' => $patient->mobile ?? 'N/A',
                    'email' => $patient->email ?? 'N/A',
                    'prakriti' => $patient->prakriti ?? 'N/A',
                    'allergies' => $patient->allergies ?? 'None',
                    'photo_path' => $patient->photo_path ?? null,
                    'registration_date' => $patient->registration_date ?? null,
                    'registration_type' => $patient->registration_type ?? 'N/A'
                ];
            });

            Log::info("Patient search: Returning formatted results", [
                'query' => $query,
                'formatted_count' => $formattedPatients->count(),
                'module' => $module
            ]);

            return response()->json($formattedPatients);
        } catch (\Exception $e) {
            Log::error('Patient search error: ' . $e->getMessage(), [
                'query' => $request->get('query'),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'module' => $module
            ]);

            return response()->json([
                'error' => 'An error occurred while searching patients',
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
     * Search patients with specific filters
     *
     * @param Request $request
     * @param string $module
     * @param array $filters Additional filters to apply
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPatientsWithFilters(Request $request, string $module = 'general', array $filters = [])
    {
        try {
            $query = trim($request->get('query', ''));

            if (empty($query) || strlen($query) < 2) {
                return response()->json([]);
            }

            $query = strip_tags($query);

            $patientsQuery = Patient::where(function ($q) use ($query) {
                $q->where('uhid', 'LIKE', "%{$query}%")
                    ->orWhere('full_name', 'LIKE', "%{$query}%")
                    ->orWhere('mobile', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            });

            // Apply additional filters
            foreach ($filters as $field => $value) {
                if (!empty($value)) {
                    $patientsQuery->where($field, $value);
                }
            }

            $patients = $patientsQuery
                ->select([
                    'id',
                    'uhid',
                    'full_name',
                    'gender',
                    'age',
                    'mobile',
                    'email',
                    'prakriti',
                    'allergies',
                    'photo_path',
                    'registration_date',
                    'registration_type'
                ])
                ->limit(10)
                ->get();

            $formattedPatients = $patients->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'uhid' => $patient->uhid ?? 'N/A',
                    'full_name' => $patient->full_name ?? 'N/A',
                    'gender' => $patient->gender ?? 'N/A',
                    'age' => $patient->age ?? 'N/A',
                    'mobile' => $patient->mobile ?? 'N/A',
                    'email' => $patient->email ?? 'N/A',
                    'prakriti' => $patient->prakriti ?? 'N/A',
                    'allergies' => $patient->allergies ?? 'None',
                    'photo_path' => $patient->photo_path ?? null,
                    'registration_date' => $patient->registration_date ?? null,
                    'registration_type' => $patient->registration_type ?? 'N/A'
                ];
            });

            return response()->json($formattedPatients);
        } catch (\Exception $e) {
            Log::error('Patient search with filters error: ' . $e->getMessage(), [
                'query' => $request->get('query'),
                'user_id' => Auth::id(),
                'module' => $module,
                'filters' => $filters
            ]);

            return response()->json([
                'error' => 'An error occurred while searching patients',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient by ID with full details
     *
     * @param int $patientId
     * @param string $module
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPatientById(int $patientId, string $module = 'general')
    {
        try {
            $patient = Patient::find($patientId);

            if (!$patient) {
                return response()->json([
                    'error' => 'Patient not found'
                ], 404);
            }

            $formattedPatient = [
                'id' => $patient->id,
                'uhid' => $patient->uhid ?? 'N/A',
                'full_name' => $patient->full_name ?? 'N/A',
                'gender' => $patient->gender ?? 'N/A',
                'age' => $patient->age ?? 'N/A',
                'mobile' => $patient->mobile ?? 'N/A',
                'email' => $patient->email ?? 'N/A',
                'prakriti' => $patient->prakriti ?? 'N/A',
                'allergies' => $patient->allergies ?? 'None',
                'photo_path' => $patient->photo_path ?? null,
                'registration_date' => $patient->registration_date ?? null,
                'registration_type' => $patient->registration_type ?? 'N/A',
                'address' => $patient->address ?? 'N/A',
                'emergency_contact' => $patient->emergency_contact ?? 'N/A',
                'medical_history' => $patient->medical_history ?? 'N/A'
            ];

            return response()->json($formattedPatient);
        } catch (\Exception $e) {
            Log::error('Get patient by ID error: ' . $e->getMessage(), [
                'patient_id' => $patientId,
                'user_id' => Auth::id(),
                'module' => $module
            ]);

            return response()->json([
                'error' => 'An error occurred while fetching patient details',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
