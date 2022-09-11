<?php

namespace App\Http\Controllers;

use App\Car;
use App\CarImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CarController extends Controller
{

    public function __construct()
    {
        $this->car = new Car();
        $this->image = new CarImage();
    }

    public function index()
    {

        return view('backend.car.index');
    }

    public function source()
    {

        $query = Car::query();
        $query->with(['manufacture', 'images']);
        return DataTables::eloquent($query)
            ->filter(function ($query) {
                if (request()->has('search')) {
                    $query->where(function ($q) {
                        $q->where('name', 'LIKE', '%' . request('search')['value'] . '%');
                    });
                }
            })
            ->addColumn('name', function ($data) {
                return str_limit($data->name, 50);
            })
            ->addColumn('manufacture', function ($data) {
                return title_case($data->manufacture->name);
            })
            ->addColumn('license_number', function ($data) {
                return $data->license_number;
            })
            ->addColumn('color', function ($data) {
                return $data->color;
            })
            ->addColumn('year', function ($data) {
                return $data->year;
            })
            ->addColumn('price', function ($data) {
                return number_format($data->price, 0, ',', '.');
            })
            ->addColumn('penalty', function ($data) {
                return number_format($data->penalty, 0, ',', '.');
            })
            ->addColumn('status', function ($data) {
                return $data->status == 'tersedia' ? '<span class="badge badge-success">' . $data->status . '</span>' : '<span class="badge badge-secondary">' . $data->status . '</span>';
            })
            ->addColumn('description', function ($data) {
                return str_limit(strip_tags($data->description, 50));
            })

            ->addIndexColumn()
            ->addColumn('action', 'backend.car.index-action')
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function create()
    {
        return view('backend.car.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request = $request->merge(['slug' => str_slug($request->name), 'status' => 'tersedia']);
            $car = $this->car->create($request->all());
            $no = 1;
            foreach ($request->image as $row) {
                $fileName = Str::uuid() . '.' . $row->extension();
                $path = public_path(\App\CarImage::IMAGE_PATH);

                $file = $row->move($path, $fileName);

                $this->image->create([
                    'car_id' => $car->id,
                    'image' => $fileName,
                ]);
            }
            DB::commit();
            return redirect()->route('car.index')->with('success-message', 'Data telah disimpan');
        } catch (\exception $e) {
            DB::rollback();
            return redirect()->route('car.index')->with('error-message', $e->getMessage());
        }

    }

    public function show($id)
    {
        $data = $this->car->find($id);
        return $data;

    }

    public function edit($id)
    {
        $data = $this->car->findOrFail($id);
        return view('backend.car.edit', compact('data'));

    }

    public function update(Request $request, $id)
    {
        $rootPublicPath = url(\App\CarImage::IMAGE_PATH);

        $request = $request->merge(['slug' => str_slug($request->name)]);
        // dd($request->image);
        if ($request->has('image')) {

            foreach ($request->image as $row) {
                $fileName = Str::uuid() . '.' . $row->extension();
                $path = public_path(\App\CarImage::IMAGE_PATH);

                $file = $row->move($path, $fileName);

                $this->image->create([
                    'car_id' => $id,
                    'image' => $fileName,
                ]);
            }
        }
        $this->car->find($id)->update($request->all());
        return redirect()->route('car.index')->with('success-message', 'Data telah dirubah');
    }

    public function destroy($id)
    {
        $car = Car::withCount("transactions")->find($id);

        if ($car == null) {
            //already deleted by someone else
            return response(null, 204);
        }

        if ($car->transactions_count > 0) {
            // do softdelete since this has many transaction.
            $car->delete();
        } else {
            $car->forceDelete();
        }

        return response(null, 204);

    }

    public function getImage($id)
    {
        $model = $this->image->where('car_id', $id)->get();
        return response()->json($model);
    }

    public function destroyImage($id)
    {
        $image = CarImage::find($id);
        if ($image == null) {
            //already deleted by someone else
            return response(null, 204);
        }

        $image->forceDelete();
        return response(null, 204);
    }

}
