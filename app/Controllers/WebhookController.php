<?php

namespace App\Controllers;

use App\Enums\OrderStatus;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\OrderModel;
use App\Services\Email\EmailService;
use ReflectionException;

class WebhookController extends ResourceController
{
    protected OrderModel $orderModel;
    protected EmailService $emailService;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->emailService = new EmailService();
    }

    /**
     * @throws ReflectionException
     */
    public function paymentNotification(): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->fail('Payload inválido', 400);
        }

        // Validação de segurança

        $signature = $this->request->getHeaderLine('X-Signature');
        if (!$this->validateSignature($payload, $signature)) {
            return $this->fail('Assinatura inválida', 401);
        }

        $eventType = $payload['event'] ?? null;

        switch ($eventType) {
            case 'payment.succeeded':
                $this->handlePaymentSucceeded($payload['data']);
                break;
            case 'payment.failed':
                $this->handlePaymentFailed($payload['data']);
                break;
            default:
                return $this->fail('Evento desconhecido', 400);
        }

        return $this->respond(['status' => 'ok']);
    }

    private function validateSignature(array $payload, ?string $signature): bool
    {
        // validação conforme o serviço externo
        // Exemplo: comparar hash HMAC com segredo compartilhado
        return true;
    }

    /**
     * @throws ReflectionException
     */
    private function handlePaymentSucceeded(array $data): void
    {
        $orderId = $data['order_id'] ?? null;
        $paymentId = $data['payment_id'] ?? null;
        $amountPaid = $data['amount'] ?? null;

        if (!$orderId) {
            log_message('error', 'Webhook payment succeeded: order_id missing');
            return;
        }

        $order = $this->orderModel->find($orderId);

        if (!$order) {
            log_message('error', "Webhook payment succeeded: order #{$orderId} not found");
            return;
        }

        $order->status = OrderStatus::COMPLETED;
//        $order->payment_id = $paymentId;
//        $order->amount_paid = $amountPaid;
//        $order->payment_date = date('Y-m-d H:i:s');

        $this->orderModel->save($order);

        // Envia email de confirmação de pagamento
        //$this->emailService->sendPaymentConfirmation($order);

        log_message('info', "Pedido #{$orderId} marcado como pago via webhook.");
    }

    /**
     * @throws ReflectionException
     */
    private function handlePaymentFailed(array $data): void
    {
        $orderId = $data['order_id'] ?? null;
        $failureReason = $data['failure_reason'] ?? 'Pagamento recusado';

        if (!$orderId) {
            log_message('error', 'Webhook payment failed: order_id missing');
            return;
        }

        $order = $this->orderModel->find($orderId);

        if (!$order) {
            log_message('error', "Webhook payment failed: order #{$orderId} not found");
            return;
        }

        $order->status = OrderStatus::CANCELLED;
//        $order->payment_failure_reason = $failureReason;
//        $order->payment_date = date('Y-m-d H:i:s');

        $this->orderModel->save($order);

        // Envia email notificando falha no pagamento
        //$this->emailService->sendPaymentFailureNotification($order, $failureReason);

        log_message('info', "Pedido #{$orderId} marcado como falha no pagamento via webhook.");
    }
}