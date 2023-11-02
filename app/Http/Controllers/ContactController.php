<?php

namespace App\Http\Controllers;

use App\Helpers\Breadcrumbs;
use App\Helpers\LinkItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Contact;
use App\Category;
use App\Page;
use App\Product;
use App\Helpers\Helper;
use App\InstallmentPayment;
use App\Mail\ContactMail;
use App\Mail\InstallmentPaymentAdminMail;
use App\Mail\InstallmentPaymentClientMail;
use App\Mail\UserRegisteredMail;
use App\Services\GrowCrmService;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Hash;

class ContactController extends Controller
{
    /**
     * Show contacts page
     */
    public function index()
    {
        $page = Helper::translation(Page::find(2)); // contacts page
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.nav.contacts'), route('contacts'), LinkItem::STATUS_INACTIVE));
        $address = Helper::translation(Helper::staticText('contact_address'))->description ?? '';
        return view('contacts', compact('breadcrumbs', 'page', 'address'));
    }

    /**
     * Send contact email
     *
     * @return json
     */
    public function send(Request $request)
    {
        $captchaKey = $request->input('captcha_key', '');
        $data = $request->validate([
            'captcha_key' => 'required',
            'captcha' => 'required|captcha_api:' . $captchaKey . ',flat',
            'name' => 'required',
            // 'email' => 'required',
            'phone' => 'required',
            'message' => '',
            'type' => '',
        ]);

        $telegram_chat_id = config('services.telegram.chat_id');
        if (empty($data['type']) || !array_key_exists($data['type'], Contact::types())) {
            $data['type'] = Contact::TYPE_CONTACT;
        }

        // save to database
        $contact = Contact::create($data);
        $contact->type = $data['type'];
        $contact->info = '';

        $category = null;
        $product = null;

        if (isset($request->product_id)) {
            $product = Product::find((int)$request->product_id);
            if ($product) {
                $contact->info = '<a href="' . $product->url . '" target="_blank" >' . $product->name . '</a>';
                if ($product->in_stock == 0 /*&& $product->isAvailableFromPartner()*/) {
                    $telegram_chat_id = config('services.telegram.partner_chat_id');
                }
            }
        } elseif (isset($request->category_id)) {
            $category = Category::find((int)$request->category_id);
            if ($category) {
                $contact->info = '<a href="' . $category->url . '" target="_blank" >' . $category->name . '</a>';
            }
        }
        $contact->save();

        // send telegram
        $text = view('telegram.admin.contact', compact('contact', 'product'))->render();
        Helper::toTelegram($text, 'Markdown', $telegram_chat_id);

        // send email
        Mail::to(setting('contact.email'))->send(new ContactMail($contact, $product));

        // send to crm
        $crm = new GrowCrmService();
        $leadData = [
            'lead_title' => 'Заявка #' . $contact->id,
            'lead_firstname' => $contact->name,
            'lead_phone' => $contact->phone,
            'lead_description' => 'Сообщение: ' . $contact->message,
        ];
        if ($contact->info) {
            $leadData['lead_description'] .= ' / Инфо: ' . $contact->info;
        }
        $crm->createLead($leadData);

        // return redirect()->route('home', ['#contact-form'])->withSuccess(__('home.contact_message_success'));

        $data = [
            'message' => __('main.form.contact_form_success'),
        ];

        return response()->json($data);
    }

    /**
     * Send installment payment request
     *
     * @return json
     */
    public function sendInstallmentPayment(Request $request)
    {
        $partners = config('services.installment_payment.partners');
        $partnersKeys = array_keys($partners);

        // $captchaKey = $request->input('captcha_key', '');
        $data = $this->validate($request, [
            // 'captcha_key' => 'required',
            // 'captcha' => 'required|captcha_api:' . $captchaKey . ',flat',
            // 'partner' => 'required|in:' . implode(',', $partnersKeys),
            // 'id_in_partner_system' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'passport_series' => 'required',
            'passport_number' => 'required',
            'card_number' => 'required',
        ]);

        $installmentPaymentProducts = [];
        $contactInfo = '';
        $category = null;
        $product = null;
        if (isset($request->product_id)) {
            $product = Product::find((int)$request->product_id);
            if ($product) {
                $contactInfo .= '<a href="' . $product->url . '" target="_blank" >' . $product->name . '</a>' . "<br>\n";
                $contactInfo .= 'SKU: ' . $product->sku . "<br>\n";
                $installmentPaymentProduct = [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->installment_price,
                ];
            }
            if (isset($installmentPaymentProduct)) {
                $installmentPaymentProducts[] = $installmentPaymentProduct;
            }
        } elseif (isset($request->category_id)) {
            $category = Category::find((int)$request->category_id);
            if ($category) {
                $contactInfo .= '<a href="' . $category->url . '" target="_blank" >' . $category->name . '</a>' . "<br>\n";
            }
        }

        $contactInfo .= __('main.customer') . ': ' . $data['last_name'] . ' ' . $data['first_name'] . "<br>\n";
        $contactInfo .= __('main.phone_number') . ': ' . $data['phone_number'] . "<br>\n";
        $contactInfo .= __('main.email') . ': ' . $data['email'] . "<br>\n";
        $contactInfo .= __('main.passport_series') . ': ' . $data['passport_series'] . "<br>\n";
        $contactInfo .= __('main.passport_number') . ': ' . $data['passport_number'] . "<br>\n";
        $contactInfo .= __('main.card_number') . ': ' . $data['card_number'] . "<br>\n";
        // $contactInfo .= __('main.partner') . ': ' . $data['partner'] . "<br>\n";
        // $contactInfo .= __('main.id_in_partner_system') . ': ' . $data['id_in_partner_system'] . "<br>\n";

        // save to database
        $contact = Contact::create([
            'name' => $data['last_name'] . ' ' . $data['first_name'],
            'type' => Contact::TYPE_INSTALLMENT_PAYMENT,
            'phone' => $data['phone_number'],
            'info' => $contactInfo,
        ]);
        $installmentPayment = InstallmentPayment::create([
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'passport_series' => $data['passport_series'],
            'passport_number' => $data['passport_number'],
            'card_number' => $data['card_number'],
            // 'partner' => $data['partner'],
            // 'id_in_partner_system' => $data['id_in_partner_system'],
            'info' => '',
            'products' => json_encode($installmentPaymentProducts),
        ]);

        if (!$contact) {
            return [];
        }

        // create user if not exists
        $checkUser = User::where('email', $data['email'])->first();
        if (!$checkUser) {
            $user = new User();
            $password = Str::random(12);
            $user->password = Hash::make($password);
            $user->email = $data['email'];
            $user->name = $data['first_name'] . ' ' . $data['last_name'];
            $user->save();
            Mail::to($data['email'])->send(new UserRegisteredMail($user, $password));
        }

        // send to client
        // send email
        Mail::to($data['email'])->send(new InstallmentPaymentClientMail($installmentPayment, $contact));

        // send to partners
        // send telegram
        // $sendPartner = $partners[$data['partner']];
        // if ($sendPartner['chat_id']) {
        //     $text = view('telegram.admin.installment_payment', compact('contact'))->render();
        //     Helper::toTelegram($text, 'Markdown', $sendPartner['chat_id']);
        // }
        $installmentPaymentChatID = config('services.installment_payment.chat_id');
        if ($installmentPaymentChatID) {
            $text = view('telegram.admin.installment_payment', compact('contact'))->render();
            Helper::toTelegram($text, 'Markdown', $installmentPaymentChatID);
        }

        // send email
        // if ($sendPartner['email']) {
        //     Mail::to($sendPartner['email'])->send(new InstallmentPaymentAdminMail($installmentPayment, $contact));
        // }
        // foreach($partners as $partner) {
        //     // send telegram
        //     if ($partner['chat_id']) {
        //         $text = view('telegram.admin.installment_payment', compact('contact'))->render();
        //         Helper::toTelegram($text, 'Markdown', $partner['chat_id']);
        //     }

        //     // send email
        //     if ($partner['email']) {
        //         Mail::to($partner['email'])->send(new InstallmentPaymentAdminMail($installmentPayment, $contact));
        //     }
        // }

        // return redirect()->route('home', ['#contact-form'])->withSuccess(__('home.contact_message_success'));

        $data = [
            'message' => __('main.form.contact_form_success'),
        ];

        return response()->json($data);
    }

}
