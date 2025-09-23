<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentStatement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'client_id',
        'statement_number',
        'date_from',
        'date_to',
        'total_budget',
        'total_invoiced',
        'total_paid',
        'outstanding_balance',
        'remaining_budget',
        'include_projects',
        'include_invoices',
        'include_payments',
        'statement_data',
        'statement_date',
        'generated_by',
        'sent_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'statement_date' => 'date',
        'sent_at' => 'datetime',
        'statement_data' => 'array',
        'include_projects' => 'boolean',
        'include_invoices' => 'boolean',
        'include_payments' => 'boolean',
    ];

    /**
     * Get the company that owns the payment statement.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the client that the payment statement belongs to.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Generate a unique statement number.
     */
    public static function generateStatementNumber($companyId)
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last statement number for this company
        $lastStatement = self::where('company_id', $companyId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStatement && preg_match('/STMT-' . $year . $month . '-(\d+)/', $lastStatement->statement_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('STMT-%s%s-%04d', $year, $month, $nextNumber);
    }
}