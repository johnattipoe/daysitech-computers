<?php

namespace App\Controllers;

use App\Models\Repair;
use App\Services\RepairService;

class RepairController
{
    protected RepairService $service;

    public function __construct()
    {
        $this->service = new RepairService();
    }

    public function showBooking(): void
    {
        view('repairs.book', [
            'title'         => 'Book a Repair',
            'deviceTypes'   => REPAIR_DEVICE_TYPES,
            'serviceTypes'  => REPAIR_SERVICE_TYPES,
            'user'          => current_user(),
            'pageScript'    => 'repairs.js',
        ]);
    }

    public function book(): void
    {
        require_csrf();

        $v = \Validator::make($_POST, [
            'name'               => 'required',
            'email'              => 'required|email',
            'phone'              => 'required|phone',
            'device_type'        => 'required',
            'service_type'       => 'required',
            'issue_description'  => 'required|min:10',
        ]);

        if ($v->fails()) {
            flash('error', $v->firstError());
            redirect('/repairs/book');
        }

        $repair = $this->service->book(sanitize_input($_POST));

        flash('success', "Repair booked! Your ticket number is {$repair['ticket_number']}. Save it to track your repair.");
        redirect('/repairs/track?ticket=' . $repair['ticket_number']);
    }

    public function track(): void
    {
        $ticket = $_GET['ticket'] ?? '';
        $repair = $ticket ? Repair::findByTicket($ticket) : null;

        view('repairs.track', [
            'title'      => 'Track Repair',
            'repair'     => $repair,
            'ticket'     => $ticket,
            'pageScript' => 'repairs.js',
        ]);
    }

    public function details(string $id): void
    {
        $repair = Repair::find($id);
        if (!$repair) {
            http_response_code(404);
            view('pages.404', ['title' => 'Repair Not Found']);
            return;
        }
        view('repairs.details', ['title' => 'Repair Ticket ' . $repair['ticket_number'], 'repair' => $repair]);
    }
}
