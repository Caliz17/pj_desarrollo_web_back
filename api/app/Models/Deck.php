<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    use HasFactory;

    protected $table = 'tb_decks';
    public function card1()
    {
        return $this->belongsTo(Card::class, 'id_card_1');
    }
    public function card2()
    {
        return $this->belongsTo(Card::class, 'id_card_2');
    }
    public function card3()
    {
        return $this->belongsTo(Card::class, 'id_card_3');
    }
    public function card4()
    {
        return $this->belongsTo(Card::class, 'id_card_4');
    }
    public function card5()
    {
        return $this->belongsTo(Card::class, 'id_card_5');
    }
    public function card6()
    {
        return $this->belongsTo(Card::class, 'id_card_6');
    }
    public function card7()
    {
        return $this->belongsTo(Card::class, 'id_card_7');
    }
    public function card8()
    {
        return $this->belongsTo(Card::class, 'id_card_8');
    }
}
