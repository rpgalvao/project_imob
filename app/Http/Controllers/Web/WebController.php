<?php

namespace LaraDev\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use LaraDev\Http\Controllers\Controller;
use LaraDev\Mail\Web\Contact;
use LaraDev\Property;

class WebController extends Controller
{
    public function home()
    {
        $head = $this->seo->render(env('APP_NAME') . ' - Home',
            'Encontre o imóvel dos seus sonhos na melhor e mais completa imobiliária. Estamos aqui para ajudá-lo!',
            route('web.home'),
            asset('frontend/assets/images/share.jpg'));

        $propertiesForSale = Property::sale()->available()->limit(3)->get();
        $propertiesForRent = Property::rent()->available()->limit(3)->get();

        return view('web.home', [
            'head' => $head,
            'propertiesForSale' => $propertiesForSale,
            'propertiesForRent' => $propertiesForRent,
        ]);
    }

    public function spotlight()
    {
        $head = $this->seo->render(env('APP_NAME') . ' - Destaque',
            'Nossos empreendimentos de destaque para trazer mais conforto para a sua vida',
            route('web.spotlight'),
            asset('frontend/assets/images/share.jpg'));

        return view('web.spotlight', [
            'head' => $head
        ]);
    }

    public function rent()
    {
        $head = $this->seo->render(env('APP_NAME') . ' - Alugar',
            'Alugue o imóvel dos seus sonhos na melhor e mais completa imobiliária. Estamos aqui para ajudá-lo!',
            route('web.rent'),
            asset('frontend/assets/images/share.jpg'));

        $filter = new FilterController();
        $filter->clearAllData();

        $properties = Property::rent()->available()->get();

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties,
            'type' => 'rent',
        ]);
    }

    public function rentProperty(Request $request)
    {
        $property = Property::where('slug', $request->slug)->first();

        $head = $this->seo->render(env('APP_NAME') . ' - Alugar',
            $property->headline ?? $property->title,
            route('web.rentProperty', ['property' => $property->slug]),
            $property->cover());

        return view('web.property', [
            'head' => $head,
            'property' => $property,
            'type' => 'rent',
        ]);
    }

    public function buy()
    {
        $head = $this->seo->render(env('APP_NAME') . ' - Comprar',
            'Compre o imóvel dos seus sonhos na melhor e mais completa imobiliária. Estamos aqui para ajudá-lo!',
            route('web.buy'),
            asset('frontend/assets/images/share.jpg'));

        $filter = new FilterController();
        $filter->clearAllData();

        $properties = Property::sale()->available()->get();

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties,
            'type' => 'sale',
        ]);
    }

    public function buyProperty(Request $request)
    {
        $property = Property::where('slug', $request->slug)->first();

        $head = $this->seo->render(env('APP_NAME') . ' - Comprar',
            $property->headline ?? $property->title,
            route('web.buyProperty', ['property' => $property->slug]),
            $property->cover());

        return view('web.property', [
            'head' => $head,
            'property' => $property,
            'type' => 'sale',
        ]);
    }

    public function filter()
    {
        $head = $this->seo->render(env('APP_NAME') . ' - Filtro',
            'Filtre o imóvel dos seus sonhos na melhor e mais completa imobiliária. Estamos aqui para ajudá-lo!',
            route('web.filter'),
            asset('frontend/assets/images/share.jpg'));

        $filter = new FilterController();
        $itemProperties = $filter->createQuery('id');

        foreach ($itemProperties as $property) {
            $properties[] = $property->id;
        }

        if (!empty($properties)) {
            $properties = Property::whereIn('id', $properties)->get();
        } else {
            $properties = Property::all();
        }

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties,
        ]);
    }

    public function experiences()
    {
        $head = $this->seo->render(env('APP_NAME') . ' - Experiência',
            'Encontre a melhor experiência de morar no imóvel dos seus sonhos na melhor e mais completa imobiliária. Estamos aqui para ajudá-lo!',
            route('web.experiences'),
            asset('frontend/assets/images/share.jpg'));

        $filter = new FilterController();
        $filter->clearAllData();

        $properties = Property::whereNotNull('experience')->get();

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties,
        ]);
    }

    public function experienceCategory(Request $request)
    {
        $filter = new FilterController();
        $filter->clearAllData();

        if ($request->slug == 'cobertura') {
            $head = $this->seo->render(env('APP_NAME') . ' - Cobertura',
                'Desfrute da experiência de morar em uma cobertura fantástica',
                route('web.experienceCategory', ['category' => 'cobertura']),
                asset('frontend/assets/images/share.jpg'));
            $properties = Property::where('experience', 'Cobertura')->get();
        } elseif ($request->slug == 'alto-padrao') {
            $head = $this->seo->render(env('APP_NAME') . ' - Alto Padrão',
                'Desfrute da experiência de morar em um imóvel de alto padrão',
                route('web.experienceCategory', ['category' => 'alto-padrao']),
                asset('frontend/assets/images/share.jpg'));
            $properties = Property::where('experience', 'Alto Padrão')->get();
        } elseif ($request->slug == 'de-frente-para-o-mar') {
            $head = $this->seo->render(env('APP_NAME') . ' - Vista pro Mar',
                'Desfrute da experiência de morar de frente para o mar',
                route('web.experienceCategory', ['category' => 'de-frente-para-o-mar']),
                asset('frontend/assets/images/share.jpg'));
            $properties = Property::where('experience', 'De Frente para o Mar')->get();
        } elseif ($request->slug == 'condominio-fechado') {$head = $this->seo->render(env('APP_NAME') . ' - Condomínio Fechado',
            'Desfrute da experiência de morar com toda a segurança em um condomínio fechado',
            route('web.experienceCategory', ['category' => 'condominio-fechado']),
            asset('frontend/assets/images/share.jpg'));
            $properties = Property::where('experience', 'Condominio Fechado')->get();
        } elseif ($request->slug == 'compacto') {
            $head = $this->seo->render(env('APP_NAME') . ' - Compacto',
                'Desfrute da experiência de morar em um imóvel compacto e funcional',
                route('web.experienceCategory', ['category' => 'compacto']),
                asset('frontend/assets/images/share.jpg'));
            $properties = Property::where('experience', 'Compacto')->get();
        } elseif ($request->slug == 'lojas-e-salas') {
            $head = $this->seo->render(env('APP_NAME') . ' - Lojas e Salas',
                'Desfrute da experiência de trabalhar em um ambiente feito sob medida para o seu negócio',
                route('web.experienceCategory', ['category' => 'lojas-e-salas']),
                asset('frontend/assets/images/share.jpg'));
            $properties = Property::where('experience', 'Lojas e Salas')->get();
        } else {
            $head = $this->seo->render(env('APP_NAME') . ' - Experiência',
                'Encontre a melhor experiência de morar no imóvel dos seus sonhos na melhor e mais completa imobiliária. Estamos aqui para ajudá-lo!',
                route('web.experiences'),
                asset('frontend/assets/images/share.jpg'));
            $properties = Property::whereNotNull('experience')->get();
        }

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties,
        ]);
    }

    public function contact()
    {
        $head = $this->seo->render(env('APP_NAME') . ' - Entre em contato',
            'Quer conversar com um corretor exclusivo e ter o atendimento diferenciado em busca do seu imóvel dos sonhos? Estamos aqui para ajudá-lo!',
            route('web.contact'),
            asset('frontend/assets/images/share.jpg'));

        return view('web.contact', [
            'head' => $head
        ]);
    }

    public function sendEmail(Request $request)
    {
        $data = [
            'reply_name' => $request->name,
            'reply_email' => $request->email,
            'cell' => $request->cell,
            'message' => $request->message
        ];

        Mail::send(new Contact($data));

        return redirect()->route('web.sendEmailSuccess');
    }

    public function sendEmailSuccess()
    {
        return view('web.contact_success');
    }
}
