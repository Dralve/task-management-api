<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_tasks';
    public $incrementing = true;

    protected $primaryKey = 'task_id';

    protected $fillable = [
        'title',
        'description',
        'priority',
        'due_date',
        'status',
        'assigned_to',
        'created_by',
    ];
    
    protected $casts = [
        'due_date' => 'datetime',
    ];

    protected $dates = ['due_date', 'deleted_at'];

    public function assigenUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get the due date formatted
     *
     * @return string
     */
    public function getDueDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }

    /**
     * Set the due date attribute.
     *
     * @param  string $value
     * @return void
     */
    public function setDueDateAttribute($value)
    {
        if (is_string($value)) {
            try {
                $date = Carbon::createFromFormat('Y-m-d H:i', $value);
                $this->attributes['due_date'] = $date->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $this->attributes['due_date'] = null;
            }
        } else {
            $this->attributes['due_date'] = null;
        }
    }
}
