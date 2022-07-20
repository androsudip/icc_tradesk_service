<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MassDestroyTicketRequest;
use App\Link;
use App\Service;
use App\Ticket;
use App\User;
use Gate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Razorpay\Api\Api;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class LinksController extends Controller
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
                return route('admin.links.show', $row->id);
            });

            return $table->make(true);
        }

        $links = Link::with('tickets.user')->orderBy('created_at', 'desc')->get();
        return view('admin.links.index', compact('links'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tickets = Ticket::with('service')->orderBy('created_at', 'desc')->get();
        return view('admin.links.create', compact('tickets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('link_generate_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ticket_id = $request->ticket_id;
        $ticket = Ticket::find($ticket_id);
        $user = User::find($ticket->assigned_to_user_id);
        $service = Service::find($ticket->service_id);
        $cost = $request->cost;
        $cost = number_format( $cost, 2);
        $api = new Api(config('constant.razorpay_key'),config('constant.razorpay_secret'));
        $url = $api->paymentLink->create([
                'amount' => (int)$cost,
                'currency' => 'INR',
                'description' => $service->name .' Service',
                'customer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact' => $user->number
                ],
                'notify' => [
                    'sms' => true,
                    'email' => true
                ],
                'reminder_enable' => true,
                'notes' => [
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->title
                ],
                'callback_url' => 'https://example-callback-url.com/',
                'callback_method' => 'get'
        ]);
        if($url){
            $links = Link::insert([
                'ticket_id' => $ticket_id,
                'payment_url' => $url->short_url,
                'status' => $url->status,
                'payment_link_id' => $url->id,
                'payment_link_json' => json_encode($url)
            ]);
        }

        return redirect()->route('admin.links.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Link $links)
    {
        abort_if(Gate::denies('link_generate_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $links = $links->with('tickets.user')->first();
        return view('admin.links.show', compact('links'));
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
