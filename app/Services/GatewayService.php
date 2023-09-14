<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Gateway;
use App\Models\GatewayCurrency;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GatewayService
{
    use ResponseTrait;

    public function getAll()
    {
        return Gateway::where('owner_user_id', auth()->id())->get();
    }

    public function getActiveAll($ownerUserId)
    {
        return Gateway::where('owner_user_id', $ownerUserId)->where('status', ACTIVE)->get();
    }

    public function getActiveBanks()
    {
        return Bank::where('owner_user_id', auth()->user()->owner_user_id)->where('status', ACTIVE)->get();
    }

    public function getInfo($id)
    {
        return Gateway::findOrFail($id);
    }

    public function getCurrenciesByGatewayId($id)
    {
        $data['gateway'] = $this->getInfo($id);
        if ($data['gateway']->slug == 'bank') {
            $data['banks'] = $this->banks();
        }
        $data['image'] = $data['gateway']->icon;
        $currencies = GatewayCurrency::where('owner_user_id', auth()->id())->where('gateway_id', $id)->get();
        foreach ($currencies as $currency) {
            $currency->symbol;
        }
        $data['currencies'] = $currencies;
        return $this->success($data);
    }

    public function banks()
    {
        return Bank::where('owner_user_id', auth()->id())->get();
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $ownerUserId = auth()->id();
            $id = $request->get('id', '');
            if ($id != '') {
                $gateway = Gateway::where('owner_user_id', $ownerUserId)->findOrFail($request->id);
            } else {
                $gateway = new Gateway();
            }
            if ($gateway->slug == 'bank') {
                if ($request->status == ACTIVE) {
                    $bankIds = [];
                    for ($i = 0; $i < count($request->bank['name']); $i++) {
                        $bank = Bank::updateOrCreate([
                            'id' => $request->bank['id'][$i],
                            'owner_user_id' => $ownerUserId
                        ], [
                            'gateway_id' => $gateway->id,
                            'owner_user_id' => $ownerUserId,
                            'gateway_id' => $gateway->id,
                            'name' => $request->bank['name'][$i],
                            'details' => $request->bank['details'][$i],
                            'status' => $request->bank['status'][$i],

                        ]);
                        array_push($bankIds, $bank->id);
                    }
                    Bank::where('owner_user_id', $ownerUserId)->whereNotIn('id', $bankIds)->delete();
                }
            } else {
                $gateway->mode = $request->mode;
                $gateway->url = $request->url;
                $gateway->key = $request->key;
                $gateway->secret = $request->secret;
            }
            $gateway->status = $request->status;
            $gateway->owner_user_id = $ownerUserId;
            $gateway->save();

            if (is_null($request->currency)) {
                throw new Exception('Please Add one currency at least');
            }
            $gatewayCurrencyIds = [];
            foreach ($request->currency as $key => $currency) {
                $gatewayCurrency =   GatewayCurrency::updateOrCreate([
                    'id' => $request->currency_id[$key],
                    'owner_user_id' => $ownerUserId
                ], [
                    'gateway_id' => $gateway->id,
                    'owner_user_id' => $ownerUserId,
                    'currency' => $currency,
                    'conversion_rate' => $request->conversion_rate[$key],
                ]);
                array_push($gatewayCurrencyIds, $gatewayCurrency->id);
            }
            GatewayCurrency::where('owner_user_id', $ownerUserId)->whereNotIn('id', $gatewayCurrencyIds)->where('gateway_id', $gateway->id)->delete();

            DB::commit();
            $message = $request->id ? UPDATED_SUCCESSFULLY : CREATED_SUCCESSFULLY;
            return $this->success([], $message);
        } catch (Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }

    public function getCurrencyByGatewayId($id)
    {
        $currencies = GatewayCurrency::where('gateway_id', $id)->get();
        foreach ($currencies as $currency) {
            $currency->symbol =  $currency->symbol;
        }
        return $currencies;
    }
}
