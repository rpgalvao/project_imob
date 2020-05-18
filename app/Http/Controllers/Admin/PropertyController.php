<?php

namespace LaraDev\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use LaraDev\Http\Controllers\Controller;
use LaraDev\Property;
use LaraDev\Http\Requests\Admin\Property as PropertyRequest;
use LaraDev\PropertyImage;
use LaraDev\Support\Cropper;
use LaraDev\User;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $properties = Property::orderBy('id', 'DESC')->get();

        return view('admin.properties.index', [
            'properties' => $properties
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('admin.properties.create', [
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyRequest $request)
    {
        $createProperty = Property::create($request->all());
        $createProperty->setSlug();

        $validator = Validator::make($request->only('files'), ['files.*' => 'image']);

        if ($validator->fails() === true) {
            return redirect()->back()->withInput()->with(['color' => 'orange', 'message' => 'Por favor selecione imagens com formatos e extensões válidos (jpg, jpeg, png)']);
        }

        if ($request->allFiles()) {
            foreach ($request->allFiles()['files'] as $image) {
                $propImage = new PropertyImage();
                $propImage->property = $createProperty->id;
                $propImage->path = $image->store('properties/' . $createProperty->id);
                $propImage->save();
                unset($propImage);
            }
        }

        return redirect()->route('admin.properties.edit', [
            'property' => $createProperty->id
        ])->with(['color' => 'green', 'message' => 'Imóvel cadastrado com sucesso!']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $property = Property::where('id', $id)->first();
        $users = User::orderBy('name')->get();

        return view('admin.properties.edit', [
            'property' => $property,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyRequest $request, $id)
    {
        $property = Property::where('id', $id)->first();

        $property->setSaleAttribute($request->sale);
        $property->setRentAttribute($request->rent);
        $property->setAirConditioningAttribute($request->air_conditioning);
        $property->setBarAttribute($request->bar);
        $property->setLibraryAttribute($request->library);
        $property->setBarbecueGrillAttribute($request->barbecue_grill);
        $property->setAmericanKitchenAttribute($request->american_kitchen);
        $property->setFittedKitchenAttribute($request->fitted_kitchen);
        $property->setPantryAttribute($request->pantry);
        $property->setEdiculeAttribute($request->edicule);
        $property->setOfficeAttribute($request->office);
        $property->setBathtubAttribute($request->bathtub);
        $property->setFirePlaceAttribute($request->fireplace);
        $property->setLavatoryAttribute($request->lavatory);
        $property->setFurnishedAttribute($request->furnished);
        $property->setPoolAttribute($request->pool);
        $property->setSteamRoomAttribute($request->steam_room);
        $property->setViewOfTheSeaAttribute($request->view_of_the_sea);

        $property->fill($request->all());

        $property->save();
        $property->setSlug();

        $validator = Validator::make($request->only('files'), ['files.*' => 'image']);

        if ($validator->fails() === true) {
            return redirect()->back()->withInput()->with(['color' => 'orange', 'message' => 'Por favor selecione imagens com formatos e extensões válidos (jpg, jpeg, png)']);
        }

        if ($request->allFiles()) {
            foreach ($request->allFiles()['files'] as $image) {
                $propImage = new PropertyImage();
                $propImage->property = $property->id;
                $propImage->path = $image->store('properties/' . $property->id);
                $propImage->save();
                unset($propImage);
            }
        }

        return redirect()->route('admin.properties.edit', [
            'property' => $property->id
        ])->with(['color' => 'green', 'message' => 'Imóvel atualizado com sucesso!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function setCoverImage(Request $request)
    {
        $imageSetCover = PropertyImage::where('id', $request->image)->first();
        $allImages = PropertyImage::where('property', $imageSetCover->property)->get();
        $imageSetCover->cover = true;
        $imageSetCover->save();

        foreach ($allImages as $image) {
            $image->cover = null;
            $image->save();
        }

        $json = [
            'success' => true
        ];

        return response()->json($json);
    }

    public function removeImage(Request $request)
    {
        $imageDelete = PropertyImage::where('id', $request->image)->first();

        Storage::delete($imageDelete->path);
        Cropper::flush($imageDelete->path);
        $imageDelete->delete();

        $json = [
            'success' => true
        ];

        return response()->json($json);
    }
}
