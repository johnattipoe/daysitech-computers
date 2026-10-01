<?php

namespace App\Services;

use App\Models\Repair;

class RepairService
{
    protected NotificationService $notifications;
    protected EmailService $email;

    public function __construct()
    {
        $this->notifications = new NotificationService();
        $this->email = new EmailService();
    }

    public function book(array $input): array
    {
        $user = current_user();

        $repair = Repair::create([
            'ticket_number'       => generate_repair_ticket(),
            'user_id'             => $user['id'] ?? null,
            'customer'            => [
                'name'  => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
            ],
            'device_type'         => $input['device_type'],
            'brand'                => $input['brand'] ?? '',
            'model'                => $input['model'] ?? '',
            'serial_number'        => $input['serial_number'] ?? '',
            'issue_description'    => $input['issue_description'],
            'service_type'         => $input['service_type'],
            'status'               => REPAIR_STATUS_BOOKED,
            'priority'             => $input['priority'] ?? 'normal',
            'estimated_cost'       => $input['estimated_cost'] ?? null,
            'drop_off_date'        => $input['drop_off_date'] ?? date('Y-m-d'),
            'expected_completion'  => null,
            'technician_notes'     => '',
        ]);

        if ($user) {
            $this->notifications->push($user['id'], NOTIFY_REPAIR, 'Repair booked', "Your repair ticket {$repair['ticket_number']} has been created.", "/repairs/track?ticket={$repair['ticket_number']}");
        }
        $this->email->sendRepairConfirmation($input['email'], $repair);

        return $repair;
    }

    public function updateStatus(string $id, string $status, ?string $note = null): array
    {
        $repair = Repair::updateStatus($id, $status, $note);

        if (!empty($repair['user_id'])) {
            $this->notifications->push(
                $repair['user_id'],
                NOTIFY_REPAIR,
                'Repair status updated',
                "Ticket {$repair['ticket_number']} is now: " . status_label($status),
                "/repairs/track?ticket={$repair['ticket_number']}"
            );
        }
        $this->email->sendRepairStatusUpdate($repair['customer']['email'] ?? '', $repair);

        return $repair;
    }

    public function setQuote(string $id, float $estimatedCost): array
    {
        return Repair::update($id, ['estimated_cost' => $estimatedCost, 'status' => REPAIR_STATUS_AWAITING_APPROVAL]);
    }
}
