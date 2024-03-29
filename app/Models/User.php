<?php

namespace App\Models;

use App\Http\Controllers\ProjectManualReviewController;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'is_student',
        'is_staff',
        'is_admin',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function notes()
    {
        return $this->hasOne(UserNotes::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function project_manual_reviews()
    {
        return $this->hasMany(ProjectManualReviewController::class, 'user_id');
    }

    public function project_mark_reviews()
    {
        return $this->hasMany(ProjectMarkReview::class, 'user_id');
    }

    public function project_mark_allocations()
    {
        return $this->hasMany(ProjectMarkAllocation::class, 'user_id');
    }

    public function project_mark_review_allocations()
    {
        return $this->hasMany(ProjectMarkReviewAllocation::class, 'user_id');
    }

    public function getToDoCountAttribute()
    {
        return $this->project_mark_allocations->count() + $this->project_mark_review_allocations->count();
    }

    public function getToMarkAttribute()
    {
        return $this->project_mark_allocations->whereNull('marked');
    }

    public function getToMarkReviewAttribute()
    {
        return $this->project_mark_review_allocations->whereNull('marked');
    }

    public function getEscalationMarkerAttribute()
    {
        return $this->wherecan_review_escalation(true)->whereIsStudent(true);
    }

    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name).' '.ucfirst($this->last_name);
    }
}
