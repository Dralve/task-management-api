<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
{
    $user = Auth::user();
    $dueDate = $this->due_date instanceof Carbon? $this->due_date->format('Y-m-d H:i:s'): ($this->due_date ?: null);


    if ($user->role === 'admin') {
        return [
            'id' => $this->task_id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'dueDate' => $dueDate,
            'status' => $this->status,
            'assignedTo' => $this->assigned_to,
            'createdBy' => $this->created_by,
            'deletedAt' => $this->deleted_at,
        ];
    }

    return [
        'id' => $this->task_id,
        'title' => $this->title,
        'description' => $this->description,
        'priority' => $this->priority,
        'dueDate' => $dueDate,
        'status' => $this->status,
        'assignedTo' => $this->assigned_to,
        'createdBy' => $this->created_by,
    ];
}
}
