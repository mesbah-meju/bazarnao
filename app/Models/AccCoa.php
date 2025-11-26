<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccCoa extends Model
{
    use HasFactory;

    protected $fillable = [
        'head_code',
        'head_name',
        'pre_head_name',
        'head_level',
        'is_active',
        'is_transaction',
        'is_gl',
        'head_type',
        'is_budget',
        'is_depreciation',
        'depreciation_rate',
        'customer_id',
        'supplier_id',
        'warehouse_id',
        'created_by',
        'created_at'
    ];

    public function dfs($HeadName, $HeadCode, $oResult, &$visit, $d)
    {
        if ($d == 0) echo "<li class=\"jstree-open\" id='" . $HeadCode . "'>$HeadName";
        else if ($d == 1) echo "<li class=\"jstree-open\" id='" . $HeadCode . "'><a href='javascript:' onclick=\"loadData('" . $HeadCode . "')\">$HeadName</a>";
        else echo "<li id='" . $HeadCode . "'><a href='javascript:' onclick=\"loadData('" . $HeadCode . "')\">$HeadName</a>";

        $p = 0;
        for ($i = 0; $i < count($oResult); $i++) {
            if (!$visit[$i]) {
                if ($HeadCode == $oResult[$i]->pre_head_code) {
                    $visit[$i] = true;
                    if ($p == 0) echo "<ul>";
                    $p++;
                    $this->dfs($oResult[$i]->head_name, $oResult[$i]->head_code, $oResult, $visit, $d + 1);
                }
            }
        }

        if ($p == 0)
            echo "</li>";
        else
            echo "</ul>";
        }

    public static function getSubTypeData()
    {
        return self::distinct()
            ->where('IsActive', 1) // Only include active records
            // ->where('sub_type_id', 1) // Replace with your actual column name and value
            ->get();
    }

    public static function getHeadDetails($headCode)
    {
        return self::where('head_code', $headCode)->first();
    }

    public static function getSubHeadDetails($PHeadCode)
    {
        return self::where('pre_head_code', $PHeadCode)->first();
    }

    public static function get_coa_heads()
    {
        $result = self::where('is_active', 1)->get();
        $list = ['' => 'Select Account'];
        if ($result) {
            foreach ($result as $value) {
                $list[$value->head_code] = $value->head_name;
            }
        }
        return $list;
    }
}
