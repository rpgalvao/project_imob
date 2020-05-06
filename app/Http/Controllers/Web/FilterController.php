<?php

namespace LaraDev\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LaraDev\Http\Controllers\Controller;

class FilterController extends Controller
{
    public function search(Request $request)
    {
        session()->remove('category');
        session()->remove('type');
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('base_price');
        session()->remove('limit_price');

        if ($request->search === 'buy') {
            session()->put('sale', true);
            session()->remove('rent');
            $properties = $this->createQuery('category');
        }

        if ($request->search === 'rent') {
            session()->put('rent', true);
            session()->remove('sale');
            $properties = $this->createQuery('category');
        }

        if ($properties->count()) {
            foreach ($properties as $categoryProperty) {
                $category[] = $categoryProperty->category;
            }

            $collect = collect($category);
            return response()->json($this->setResponse('success', $collect->unique()->toArray()));
        }

        return response()->json($this->setResponse('fail', [],
            'Ooops, não foi retornado nenhum dado para essa pesquisa!'));
    }

    public function category(Request $request)
    {
        session()->remove('type');
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('base_price');
        session()->remove('limit_price');

        session()->put('category', $request->search);

        $typeProperties = $this->createQuery('type');

        if ($typeProperties->count()) {
            foreach ($typeProperties as $property) {
                $type[] = $property->type;
            }

            $collect = collect($type);
            return response()->json($this->setResponse('success', $collect->unique()->toArray()));
        }

        return response()->json($this->setResponse('fail', [],
            'Ooops, não foi retornado nenhum dado para essa pesquisa!'));
    }

    public function type(Request $request)
    {
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('base_price');
        session()->remove('limit_price');

        session()->put('type', $request->search);

        $neighborhoodProperties = $this->createQuery('neighborhood');

        if ($neighborhoodProperties->count()) {
            foreach ($neighborhoodProperties as $property) {
                $neighborhood[] = $property->neighborhood;
            }

            $collect = collect($neighborhood);
            return response()->json($this->setResponse('success', $collect->unique()->toArray()));
        }

        return response()->json($this->setResponse('fail', [],
            'Ooops, não foi retornado nenhum dado para essa pesquisa!'));
    }

    public function neighborhood(Request $request)
    {
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('base_price');
        session()->remove('limit_price');

        session()->put('neighborhood', $request->search);

        $bedroomsProperties = $this->createQuery('bedrooms');

        if ($bedroomsProperties->count()) {
            foreach ($bedroomsProperties as $property) {
                if ($property->bedrooms === 0 || $property->bedrooms === 1) {
                    $bedrooms[] = $property->bedrooms . ' quarto';
                } else {
                    $bedrooms[] = $property->bedrooms . ' quartos';
                }
            }

            $bedrooms[] = 'Indiferente';

            $collect = collect($bedrooms)->unique()->toArray();
            sort($collect);
            return response()->json($this->setResponse('success', $collect));
        }

        return response()->json($this->setResponse('fail', [],
            'Ooops, não foi retornado nenhum dado para essa pesquisa!'));
    }

    public function bedrooms(Request $request)
    {
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('base_price');
        session()->remove('limit_price');

        session()->put('bedrooms', $request->search);

        $suitesProperties = $this->createQuery('suites');

        if ($suitesProperties->count()) {
            foreach ($suitesProperties as $property) {
                if ($property->suites === 0 || $property->suites === 1) {
                    $suites[] = $property->suites . ' suíte';
                } else {
                    $suites[] = $property->suites . ' suítes';
                }
            }

            $suites[] = 'Indiferente';

            $collect = collect($suites)->unique()->toArray();
            sort($collect);
            return response()->json($this->setResponse('success', $collect));
        }

        return response()->json($this->setResponse('fail', [],
            'Ooops, não foi retornado nenhum dado para essa pesquisa!'));
    }

    public function suites(Request $request)
    {
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('base_price');
        session()->remove('limit_price');

        session()->put('suites', $request->search);

        $bathroomsProperties = $this->createQuery('bathrooms');

        if ($bathroomsProperties->count()) {
            foreach ($bathroomsProperties as $property) {
                if ($property->bathrooms === 0 || $property->bathrooms === 1) {
                    $bathrooms[] = $property->bathrooms . ' banheiro';
                } else {
                    $bathrooms[] = $property->bathrooms . ' banheiros';
                }
            }

            $bathrooms[] = 'Indiferente';

            $collect = collect($bathrooms)->unique()->toArray();
            sort($collect);
            return response()->json($this->setResponse('success', $collect));
        }

        return response()->json($this->setResponse('fail', [],
            'Ooops, não foi retornado nenhum dado para essa pesquisa!'));
    }

    public function bathrooms(Request $request)
    {
        session()->remove('garage');
        session()->remove('base_price');
        session()->remove('limit_price');

        session()->put('bathrooms', $request->search);

        $garageProperties = $this->createQuery('garage');

        if ($garageProperties->count()) {
            foreach ($garageProperties as $property) {
                if ($property->garage === 0 || $property->garage === 1) {
                    $garage[] = $property->garage . ' garagem';
                } else {
                    $garage[] = $property->garage . ' garagens';
                }
            }

            $garage[] = 'Indiferente';

            $collect = collect($garage)->unique()->toArray();
            sort($collect);
            return response()->json($this->setResponse('success', $collect));
        }

        return response()->json($this->setResponse('fail', [],
            'Ooops, não foi retornado nenhum dado para essa pesquisa!'));
    }

    public function garage(Request $request)
    {
        session()->remove('base_price');
        session()->remove('limit_price');

        session()->put('garage', $request->search);

        if (session('sale') === true) {
            $priceBaseProperties = $this->createQuery('sale_price as price');
        } else {
            $priceBaseProperties = $this->createQuery('rent_price as price');
        }

        if ($priceBaseProperties->count()) {
            foreach ($priceBaseProperties as $property) {
                $price[] = 'À partir de R$ ' . number_format($property->price, 2, ',', '.');
            }

            $price[] = 'Indiferente';

            $collect = collect($price)->unique()->toArray();
            sort($collect);
            return response()->json($this->setResponse('success', $collect));
        }

        return response()->json($this->setResponse('fail', [],
            'Ooops, não foi retornado nenhum dado para essa pesquisa!'));
    }

    public function basePrice(Request $request)
    {
        session()->remove('limit_price');

        session()->put('base_price', $request->search);

        if (session('sale') === true) {
            $priceLimitProperties = $this->createQuery('sale_price as price');
        } else {
            $priceLimitProperties = $this->createQuery('rent_price as price');
        }

        if ($priceLimitProperties->count()) {
            foreach ($priceLimitProperties as $property) {
                $price[] = 'Até R$ ' . number_format($property->price, 2, ',', '.');
            }

            $price[] = 'Indiferente';

            $collect = collect($price)->unique()->toArray();
            sort($collect);
            return response()->json($this->setResponse('success', $collect));
        }

        return response()->json($this->setResponse('fail', [],
            'Ooops, não foi retornado nenhum dado para essa pesquisa!'));
    }

    public function limitPrice(Request $request)
    {
        session()->put('limit_price', $request->search);

        return response()->json($this->setResponse('success', []));
    }

    public function clearAllData()
    {
        session()->remove('sale');
        session()->remove('rent');
        session()->remove('category');
        session()->remove('type');
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('base_price');
        session()->remove('limit_price');
    }

    public function createQuery($field)
    {
        $sale = session('sale');
        $rent = session('rent');
        $category = session('category');
        $type = session('type');
        $neighborhood = session('neighborhood');
        $bedrooms = session('bedrooms');
        $suites = session('suites');
        $bathrooms = session('bathrooms');
        $garage = session('garage');
        $basePrice = session('base_price');
        $limitPrice = session('limit_price');

        return DB::table('properties')
            ->when($sale, function ($query, $sale) {
                return $query->where('sale', $sale);
            })
            ->when($rent, function ($query, $rent) {
                return $query->where('rent', $rent);
            })
            ->when($category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($type, function ($query, $type) {
                return $query->whereIn('type', $type);
            })
            ->when($neighborhood, function ($query, $neighborhood) {
                return $query->whereIn('neighborhood', $neighborhood);

            })
            ->when($bedrooms, function ($query, $bedrooms) {
                if ($bedrooms == 'Indiferente') {
                    return $query;
                }
                $bedrooms = (int)$bedrooms;
                return $query->where('bedrooms', $bedrooms);
            })
            ->when($suites, function ($query, $suites) {
                if ($suites == 'Indiferente') {
                    return $query;
                }
                $suites = (int)$suites;
                return $query->where('suites', $suites);
            })
            ->when($bathrooms, function ($query, $bathrooms) {
                if ($bathrooms == 'Indiferente') {
                    return $query;
                }
                $bathrooms = (int)$bathrooms;
                return $query->where('bathrooms', $bathrooms);
            })
            ->when($garage, function ($query, $garage) {
                if ($garage == 'Indiferente') {
                    return $query;
                }
                $garage = (int)$garage;
                return $query->where('garage', $garage);
            })
            ->when($basePrice, function ($query, $basePrice) {
                if ($basePrice == 'Indiferente') {
                    return $query;
                }
                $basePrice = (float)str_replace(',', '.', str_replace('.', '', explode('R$ ', $basePrice, 2)[1]));
                if (session('sale') == true) {
                    return $query->where('sale_price', '>=', $basePrice);
                } else {
                    return $query->where('rent_price', '>=', $basePrice);
                }
            })
            ->when($limitPrice, function ($query, $limitPrice) {
                if ($limitPrice == 'Indiferente') {
                    return $query;
                }
                $limitPrice = (float)str_replace(',', '.', str_replace('.', '', explode('R$ ', $limitPrice, 2)[1]));
                if (session('sale') == true) {
                    return $query->where('sale_price', '<=', $limitPrice);
                } else {
                    return $query->where('rent_price', '<=', $limitPrice);
                }
            })
            ->get([$field]);
    }

    private function setResponse(string $status, array $data = null, string $message = null)
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message,
        ];
    }
}
