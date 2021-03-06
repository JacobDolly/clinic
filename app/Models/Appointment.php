<?php

namespace App\Models;

use App\Models\User;
use App\Models\InvoiceMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'doctor_id', 'status', 'user_id', 'invoice_id'
    ];

    protected $dates = ['date'];

// RELACIONES
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

// ALMACENAMIENTO
    public function store($request, $invoice)
    {
        $date = Carbon::createFromFormat('Y-m-d H:i', $request->date_submit . ' ' . $request->time_submit)->toDateTimeString();

        InvoiceMeta::create(['key' => 'doctor', 'value' => $request->doctor, 'invoice_id' => $invoice->id]);

        InvoiceMeta::create(['key' => 'concept', 'value' => 'Cita médica', 'invoice_id' => $invoice->id]);

        return self::create([
            'date' => $date,
            'doctor_id' => $request->doctor,
            'status' => 'pending',
            'user_id' => $invoice->user->id,
            'invoice_id' => $invoice->id
        ]);
    }

    public function my_update($request)
    {
        $date = Carbon::createFromFormat('Y-m-d H:i', $request->date_submit . ' ' . $request->time_submit)->toDateTimeString();

        $invoice_status = ($request->status == 'done') ? 'approved' : $request->status;

        self::update([
            'date' => $date,
            'status' => $request->status
        ]);

        $this->invoice->update([
            'status' => $invoice_status
        ]);
    }

// RECUPERAR INFORMACIÓN
    public function doctor()
    {
        $doctor = User::find($this->doctor_id);
        return $doctor;
    }
}
