<?php

namespace Tdt\Input\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Job;

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
        $this->comment("\nPACKED DATALOADER");
        $this->info("\nAvailable jobs:");

        $jobs = Job::orderBy('collection_uri', 'asc')
                   ->orderBy('name', 'asc')
                   ->get();

        $availableJobs = array();

        $i = 1;
        foreach ($jobs as $job) {
            $job = $job->collection_uri . '/' . $job->name;
            array_push($availableJobs, $job);

            $this->line(" • " . $i . ") " . $job);
            $i++;
        }

        $keys = array_keys($availableJobs);

        $number = $this->ask("\nWhat job should I start? (number): ");
        while (!in_array($number - 1, $keys)) {
            $this->error("Sorry, that was not a valid number, try again!");
            $number = $this->ask("What job should I start? (number): ");
        }

        $jobPicked = $availableJobs[$number - 1];
        $this->info("\nStarting job #$number ($jobPicked):");

        $this->call('input:execute', array('jobname' => $jobPicked));

        $this->question("\n\nJob complete! Re-run the command to start a new job.\n");


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
