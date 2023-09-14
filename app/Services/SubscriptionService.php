<?php

namespace App\Services;

use App\Models\GatewayCurrency;
use App\Models\OwnerPackage;
use App\Models\Package;
use App\Models\User;
use App\Traits\ResponseTrait;

class SubscriptionService
{
    use ResponseTrait;

    public function getCurrentPlan($userId = null)
    {
        $userId = $userId == null ? auth()->id() : $userId;
        return OwnerPackage::query()
            ->where('user_id', $userId)
            ->whereIn('owner_packages.status', [ACTIVE])
            ->whereDate('end_date', '>=', now())
            ->select('owner_packages.*')
            ->first();
    }

    public function getAllPackages()
    {
        return Package::where('status', ACTIVE)->where('is_trail', '!=', ACTIVE)->get();
    }

    public function getById($id)
    {
        return Package::query()->findOrFail($id);
    }

    public function getCurrencyByGatewayId($id)
    {
        $userId = User::where('role', USER_ROLE_ADMIN)->first()->id;
        $currencies = GatewayCurrency::where(['owner_user_id' => $userId, 'gateway_id' => $id])->get();
        foreach ($currencies as $currency) {
            $currency->symbol =  $currency->symbol;
        }
        return $currencies;
    }

    public function cancel()
    {
        return OwnerPackage::query()
            ->where(['user_id' => auth()->id(), 'status' => ACTIVE])
            ->whereDate('end_date', '>=', now()->toDateTimeString())
            ->update(['status' => DEACTIVATE]);
    }
}
