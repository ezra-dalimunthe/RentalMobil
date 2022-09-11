<?php

namespace App\Http\Controllers;

use App\Car;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->car = new Car();
        $this->customer = new Customer();
        $this->transaction = new Transaction();
    }

    public function username()
    {
        return 'username';
    }

    public function showLoginForm()
    {
        return view('backend.component.login');
    }

    public function dashboard()
    {
        $car = $this->car;
        $customer = $this->customer;
        $transaction = $this->transaction;
        $transaction_data = collect([null]);
        $year = date("Y");

        $rvalue = Transaction::selectRaw("EXTRACT( YEAR_MONTH FROM rent_date) ym ,"
            . " count(id) as total")
            ->where(\DB::raw("Year(rent_date)"), $year)
            ->groupBy("ym")
            ->get();

        $label = collect(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
            'Agustus', 'September', 'Oktober', 'November', 'Desember']);

        if ($rvalue->count() > 0) {

            $transaction_data = $label->map(function ($item, $key) use ($rvalue, $year) {
                $d = $rvalue->where("ym", $year .
                    str_pad($key + 1, 2, "0", STR_PAD_LEFT))->first();
                if ($d) {
                    return $d->total;
                }
                return 0;

            });

            //need to remove 0 value
            $transaction_data = array_reverse($transaction_data->toArray(), false);

            $pos = 0;
            foreach ($transaction_data as $key => $value) {
                if ($value > 0) {
                    $pos = $key;
                    break;}
            }

            $nullValues = array_fill(0, $pos, null);
            $transaction_data = array_replace($transaction_data, $nullValues);
            $transaction_data = array_reverse($transaction_data);
            //return response()->json(["va" => $transaction_data]);
        }
        $chartjs = app()->chartjs
            ->name('trans')
            ->type('line')
            ->size(['width' => 400, 'height' => 200])
            ->labels($label->toArray())
            ->datasets([
                [
                    "label" => "Transaksi " . $year,
                    'backgroundColor' => "rgba(78, 115, 223, 0.05)",
                    'borderColor' => "#e74a3b",
                    "pointHoverRadius" => "3",
                    "pointHitRadius" => "10",
                    "pointBorderWidth" => "2",
                    "pointBorderColor" => "#e74a3b",
                    "pointBackgroundColor" => "#e74a3b",
                    "pointHoverBackgroundColor" => "#e74a3b",
                    "pointHoverBorderColor" => "#e74a3b",
                    'data' => $transaction_data,

                ],
            ])
            ->optionsRaw("{
             'animation': {
                 'duration': 2000
             }
         }");

        return view('backend.dashboard.index', compact(['car', 'customer', 'transaction',
            'chartjs']));
    }

}
