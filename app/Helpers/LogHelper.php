<?php

namespace App\Helpers;

use App\Models\UserLog;
use App\Models\PatientLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


class LogHelper
{
    public static function logActivity(string $type, int $recordId, string $modelType, array $extraData = []): void
    {
        $ip = Request::ip();
        $activityType = ucfirst(strtolower($type));
        $actionTime = now();

        switch (strtolower($modelType)) {
            case 'user':
                UserLog::create([
                    'user_id'     => $recordId,
                    'action'      => $activityType,
                    'ip_address'  => $ip,
                    'action_time' => $actionTime,
                    'data'        => json_encode($extraData ?? []),
                ]);
                break;

            case 'patient':
                PatientLog::create([
                    'patient_id'  => $recordId,
                    'user_id'     => Auth::id(),
                    'action'      => $activityType,
                    'ip_address'  => $ip,
                    'action_time' => $actionTime,
                    'data'        => json_encode($extraData ?? []),
                ]);
                break;
        }
    }
}
