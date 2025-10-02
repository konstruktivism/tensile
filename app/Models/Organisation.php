<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
        'website',
    ];

    // create relationships so that multiple projects can be associated with an organisation

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // create relationships so that multiple users can be associated with an organisation

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
