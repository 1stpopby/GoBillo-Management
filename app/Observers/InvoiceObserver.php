<?php

namespace App\Observers;

use App\Models\Invoice;

class InvoiceObserver
{
    public function updated(Invoice $invoice): void
    {
        // If invoice was just marked as paid, no-op here but kept for extension
        // Reports read directly from invoices table filtered by status/paid_at,
        // so no denormalization is required. This hook exists for cache busting if needed.
    }
}






