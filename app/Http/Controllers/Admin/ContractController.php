<?php

namespace LaraDev\Http\Controllers\Admin;

use Illuminate\Http\Request;
use LaraDev\Contract;
use LaraDev\Http\Controllers\Controller;
use LaraDev\Http\Requests\Admin\Contract as ContractRequest;
use LaraDev\Property;
use LaraDev\User;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contracts = Contract::orderBy('id', 'DESC')->with(['ownerObject', 'acquirerObject'])->get();

        return view('admin.contracts.index', [
            'contracts' => $contracts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lessors = User::lessors();
        $lessees = User::lessees();

        return view('admin.contracts.create', [
            'lessors' => $lessors,
            'lessees' => $lessees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractRequest $request)
    {
        $contractCreate = Contract::create($request->all());

        if ($request->property) {
            $property = Property::where('id', $request->property)->first();
            if ($request->status === 'active') {
                $property->status = 0;
                $property->save();
            } else {
                $property->status = 1;
                $property->save();
            }
        }

        return redirect()->route('admin.contracts.edit', [
            'contract' => $contractCreate->id
        ])->with(['color' => 'green', 'message' => 'Contrato cadastrado com sucesso!']);

//        $contract = new Contract();
//        $contract->fill(\request()->all());
//        var_dump($contract->getAttributes());
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
        $contract = Contract::where('id', $id)->first();
        $lessors = User::lessors();
        $lessees = User::lessees();

        return view('admin.contracts.edit', [
            'contract' => $contract,
            'lessors' => $lessors,
            'lessees' => $lessees
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContractRequest $request, $id)
    {
        $contract = Contract::where('id', $id)->first();
        $contract->fill($request->all());
//        var_dump($contract->getAttributes());
        $contract->save();

        if ($request->property) {
            $property = Property::where('id', $request->property)->first();
            if ($request->status === 'active') {
                $property->status = 0;
                $property->save();
            } else {
                $property->status = 1;
                $property->save();
            }
        }

        return redirect()->route('admin.contracts.edit', [
            'contract' => $contract->id
        ])->with(['color' => 'green', 'message' => 'Contrato editado com sucesso!']);
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

    public function getOwnerInfo(Request $request)
    {
        $lessor = User::where('id', $request->user)->first([
            'id',
            'civil_status',
            'spouse_name',
            'spouse_document'
        ]);

        $civilStatusSpouseRequired = [
            'married',
            'separated'
        ];

        if (empty($lessor)) {
            $spouse = null;
            $companies = null;
            $properties = null;
        } else {
            if (in_array($lessor->civil_status, $civilStatusSpouseRequired)) {
                $spouse = [
                    'spouse_name' => $lessor->spouse_name,
                    'spouse_document' => $lessor->spouse_document
                ];
            } else {
                $spouse = null;
            }

            $companies = $lessor->companies()->get([
                'id',
                'alias_name',
                'document_company'
            ]);

            $getProperties = $lessor->properties()->get();
            $properties = [];
            foreach ($getProperties as $property) {
                $properties[] = [
                    'id' => $property->id,
                    'description' => '#' . $property->id . ' - ' . $property->street . ', ' . $property->number . ' ' . $property->neighborhood . ' ' . $property->city . '/' . $property->state . ' (' . $property->zipcode . ')'];
            }
        }

        $json['spouse'] = $spouse;
        $json['companies'] = $companies;
        $json['properties'] = $properties;

        return response()->json($json);
    }

    public function getAcquirerInfo(Request $request)
    {
        $lessee = User::where('id', $request->user)->first([
            'id',
            'civil_status',
            'spouse_name',
            'spouse_document'
        ]);

        $civilStatusSpouseRequired = [
            'married',
            'separated'
        ];

        if (empty($lessee)) {
            $spouse = null;
            $companies = null;
        } else {
            if (in_array($lessee->civil_status, $civilStatusSpouseRequired)) {
                $spouse = [
                    'spouse_name' => $lessee->spouse_name,
                    'spouse_document' => $lessee->spouse_document
                ];
            } else {
                $spouse = null;
            }

            $companies = $lessee->companies()->get([
                'id',
                'alias_name',
                'document_company'
            ]);
        }

        $json['spouse'] = $spouse;
        $json['companies'] = $companies;

        return response()->json($json);
    }

    public function getPropertyInfo(Request $request)
    {
        $property = Property::where('id', $request->property)->first();

        if (empty($property)) {
            $property = null;
        } else {
            $property = [
                'id' => $property->id,
                'sale_price' => $property->sale_price,
                'rent_price' => $property->rent_price,
                'tribute' => $property->tribute,
                'condominium' => $property->condominium
            ];
        }

        $json['property'] = $property;

        return response()->json($json);
    }
}
