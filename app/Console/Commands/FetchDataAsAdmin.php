<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class FetchDataAsAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:data-as-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data as admin for different datasets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();
        $url = "https://driveshield.net/api/admin/";
//        $url = "127.0.0.1:8000/api/admin/";

        $admin_phone = config('app.ADMIN_PHONE');
        $admin_pass = config('app.ADMIN_PASSWORD');
        $token = $this->getToken($url, $admin_phone, $admin_pass);
        $datasets = [ 'invoices', 'receipts'];
        $url .= 'fetch';
        $start_time = microtime(true);

        foreach ($datasets as $dataset) {
            try {
                $response = $client->request('GET', $url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                    'query' => ['data' => $dataset]
                ]);

                $this->info("Fetched data for {$dataset}: " . $response->getBody());
            } catch (\Exception $e) {
                $this->error("Failed to fetch data for {$dataset}: " . $e->getMessage());
            }
        }

        $end_time = microtime(true);

        $total_time = $end_time - $start_time;

        $total_time_ms = round($total_time * 1000);
//        dd($total_time_ms );
        $this->info("Total time taken to fetch data: {$total_time_ms} milliseconds");

    }

    private function getToken($url, $admin_phone, $admin_pass)
    {
        $client = new Client();
        $url .= "login";
        try {
        $response = $client->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                "phone_number" => $admin_phone,
                "password" => $admin_pass,
            ]
        ]);
        $responseData = json_decode($response->getBody(), true);
            return$responseData['data']['token'];
        } catch (\Exception $e) {
            $this->error("Failed to get Token");
        }
    }
}
