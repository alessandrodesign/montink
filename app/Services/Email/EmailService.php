<?php

namespace App\Services\Email;

use App\Entities\OrderEntity;
use App\Entities\UserEntity;
use App\Models\OrderItemModel;
use App\Models\UserModel;
use App\Services\BaseService;
use CodeIgniter\Email\Email;

class EmailService extends BaseService implements EmailServiceInterface
{
    protected Email $email;
    protected UserModel $userModel;
    protected OrderItemModel $orderItemModel;

    public function __construct()
    {
        $this->email = service('email');
        $this->userModel = new UserModel();
        $this->orderItemModel = new OrderItemModel();
    }

    public function sendOrderConfirmation(OrderEntity $order): bool
    {
        $this->clearErrors();

        $user = $this->userModel->find($order->user_id);

        if (!$user) {
            $this->setError('user', 'User not found');
            return false;
        }

        $items = $this->orderItemModel->getItemsWithProductDetails($order->id);

        $this->email->setTo((string)$user->email);
        $this->email->setSubject('Order Confirmation - ' . $order->order_number);

        $message = view('emails/order_confirmation', [
            'order' => $order,
            'user' => $user,
            'items' => $items,
        ]);

        $this->email->setMessage($message);

        if (!$this->email->send(false)) {
            $this->setError('email', $this->email->printDebugger(['headers']));
            return false;
        }

        return true;
    }

    public function sendWelcomeEmail(UserEntity $user): bool
    {
        $this->clearErrors();

        $this->email->setTo((string)$user->email);
        $this->email->setSubject('Welcome to Our Store');

        $message = view('emails/welcome', [
            'user' => $user,
        ]);

        $this->email->setMessage($message);

        if (!$this->email->send(false)) {
            $this->setError('email', $this->email->printDebugger(['headers']));
            return false;
        }

        return true;
    }

    public function sendPasswordReset(UserEntity $user, string $resetLink): bool
    {
        $this->clearErrors();

        $this->email->setTo((string)$user->email);
        $this->email->setSubject('Password Reset Request');

        $message = view('emails/password_reset', [
            'user' => $user,
            'resetLink' => $resetLink,
        ]);

        $this->email->setMessage($message);

        if (!$this->email->send(false)) {
            $this->setError('email', $this->email->printDebugger(['headers']));
            return false;
        }

        return true;
    }

    public function sendPaymentConfirmation($order): bool
    {
        $to = $order->user_email ?? $order->email ?? null;
        if (!$to) {
            log_message('error', "EmailService: email do usuário não encontrado para pedido #{$order->id}");
            return false;
        }

        $subject = "Confirmação de pagamento do pedido #{$order->id}";
        $message = view('emails/payment_confirmation', ['order' => $order]);

        return $this->sendEmail($to, $subject, $message);
    }

    public function sendPaymentFailureNotification($order, string $reason): bool
    {
        $to = $order->user_email ?? $order->email ?? null;
        if (!$to) {
            log_message('error', "EmailService: email do usuário não encontrado para pedido #{$order->id}");
            return false;
        }

        $subject = "Falha no pagamento do pedido #{$order->id}";
        $message = view('emails/payment_failure', ['order' => $order, 'reason' => $reason]);

        return $this->sendEmail($to, $subject, $message);
    }

    protected function sendEmail(string $to, string $subject, string $message): bool
    {
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMessage($message);
        $this->email->setMailType('html');

        if (!$this->email->send()) {
            log_message('error', $this->email->printDebugger(['headers']));
            log_message('error', 'EmailService: falha ao enviar email para ' . $to);
            return false;
        }

        return true;
    }
}