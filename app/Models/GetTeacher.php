<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GetTeacher extends Model
{
    // 指定資料表名稱
    protected $table = 'be_teachers';

    // 指定主鍵
    protected $primaryKey = 'id';

    // 避免 Laravel 嘗試自動增加 `created_at` 和 `updated_at` 欄位
    public $timestamps = true;

    // 指定可填充的欄位
    protected $fillable = [
        'user_id', 'title', 'subject_id', 'available_time', 'hourly_rate',
        'city_id', 'district_ids', 'details', 'resume_id'
    ];

    // 指定不可填充的欄位
    protected $guarded = [];

    /**
     * 取得教師所屬的科目
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
}