<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;
use App\Mail\SendMail;
use App\Models\User;
class Daily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:daily';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Respectively send daily notifications';
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
        
        $users = User::all();
        foreach ($users as $user) {

            Mail::send('emails.taskassigned',$user, function ($mail) use ($user) {
                $mail->from('test@123.com');
                $mail->to($user->email)
                    ->subject('Daily New!');
            });
        }
         
        $this->info('Successfully sent daily email to everyone.');
    }
}