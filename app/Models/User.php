<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->wallet()->create([
                'balance' => 0,
                'total_deposits' => 0,
                'total_withdrawals' => 0,
                'debt_ceiling' => 0,
                'debt_usage' => 0,
            ]);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country_code',
        'status',
        'country',
        'city',
        'address',
        'gender',
        'date_of_birth',
        'profile_photo',
        'password',
        'id_number',
        'kyc_level',
        'identity_verified_at',
        'iban',
        'bic_code',
        'beneficiary_name',
        'address_1',
        'address_2',
        'bank_city',
        'bank_country',
        'check_bank',
        'bank_name',
        'account_number',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function kycRequests()
    {
        return $this->hasMany(KycRequest::class);
    }

    public function latestKycRequest()
    {
        return $this->hasOne(KycRequest::class)->latestOfMany();
    }

    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $this->name ?: 'User';
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=7F9CF5&background=EBF4FF';
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
