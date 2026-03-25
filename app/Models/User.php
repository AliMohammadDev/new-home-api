<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable, HasApiTokens, HasRoles;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */

  protected $fillable = [
    'name',
    'email',
    'is_active',
    'password',
    'google_id',
    'google_token',
    'fcm_token',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
    'google_token',
    'google_id',
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
      'is_active' => 'boolean',
    ];
  }

  protected static function booted()
  {
    static::updated(function ($user) {
      if ($user->wasChanged('is_active') && !$user->is_active) {
        $user->tokens()->delete();
      }
    });
  }


  public function checkout()
  {
    return $this->hasOne(Checkout::class);
  }
  public function wishlist()
  {
    return $this->hasMany(WishList::class);
  }
  public function reviews()
  {
    return $this->hasMany(Reviews::class);
  }

  public function activeCart()
  {
    return $this->hasOne(Cart::class)
      ->where('status', 'active');
  }

  public function carts()
  {
    return $this->hasMany(Cart::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }

  public function checkouts()
  {
    return $this->hasMany(Checkout::class);
  }

  public function warehouses()
  {
    return $this->hasMany(Warehouse::class);
  }

  public function salesPointManagers()
  {
    return $this->hasMany(SalesPointManager::class);
  }

  public function canAccessPanel(\Filament\Panel $panel): bool
  {
    if (!$this->is_active) {
      return false;
    }
    return $this->roles()->where('name', '!=', 'customer')->exists();
  }

  public function salesPoints()
  {
    return $this->belongsToMany(SalesPoint::class, 'sales_point_managers');
  }

}
