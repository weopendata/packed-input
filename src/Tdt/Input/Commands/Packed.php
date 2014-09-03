<?php

namespace Tdt\Input\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Packed extends Command
{

    /**
     * The console command name
     *
     * @var string
     */
    protected $name = 'packed';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = "Start a data load job.";

    /**
     * Execute the console command
     *
     * @return void
     */
    public function fire()
    {
        $this->job;
        // $job_name = $this->argument('jobname');

        // list($collection_uri, $name) = InputController::getParts($job_name);

        // // Check if the job exists
        // $job = \Job::where('name', '=', $name)
        //            ->where('collection_uri', '=', $collection_uri)
        //            ->first();

        // if (empty($job)) {
        //     $this->error("The job with identified by: $job_name could not be found.\n");
        //     exit();
        // }

        // $this->line('The job has been found.');

        // $job_exec = new JobExecuter($job, $this);
        // $job_exec->execute();
    }

    /**
     * Get the console command arguments
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            // array('jobname', InputArgument::REQUIRED, 'Full name of the job that needs to be executed. (the uri that was given to PUT the meta-data for this job).'),
        );
    }

    /**
     * Get the console command options
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(

        );
    }
}
