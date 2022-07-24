<?php

namespace App\Http\Controllers\Admin;

use App\Bill;
use App\BillTicketServices;
use App\Http\Requests\MassDestroyTicketRequest;
use App\Link;
use App\Service;
use App\Ticket;
use App\User;
use Gate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BillsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Link::with(['tickets'])
                ->whereHas('tickets',function ($query){
                    $query->where('assigned_to_user_id',Auth::user()->id);
                })
                ->orderBy('created_at', 'desc')
                ->select(sprintf('%s.*', (new Link)->table))->get();
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'ticket_show';
                $editGate = 'ticket_edit';
                $deleteGate = 'ticket_delete';
                $crudRoutePart = 'tickets';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('title', function ($row) {
                return $row->ticket_id ? $row->title : "";
            });
            $table->addColumn('status_name', function ($row) {
                return $row->status ? $row->status->name : '';
            });

            $table->addColumn('view_link', function ($row) {
                return route('admin.bills.show', $row->id);
            });
            return $table->make(true);
        }

        $bills = Bill::with(['ticket','billServices.ticket','billServices.services'])
            ->where('created_by',Auth::user()->id)
            ->orderBy('created_at', 'desc')->get();
        return view('admin.bills.index', compact('bills'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tickets = Ticket::with('ticketServices','user')
                        ->where('assigned_to_user_id',Auth::user()->id)
                        ->orderBy('created_at', 'desc')->get();
        return view('admin.bills.create', compact('tickets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('bill_generate_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $service_ids = $request->input('service_ids');
        $ticket_id = $request->input('ticket_id');
        $service_ids= explode(",",$service_ids);
        $service_ids = array_filter($service_ids);
        $bill_services = [];
        $services = [];
        $total = 0;
        foreach ($service_ids as $value){
            $cost = $request->input('cost_'.$value);
            $remarks = $request->input('remarks_'.$value);
            $services[] = [
                'ticket_id' => $ticket_id,
                'service_id' => $value,
                'updated_cost' => $cost,
                'remarks' => $remarks,
            ];
            $total += $cost;
        }
        $bill = Bill::create([
            'ticket_id' => $ticket_id,
            'bill_cost' => $total,
            'created_by' => Auth::user()->id
        ]);
        foreach ($services as $key => $value){
            $services[$key]['bill_id'] = $bill->id;
        }
        BillTicketServices::insert($services);

        return redirect()->route('admin.bills.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('bill_generate_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bills = Bill::with(['ticket','billServices.ticket','billServices.services'])
                    ->where('created_by',Auth::user()->id)
                    ->where('id',$id)->first();
        return view('admin.bills.show', compact('bills'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function massDestroy(MassDestroyTicketRequest $request)
    {
        Link::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
