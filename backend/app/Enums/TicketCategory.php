<?php
namespace App\Enums;

enum TicketCategory: string
{
    case Billing = 'billing';
    case Technical = 'technical';
    case Account = 'account';
    case Shipping = 'shipping';
    case Sales = 'sales';
    case Other = 'other';
}
