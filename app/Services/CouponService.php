<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for coupon maths.
 *
 * Nothing here is cached in the session. Every call re-reads the current
 * order lines, so adding, removing or cancelling an item automatically
 * produces the correct discount and totals.
 */
class CouponService
{
    /**
     * Sum of the live order lines, including add-ons, skipping cancelled lines.
     *
     * @param array $lines rows from orders.data / order_print.item_data
     */
    public function subtotal(array $lines): float
    {
        $total = 0.0;

        foreach ($lines as $line) {
            $line = (array) $line;

            if ($this->isCancelled($line)) {
                continue;
            }

            $qty    = (int) ($line['quantity'] ?? 0);
            $total += ((float) ($line['price'] ?? 0)) * $qty;

            foreach ($this->addons($line) as $addon) {
                $total += ((float) ($addon['price'] ?? 0)) * $qty;
            }
        }

        return round($total, 2);
    }

    /**
     * Re-check a coupon against the CURRENT order.
     *
     * @return array{valid:bool, discount:float, coupon:?Coupon, message:?string}
     */
    public function evaluate(?string $code, float $subtotal, array $lines): array
    {
        if ($code === null || $code === '' || $code === '0') {
            return $this->none();
        }

        $coupon = Coupon::whereRaw('BINARY `code` = ?', [$code])->first();

        if (! $coupon) {
            return $this->fail('Invalid coupon code!');
        }

        if (! $coupon->isValid()) {
            return $this->fail('Coupon has expired!');
        }

        // Re-checked on every call, so it also fires when items are removed.
        if (! empty($coupon->min_order) && $subtotal < (float) $coupon->min_order) {
            return $this->fail("Minimum order required ₹{$coupon->min_order}");
        }

        if ($coupon->type === 'Time-Based Discount') {
            $now   = now()->format('H:i:s');
            $start = Carbon::parse($coupon->start_time)->format('H:i:s');
            $end   = Carbon::parse($coupon->end_time)->format('H:i:s');

            if ($now < $start || $now > $end) {
                return $this->fail(sprintf(
                    'Coupon valid only between %s - %s',
                    Carbon::parse($coupon->start_time)->format('h:i A'),
                    Carbon::parse($coupon->end_time)->format('h:i A')
                ));
            }
        }

        if (in_array($coupon->type, ['Weekend Discount', 'Weekday Discount'], true)) {
            $days = json_decode($coupon->applicable_days, true) ?: [];

            if (! in_array(strtolower(now()->format('l')), $days, true)) {
                return $this->fail('Coupon not valid today!');
            }
        }

        if ($coupon->type === 'First Order Discount') {
            $phone = session('phone');

            if (! $phone) {
                return $this->fail('Phone number required before applying first order discount!');
            }

            if (Customer::where('phone', $phone)->count() > 1) {
                return $this->fail('This discount is only for first order!');
            }
        }

        // Which money the percentage/flat amount applies to.
        $base = $subtotal;

        if (! empty($coupon->categoryId) && $this->isCategoryScoped($coupon)) {
            $base = $this->categoryAmount($lines, (int) $coupon->categoryId);

            if ($base <= 0) {
                $name = DB::table('categories')
                    ->where('id', $coupon->categoryId)
                    ->value('category_name');

                return $this->fail("This coupon applies only to {$name} items in your order!");
            }
        }

        $discount = $coupon->discount_mode === 'fixed'
            ? (float) $coupon->value
            : round(($base * (float) $coupon->value) / 100, 2);

        // A flat coupon must never exceed the bill or produce a negative total.
        $discount = min($discount, $subtotal);

        return [
            'valid'    => true,
            'discount' => round($discount, 2),
            'coupon'   => $coupon,
            'message'  => null,
        ];
    }

    /**
     * Full money breakdown for the current order. This is what the
     * controllers return to the client and what the invoice is built from.
     *
     * @return array{sub_total:float, coupon:?string, discount:float,
     *               coupon_error:?string, grand_total:float, cgst:float,
     *               sgst:float, total_with_tax:float}
     */
    public function quote(?string $code, array $lines, float $cgstRate = 0.0, float $sgstRate = 0.0): array
    {
        $subtotal = $this->subtotal($lines);
        $result   = $this->evaluate($code, $subtotal, $lines);

        $discount = $result['valid'] ? $result['discount'] : 0.0;
        $grand    = max(0.0, round($subtotal - $discount, 2));

        $cgst = round(($grand * $cgstRate) / 100, 2);
        $sgst = round(($grand * $sgstRate) / 100, 2);

        return [
            'sub_total'      => $subtotal,
            'coupon'         => $result['valid'] ? $code : null,
            'discount'       => $discount,
            'coupon_error'   => $result['message'],
            'grand_total'    => $grand,
            'cgst'           => $cgst,
            'sgst'           => $sgst,
            'total_with_tax' => round($grand + $cgst + $sgst, 2),
        ];
    }

    /**
     * Value of the lines belonging to one category, add-ons included.
     */
    private function categoryAmount(array $lines, int $categoryId): float
    {
        $ids = [];

        foreach ($lines as $line) {
            $line = (array) $line;
            $id   = $line['item_id'] ?? null;

            // Custom items (CUSTOM-XXXX) have no category, skip them.
            if ($id !== null && is_numeric($id)) {
                $ids[] = (int) $id;
            }
        }

        if (empty($ids)) {
            return 0.0;
        }

        $matching = DB::table('items')
            ->whereIn('id', array_unique($ids))
            ->where('category_id', $categoryId)
            ->pluck('id')
            ->all();

        if (empty($matching)) {
            return 0.0;
        }

        $matching = array_flip($matching);
        $amount   = 0.0;

        foreach ($lines as $line) {
            $line = (array) $line;

            if ($this->isCancelled($line)) {
                continue;
            }

            $id = $line['item_id'] ?? null;

            if ($id === null || ! is_numeric($id) || ! isset($matching[(int) $id])) {
                continue;
            }

            $qty     = (int) ($line['quantity'] ?? 0);
            $amount += ((float) ($line['price'] ?? 0)) * $qty;

            foreach ($this->addons($line) as $addon) {
                $amount += ((float) ($addon['price'] ?? 0)) * $qty;
            }
        }

        return round($amount, 2);
    }

    private function isCategoryScoped(Coupon $coupon): bool
    {
        return in_array(
            $coupon->type,
            ['Category-Specific Discount', 'Weekend Discount', 'Weekday Discount'],
            true
        );
    }

    /**
     * addons is stored as a JSON string on some rows and an array on others.
     */
    private function addons(array $line): array
    {
        $addons = $line['addons'] ?? null;

        if (is_string($addons)) {
            $addons = json_decode($addons, true);
        }

        if (! is_array($addons)) {
            return [];
        }

        return array_map(fn ($a) => (array) $a, $addons);
    }

    private function isCancelled(array $line): bool
    {
        if (($line['kitchen_status'] ?? null) === 'cancelled') {
            return true;
        }

        $cancelled = $line['cancelled'] ?? false;

        return filter_var($cancelled, FILTER_VALIDATE_BOOLEAN);
    }

    private function none(): array
    {
        return ['valid' => false, 'discount' => 0.0, 'coupon' => null, 'message' => null];
    }

    private function fail(string $message): array
    {
        return ['valid' => false, 'discount' => 0.0, 'coupon' => null, 'message' => $message];
    }
}