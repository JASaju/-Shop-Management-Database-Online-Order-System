<?php

namespace App\Support;

/**
 * One filter routine for every report query (works with DB::table() and Eloquent).
 *
 *   ReportFilter::apply($query, $filters, ['shop' => 'memos.shop_id', 'date' => 'memos.memo_date']);
 *
 * $filters keys : shop_id, company_id, customer_id, staff_id, from, to, due_only
 * $cols keys    : shop, company, customer, staff, date, due   (only the ones the table has)
 * A filter is applied only when it has a value AND the table has a matching column,
 * so the same call gives the same behaviour on any query - only the column names change.
 */
class ReportFilter
{
    public static function apply($query, array $f, array $cols)
    {
        $equals = [
            'shop_id'     => 'shop',
            'company_id'  => 'company',
            'customer_id' => 'customer',
            'staff_id'    => 'staff',
        ];
        foreach ($equals as $key => $col) {
            if (!empty($f[$key]) && isset($cols[$col])) {
                $query->where($cols[$col], $f[$key]);
            }
        }
        if (!empty($f['from']) && isset($cols['date'])) {
            $query->whereDate($cols['date'], '>=', $f['from']);
        }
        if (!empty($f['to']) && isset($cols['date'])) {
            $query->whereDate($cols['date'], '<=', $f['to']);
        }
        if (!empty($f['due_only']) && isset($cols['due'])) {
            $query->where($cols['due'], '>', 0);
        }
        return $query;
    }
}
