<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Product;
use App\Rules\Uppercase;
use App\Http\Requests\CreateValidationRequest;

class CarsController extends Controller
{
    public function __construct() 
    {
        // Don't show any page to non-logged in users except the 
        // "index" and "show" pages
        $this->middleware('auth', ['except'=> ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cars = Car::all();

        return view('cars.index', [
            'cars' => $cars
        ]);
    }
    // This one is using the serialization 
    // public function index()
    // {
    //     $cars = Car::all()->toJson();
    //     $cars = json_decode($cars);
    //     var_dump($cars);

    //     return view('cars.index', [
    //         'cars' => $cars
    //     ]);
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cars.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // This is the first way of inserting to database using Eloquent
        // by creating a new instance then save it. 
        // $car = new Car();
        // $car->name = $request->input('name');
        // $car->founded = $request->input('founded');
        // $car->description = $request->input('description');
        // $car->save();

        // This is the second way of inserting to database using Eloquent
        // which is somehow prefered by passing an array to a model. 
        // And when ever you pass an array every property needs to be 
        // approved for mass assignment in our model. 
        // by creating the propery "protected $fillable = [];" in our model. 

        // Since the $request is returning an instance of Symfony's upload file
        // Methods we can use on $request
        // guessExtension(), getMimeType(), store(), asStore(), storePublicly() 
        // move(), getClientOriginalName(), getClientMimeType(), 
        // guessClientExtension(), getSize(), getError(), isValid() 

        // $test = $request->file('image')->guessClientExtension();
        // dd($test);
        $request->validate([
            // 'name' => 'required|unique:cars', 
            // 'name' => new Uppercase, 
            'name' => 'required',
            'founded' => 'required|integer|min:0|max:2021', 
            'description' => 'required', 
            'image'=>'required|mimes:jpg,png,jpeg|max:5048'
        ]);

        $newImageName = time() . '-' . $request->name . '.' . 
            $request->image->extension();

        $request->image->move(public_path('images'), $newImageName);


        // If it's valid, it will proceed. 
        // If it's not valid, throw a ValidationException. 

        $car = Car::create([
            'name' => $request->input('name'),
            'founded' => $request->input('founded'),
            'description' => $request->input('description'),
            'image_path' => $newImageName,
            'user_id' => auth()->user()->id
        ]);

        // Note: we can use make method, Car::make
        // but then we need to save, thats the difference.

        return redirect('/cars');
    }
    // public function store(CreateValidationRequest $request)
    // {
    //     // This is the first way of inserting to database using Eloquent
    //     // by creating a new instance then save it. 
    //     // $car = new Car();
    //     // $car->name = $request->input('name');
    //     // $car->founded = $request->input('founded');
    //     // $car->description = $request->input('description');
    //     // $car->save();

    //     // This is the second way of inserting to database using Eloquent
    //     // which is somehow prefered by passing an array to a model. 
    //     // And when ever you pass an array every property needs to be 
    //     // approved for mass assignment in our model. 
    //     // by creating the propery "protected $fillable = [];" in our model. 

    //     $request->validated();

    //     // If it's valid, it will proceed. 
    //     // If it's not valid, throw a ValidationException. 

    //     $car = Car::create([
    //         'name' => $request->input('name'),
    //         'founded' => $request->input('founded'),
    //         'description' => $request->input('description'),
    //     ]);

    //     // Note: we can use make method, Car::make
    //     // but then we need to save, thats the difference.

    //     return redirect('/cars');
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $car = Car::find($id);

        // dd($car->engines);
        // var_dump($car->productionDate);
        // echo '<pre>', var_dump($car->productionDate), '</pre>';

        // var_dump($car->products);

        $products = Product::find($id);
        return view('cars.show')->with('car', $car);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $car = Car::find($id)->first();

        return view('cars.edit')->with('car', $car);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         // 'name' => 'required|unique:cars', 
    //         'name' => new Uppercase, 
    //         'founded' => 'required|integer|min:0|max:2021', 
    //         'description' => 'required'
    //     ]);

    //     $car = Car::where('id', $id)
    //     ->update([
    //         'name' => $request->input('name'),
    //         'founded' => $request->input('founded'),
    //         'description' => $request->input('description'),
    //     ]);

    //     return redirect('/cars');
    // }
    public function update(CreateValidationRequest $request, $id)
    {
        $request->validated();
        
        $car = Car::where('id', $id)
        ->update([
            'name' => $request->input('name'),
            'founded' => $request->input('founded'),
            'description' => $request->input('description'),
        ]);

        return redirect('/cars');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // This is the first approach of deleting a record using Eloquent
    // public function destroy($id)
    // {
    //     $car = Car::find($id)->first();

    //     $car->delete();

    //     return redirect('/cars');
    // }
    // This is the second approach of deleting a record using Eloquent
    public function destroy(Car $car)
    {
        $car->delete();

        return redirect('/cars');
    }
}
