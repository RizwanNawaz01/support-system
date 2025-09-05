<?php
namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Ticket extends Model
{
    use HasFactory , HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'subject', 'body', 'status', 'category', 'note','ai_explanation',
        'ai_confidence', 'manually_overridden'
      ];

    protected $casts = [
    'id' => 'string',
    'status' => TicketStatus::class,
    'category' => TicketCategory::class,
    'ai_confidence' => 'float',
    'manually_overridden' => 'boolean',
    ];
}
