<?php

namespace App\Http\Controllers;

use App\BotConfig;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $url = BotConfig::get('url');
        $token = BotConfig::get('token');
        $currency = BotConfig::get('currency');
        $currencies = json_decode(file_get_contents('https://api.coindesk.com/v1/bpi/supported-currencies.json'));

        return view('settings', compact('url', 'token', 'currency', 'currencies'));
    }

    public function store(Request $request)
    {
        $this->updateUrl($request->url, $request->token);

        BotConfig::set('url', $request->url);
        BotConfig::set('token', $request->token);
        BotConfig::set('currency', $request->currency);

        return back();
    }

    private function updateUrl($url, $token)
    {
        $url = 'https://api.telegram.org/bot'.$token.'/setWebhook?url='.$url;

        $output = json_decode(file_get_contents($url));

        if ($output->ok !== true || $output->result !== true) {
            throw new \Exception('Something went wrong');
        }
    }
}
