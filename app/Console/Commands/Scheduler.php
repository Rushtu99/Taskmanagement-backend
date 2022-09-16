<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
class Scheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scheduler';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run scheduler';
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
        $this->line('Scheduling is running..');
        while (true) {
            if (Carbon::now()->hour === 0) {
                $this->line('Called for Daily..');
                Artisan::call('schedule:run');
                $output = Artisan::output();
                if ($output != 'No scheduled commands are ready to run.') {
                    $this->line($output);
                }
            }
            sleep(3500);
        }
    }
}