<?php

namespace App\Http\Controllers\Admin;

use App\Bill;
use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Payments;
use App\Priority;
use App\Role;
use App\RoleUser;
use App\Service;
use App\Status;
use App\Ticket;
use App\TicketServices;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class TicketsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Ticket::with(['ticketServices','status', 'priority', 'category', 'assigned_to_user', 'comments'])
                ->filterTickets($request)
                ->where('assigned_to_user_id',Auth::user()->id)
                ->select(sprintf('%s.*', (new Ticket)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'ticket_show';
                $editGate      = 'ticket_edit';
                $deleteGate    = 'ticket_delete';
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
                return $row->title ? $row->title : "";
            });
            $table->addColumn('status_name', function ($row) {
                return $row->status ? $row->status->name : '';
            });
            $table->addColumn('status_color', function ($row) {
                return $row->status ? $row->status->color : '#000000';
            });

            $table->addColumn('priority_name', function ($row) {
                return $row->priority ? $row->priority->name : '';
            });
            $table->addColumn('priority_color', function ($row) {
                return $row->priority ? $row->priority->color : '#000000';
            });

            $table->editColumn('author_name', function ($row) {
                return $row->author_name ? $row->author_name : "";
            });
            $table->editColumn('author_email', function ($row) {
                return $row->author_email ? $row->author_email : "";
            });
            $table->addColumn('assigned_to_user_name', function ($row) {
                return $row->assigned_to_user ? $row->assigned_to_user->name : '';
            });

            $table->addColumn('comments_count', function ($row) {
                return $row->comments->count();
            });

            $table->addColumn('view_link', function ($row) {
                return route('admin.tickets.show', $row->id);
            });

            $table->rawColumns(['actions', 'placeholder', 'status', 'priority', 'category', 'assigned_to_user']);

            return $table->make(true);
        }
        $priorities = Priority::all();
        $statuses = Status::all();
        $services = Service::all();

        return view('admin.tickets.index', compact('priorities', 'statuses', 'services'));
    }

    public function create()
    {
        abort_if(Gate::denies('ticket_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $statuses = Status::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $priorities = Priority::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $services = Service::where('status','1')->orderBy('created_at','desc')->get();

        $assigned_to_users = User::whereHas('roles', function($query) {
                $query->whereId(2);
            })
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        return view('admin.tickets.create', compact('statuses', 'priorities', 'services', 'assigned_to_users'));
    }

    public function store(StoreTicketRequest $request)
    {
        $user_id = Auth::user()->id;
        $service_id = $request->input('service_id');
        if(is_array($service_id) && count($service_id) > 1)    {
            $roleId = Role::where('title',config('constant.department_user_title'))->pluck('id');
            $user = User::with(['roles'])->whereHas('roles',function ($query) use($roleId){
                $query->where('id',$roleId);
            })->pluck('id');
            if(!empty($user) && $user != null){
                $user_id = $user->first();
            }
        }
        $request->request->add([
            'assigned_to_user_id'   => $user_id,
            'status_id' => 1 //open
        ]);
        $ticket = Ticket::create($request->all());
        $ticket->ticketServices()->sync($request->input('service_id', []));

        foreach ($request->input('attachments', []) as $file) {
            $ticket->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('attachments');
        }

        return redirect()->route('admin.tickets.index');
    }

    public function edit(Ticket $ticket)
    {
        abort_if(Gate::denies('ticket_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $statuses = Status::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $priorities = Priority::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $services = Service::where('status','1')->orderBy('created_at','desc')->get();

        $assigned_to_users = User::whereHas('roles', function($query) {
                $query->whereId(2);
            })
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $ticket->load('ticketServices','status', 'priority', 'category', 'assigned_to_user');

        return view('admin.tickets.edit', compact('statuses', 'priorities', 'services', 'assigned_to_users', 'ticket'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $user_id = Auth::user()->id;
        $service_id = $request->input('service_id');
        if(is_array($service_id) && count($service_id) > 1)    {
            $roleId = Role::where('title',config('constant.department_user_title'))->pluck('id');
            $user = User::with(['roles'])->whereHas('roles',function ($query) use($roleId){
                $query->where('id',$roleId);
            })->pluck('id');
            if(!empty($user) && $user != null){
                $user_id = $user->first();
            }
        }
        $request->request->add([
            'assigned_to_user_id'   => $user_id,
        ]);
        $ticket->update($request->all());
        $ticket->ticketServices()->sync($request->input('service_id', []));

        if (count($ticket->attachments) > 0) {
            foreach ($ticket->attachments as $media) {
                if (!in_array($media->file_name, $request->input('attachments', []))) {
                    $media->delete();
                }
            }
        }

        $media = $ticket->attachments->pluck('file_name')->toArray();

        foreach ($request->input('attachments', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $ticket->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('attachments');
            }
        }

        return redirect()->route('admin.tickets.index');
    }

    public function show(Ticket $ticket)
    {
        abort_if(Gate::denies('ticket_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ticket->load('status', 'priority', 'category', 'assigned_to_user', 'comments');

        return view('admin.tickets.show', compact('ticket'));
    }

    public function destroy(Ticket $ticket)
    {
        abort_if(Gate::denies('ticket_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ticket->delete();

        return back();
    }

    public function massDestroy(MassDestroyTicketRequest $request)
    {
        Ticket::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment_text' => 'required'
        ]);
        $user = auth()->user();
        $comment = $ticket->comments()->create([
            'author_name'   => $user->name,
            'author_email'  => $user->email,
            'user_id'       => $user->id,
            'comment_text'  => $request->comment_text
        ]);

        $ticket->sendCommentNotification($comment);

        return redirect()->back()->withStatus('Your comment added successfully');
    }

    public function getServices(Request $request)
    {
        $ticket_id = $request->input('id');
        if($ticket_id){
            $services = Service::join('service_ticket','service_id','id')->where('service_ticket.ticket_id',$ticket_id)->get();
            return $services;
        }
    }

    public function getResponse(Request $request){
        $payment_id = $request->input('razorpay_payment_id');
        $payment_link_id = $request->input('razorpay_payment_link_id');
        $payment_link_reference_id = $request->input('razorpay_payment_link_reference_id');
        $payment_link_status = $request->input('razorpay_payment_link_status');
        $signature = $request->input('razorpay_signature');
        $api = new Api(config('constant.razorpay_key'),config('constant.razorpay_secret'));
        $payment = $api->payment->fetch($payment_id);
        $cost = ($payment->amount/100);
        if($payment){
            $billId= $payment->notes->bill_id;
            Payments::insert([
                'payment_id' => $payment_id,
                'payment_link_id' => $payment_link_id,
                'payment_link_reference_id' => $payment_link_reference_id,
                'payment_link_status' => $payment_link_status,
                'signature' => $signature,
                'method' => $payment->method,
                'amount' => $cost,
                'bill_id' => $payment->notes->bill_id
            ]);
            if($payment->status == 'captured'){
                $bill = Bill::where('id',$billId)->first();
                $paidCost = $cost;
                if($bill->remaining_cost != 0){
                    $paidCost = $bill->remaining_cost - $cost;
                }
                $bill = Bill::where('id',$billId)->update([
                    'remaining_cost' => $paidCost
                ]);
                Session::flash('message', 'Your Payment is Successfully');
                Session::flash('alert-class', 'alert-success');

            }else{
                Session::flash('message', 'Your Payment is Failed');
                Session::flash('alert-class', 'alert-danger');

            }
        }

        return redirect()->route('admin.tickets.index');
    }
}
