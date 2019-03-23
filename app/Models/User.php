<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class User extends Model{
  public $timestamps = false;
  
  protected $table = 'users';

  protected $fillable = ['username', 'password', 'rank', 'gender', 'motto', 'credits', 'activity_points', 'last_offline', 'account_created', 'ip_last', 'ipcountry', 'jetons', 'mois_vip', 'mail', 'mail_valide', 'calendrier'];
}