<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';
    protected $primaryKey = 'id';
}