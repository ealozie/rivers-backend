<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Filament\Models\Contracts\FilamentUser;
//use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Cog\Contracts\Ban\Bannable as BannableInterface;
use Cog\Laravel\Ban\Traits\Bannable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;


class User extends Authenticatable implements Auditable, BannableInterface
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use \OwenIt\Auditing\Auditable;
    use Bannable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'last_login_at',
        'status',
        'local_government_area_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function agent()
    {
        return $this->hasOne(TicketAgent::class);
    }

    // public function agent()
    // {
    //     return $this->hasOne(TicketAgent::class);
    // }

    // public function canAccessPanel(Panel $panel): bool
    // {
    //     return str_ends_with($this->email, '@gmail.com') && $this->hasVerifiedEmail();
    // }
    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
    }
}
