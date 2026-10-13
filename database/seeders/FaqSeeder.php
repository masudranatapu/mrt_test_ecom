<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            'general' => [
                ['How do I place an order?', "Browse the shop, add products to your cart and click \"Proceed to Checkout\". Log in or create an account, choose your delivery address and payment method, then place the order."],
                ['Do I need an account to buy?', 'You can browse and add products to your cart as a guest. To place an order you need a free account so we can send updates and you can track your order.'],
                ['Are your products genuine?', 'Yes. All products are sourced directly from trusted suppliers and distributors. Each product page shows its best-before date where applicable.'],
                ['What does "50+" in stock mean?', 'When we have more than 50 units of a product we show "50+". When stock is lower, you will see the exact number left.'],
            ],
            'shipping' => [
                ['How much is the delivery charge?', 'Delivery inside Dhaka costs ৳60 and outside Dhaka costs ৳120. The exact charge is shown at checkout before you place the order.'],
                ['How long does delivery take?', 'Orders inside Dhaka are usually delivered within 1–2 working days, and outside Dhaka within 3–5 working days.'],
                ['Can I return a product?', 'Yes. If you receive a damaged, expired or wrong item, contact us within 3 days of delivery. We will replace it or refund you after checking the item.'],
                ['How do refunds work?', 'Approved refunds are sent to your original payment method (bKash, card or bank) within 7 working days. Cash-on-delivery refunds are sent by bKash.'],
            ],
            'payment' => [
                ['Which payment methods do you accept?', 'Cash on delivery, bKash, and cards / mobile banking / internet banking through SSLCommerz. Approved business customers can also use 28-day credit.'],
                ['Is online payment safe?', 'Yes. Payments are processed on the secure pages of bKash and SSLCommerz. We never see or store your card number or bKash PIN.'],
                ['What is 28-day credit?', 'Business customers whose account is approved by our team can order now and pay within 28 days of the order date.'],
                ['My online payment failed. What now?', 'Your order is saved. Open the order from "My Orders" and click "Pay Now" to try again, or choose another payment method.'],
            ],
            'account' => [
                ['How do I track my order?', 'Log in and open "My Orders". Each order shows its current status: Pending, Confirmed, Processing, Shipped or Delivered.'],
                ['Can I cancel my order?', 'You can cancel an order yourself while it is still "Pending". After it is confirmed, please contact us.'],
                ['I forgot my password.', 'Click "Forgot password?" on the login page and enter your email. We will send you a link to set a new password.'],
                ['How do I get my business account approved?', 'Register with your business name, then contact us. Once approved you get access to credit terms and business pricing.'],
            ],
        ];

        foreach ($faqs as $category => $items) {
            foreach ($items as $order => [$question, $answer]) {
                Faq::firstOrCreate(
                    ['question' => $question],
                    ['category' => $category, 'answer' => $answer, 'sort_order' => $order + 1, 'status' => true]
                );
            }
        }
    }
}
