<?php

namespace LaraDev;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use LaraDev\Support\Cropper;

class Property extends Model
{
    protected $fillable = [
        'sale',
        'rent',
        'category',
        'type',
        'user',
        'sale_price',
        'rent_price',
        'tribute',
        'condominium',
        'description',
        'bedrooms',
        'suites',
        'bathrooms',
        'rooms',
        'garage',
        'garage_covered',
        'area_total',
        'area_util',
        'zipcode',
        'street',
        'number',
        'complement',
        'neighborhood',
        'state',
        'city',
        'air_conditioning',
        'bar',
        'library',
        'barbecue_grill',
        'american_kitchen',
        'fitted_kitchen',
        'pantry',
        'edicule',
        'office',
        'bathtub',
        'fireplace',
        'lavatory',
        'furnished',
        'pool',
        'steam_room',
        'view_of_the_sea',
        'status',
        'title',
        'slug',
        'headline',
        'experience'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'property', 'id')
            ->orderBy('cover', 'DESC');
    }

    public function cover()
    {
        $images = $this->images();
        $cover = $images->where('cover', 1)->first(['path']);

        if(!$cover){
            $images = $this->images();
            $cover = $images->first(['path']);
        }

        if(empty($cover['path']) || !File::exists('../public/storage/' . $cover['path'])) {
            return url(asset('backend/assets/images/realty.jpeg'));
        }

        return Storage::url(Cropper::thumb($cover['path'], '1366', '768'));
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 1);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('status', 0);
    }

    public function scopeSale($query)
    {
        return $query->where('sale', 1);
    }

    public function scopeRent($query)
    {
        return $query->where('rent', 1);
    }

    public function setSaleAttribute($value)
    {
        $this->attributes['sale'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setRentAttribute($value)
    {
        $this->attributes['rent'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = ($value == '1' ? 1 : 0);
    }

    public function setSalePriceAttribute($value)
    {
        if(empty($value)){
            $this->attributes['sale_price'] = null;
        }else{
            $this->attributes['sale_price'] = floatval($this->convertStringToDouble($value));
        }
    }

    public function getSalePriceAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setRentPriceAttribute($value)
    {
        if(empty($value)){
            $this->attributes['rent_price'] = null;
        }else{
            $this->attributes['rent_price'] = floatval($this->convertStringToDouble($value));
        }
    }

    public function getRentPriceAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setTributeAttribute($value)
    {
        if(empty($value)){
            $this->attributes['tribute'] = null;
        }else{
            $this->attributes['tribute'] = floatval($this->convertStringToDouble($value));
        }
    }

    public function getTributeAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setCondominiumAttribute($value)
    {
        if(empty($value)){
            $this->attributes['condominium'] = null;
        }else{
            $this->attributes['condominium'] = floatval($this->convertStringToDouble($value));
        }
    }

    public function getCondominiumAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setZipcodeAttribute($value)
    {
        return $this->attributes['zipcode'] = $this->clearField($value);
    }

    public function getZipcodeAttribute($value)
    {
        return substr($value, '0', '5') . '-' .substr($value, '5', '8');
    }

    public function setAirConditioningAttribute($value)
    {
        $this->attributes['air_conditioning'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setBarAttribute($value)
    {
        $this->attributes['bar'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setLibraryAttribute($value)
    {
        $this->attributes['library'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setBarbecueGrillAttribute($value)
    {
        $this->attributes['barbecue_grill'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setAmericanKitchenAttribute($value)
    {
        $this->attributes['american_kitchen'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setFittedKitchenAttribute($value)
    {
        $this->attributes['fitted_kitchen'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setPantryAttribute($value)
    {
        $this->attributes['pantry'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setEdiculeAttribute($value)
    {
        $this->attributes['edicule'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setOfficeAttribute($value)
    {
        $this->attributes['office'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setBathtubAttribute($value)
    {
        $this->attributes['bathtub'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setFireplaceAttribute($value)
    {
        $this->attributes['fireplace'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setLavatoryAttribute($value)
    {
        $this->attributes['lavatory'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setFurnishedAttribute($value)
    {
        $this->attributes['furnished'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setPoolAttribute($value)
    {
        $this->attributes['pool'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setSteamRoomAttribute($value)
    {
        $this->attributes['steam_room'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setViewOfTheSeaAttribute($value)
    {
        $this->attributes['view_of_the_sea'] = ($value == 'on' || $value == true ? 1 : 0);
    }

    public function setSlug()
    {
        if(!empty($this->title)){
            $this->attributes['slug'] = str_slug($this->title) . '-' . $this->id;
            $this->save();
        }
    }

    private function clearField(?string $param)
    {
        if (empty($param)) {
            return '';
        }

        return str_replace(['.', '-', '_', '(', ')', ' ', '/'], '', $param);
    }

    private function convertStringToDouble($param)
    {
        if (empty($param)) {
            return null;
        }

        return str_replace(',', '.', str_replace('.', '', $param));
    }
}
