<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Student extends Model {
    protected $table = 'students';

    protected $guarded = ['id'];

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
