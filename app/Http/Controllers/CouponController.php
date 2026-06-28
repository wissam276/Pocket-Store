<?php

namespace App\Http\Controllers;

use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::live()
            ->latest()
            ->get();

        return CouponResource::collection($coupons);
    }

    public function adminIndex()
    {
        $coupons = Coupon::latest()->get();

        return CouponResource::collection($coupons);
    }

    public function store(Request $request)
    {
        $coupon = Coupon::create($this->validatedData($request));

        return new CouponResource($coupon);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validatedData($request, $coupon));

        return new CouponResource($coupon->refresh());
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully']);
    }

    private function validatedData(Request $request, ?Coupon $coupon = null): array
    {
        $payload = $this->normalizePayload($request->all());
        $required = $coupon ? 'sometimes' : 'required';

        $validator = Validator::make($payload, [
            'code' => [
                $required,
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($coupon?->id),
            ],
            'type' => [
                $required,
                Rule::in([
                    Coupon::TYPE_PERCENTAGE,
                    Coupon::TYPE_FIXED,
                    Coupon::TYPE_FREE_SHIPPING,
                ]),
            ],
            'value' => [$required, 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'enabled' => ['sometimes', 'boolean'],
        ]);

        $validator->after(function ($validator) use ($payload, $coupon): void {
            $type = $payload['type'] ?? $coupon?->type;
            $value = $payload['value'] ?? $coupon?->value;

            if ($type === Coupon::TYPE_PERCENTAGE && (float) $value > 100) {
                $validator->errors()->add('value', 'Percentage coupons cannot be greater than 100.');
            }
        });

        $data = $validator->validate();

        if (array_key_exists('code', $data)) {
            $data['code'] = strtoupper(trim($data['code']));
        }

        if (! $coupon) {
            $data['min_order'] = $data['min_order'] ?? 0;
            $data['usage_count'] = 0;
            $data['enabled'] = $data['enabled'] ?? true;
        }

        return $data;
    }

    private function normalizePayload(array $payload): array
    {
        $aliases = [
            'startDate' => 'start_date',
            'endDate' => 'end_date',
            'usageLimit' => 'usage_limit',
            'minOrder' => 'min_order',
        ];

        foreach ($aliases as $camel => $snake) {
            if (array_key_exists($camel, $payload) && ! array_key_exists($snake, $payload)) {
                $payload[$snake] = $payload[$camel];
            }
        }

        return $payload;
    }
}
