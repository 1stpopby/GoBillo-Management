<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KBFeedback extends Model
{
    use HasFactory;

    protected $table = 'kb_feedback';

    protected $fillable = [
        'article_id',
        'user_id',
        'is_helpful',
        'comment',
        'session_id',
        'ip_address'
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function article()
    {
        return $this->belongsTo(KBArticle::class, 'article_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}