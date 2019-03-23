<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserStats extends Model{
  public $timestamps = false;
  
  protected $table = 'user_stats';
  protected $fillable = ['id'];
}